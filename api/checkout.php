<?php
// Cria o pedido: valida os dados, calcula o preço no servidor, cria (ou reaproveita) a cliente
// no Asaas, gera a cobrança PIX ou cartão e devolve o link da página de pagamento do Asaas.
require __DIR__ . '/lib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false], 405);
if (!limitar('checkout', 8, 600)) json_out(['ok' => false, 'erro' => 'Muitas tentativas seguidas. Espere alguns minutos ou chame a gente no WhatsApp.'], 429);
if (!cfg()['asaas_key']) json_out(['ok' => false, 'erro' => 'O pagamento ainda não está ativo. Chame a gente no WhatsApp pra garantir sua vaga.'], 503);

$b = body_json();
if (!empty($b['website'])) json_out(['ok' => false, 'erro' => 'Não foi possível concluir.'], 400); // robô

$sit = situacao_vagas();
if ($sit['encerrado']) json_out(['ok' => false, 'erro' => 'As vendas da Turma 1 foram encerradas.', 'esgotado' => true], 409);

$tipo  = ($b['tipo'] ?? '') === 'dupla' ? 'dupla' : 'individual';
$forma = ($b['forma'] ?? '') === 'cartao' ? 'cartao' : 'pix';
$calc  = calcular($tipo, $forma, (string)($b['cupom'] ?? ''), (int)($b['parcelas'] ?? 1));
if ($calc['vagas'] > $sit['restantes']) {
    json_out(['ok' => false, 'esgotado' => $sit['restantes'] <= 0,
        'erro' => $sit['restantes'] <= 0 ? 'As 50 vagas da Turma 1 esgotaram.' : 'Só resta ' . $sit['restantes'] . ' vaga. Faça a compra individual.'], 409);
}
if (($b['cupom'] ?? '') !== '' && !$calc['cupom_ok']) json_out(['ok' => false, 'erro' => $calc['aviso'] ?: 'Código inválido.'], 422);

// ---- dados ----
$pessoa = function ($d, bool $exigeCpf) {
    $p = [
        'nome'     => limpa($d['nome'] ?? '', 100),
        'email'    => strtolower(limpa($d['email'] ?? '', 120)),
        'whatsapp' => so_digitos($d['whatsapp'] ?? ''),
        'cpf'      => so_digitos($d['cpf'] ?? ''),
    ];
    $erros = [];
    if (mb_strlen($p['nome']) < 5 || strpos($p['nome'], ' ') === false) $erros[] = 'nome completo';
    if (!email_valido($p['email'])) $erros[] = 'e-mail';
    if (strlen($p['whatsapp']) < 10 || strlen($p['whatsapp']) > 13) $erros[] = 'WhatsApp com DDD';
    if ($exigeCpf && !cpf_valido($p['cpf'])) $erros[] = 'CPF';
    return [$p, $erros];
};
[$p1, $e1] = $pessoa($b['p1'] ?? [], true);
$p2 = null; $e2 = [];
if ($tipo === 'dupla') {
    [$p2, $e2] = $pessoa($b['p2'] ?? [], false);
    if (!$e2 && $p2['email'] === $p1['email']) $e2[] = 'e-mail diferente do seu';
}
$cnpj = so_digitos($b['cnpj'] ?? '');
$razao = limpa($b['razao'] ?? '', 120);
$e3 = [];
if ($cnpj !== '') {
    if (!cnpj_valido($cnpj)) $e3[] = 'CNPJ';
    if (mb_strlen($razao) < 3) $e3[] = 'razão social';
}
if ($e1 || $e2 || $e3) {
    $msg = [];
    if ($e1) $msg[] = 'Seus dados: confira ' . implode(', ', $e1) . '.';
    if ($e2) $msg[] = 'Dados da amiga: confira ' . implode(', ', $e2) . '.';
    if ($e3) $msg[] = 'Nota fiscal: confira ' . implode(', ', $e3) . '.';
    json_out(['ok' => false, 'erro' => implode(' ', $msg)], 422);
}
if (empty($b['aceite'])) json_out(['ok' => false, 'erro' => 'Marque que você leu as condições pra continuar.'], 422);

// ---- cliente no Asaas: SEMPRE uma nova, marcada como AUTONOM/IA ----
// A conta Asaas é compartilhada com o pipo.guru, cujo webhook procura o dono do pagamento pela
// cliente. Reaproveitar uma cliente do pipo faria ele tratar esta compra como renovação de plano.
$doc = $cnpj ?: $p1['cpf'];
$customerId = null;
{
    $r = asaas('POST', '/customers', array_filter([
        'name'        => $cnpj ? $razao : $p1['nome'],
        'cpfCnpj'     => $doc,
        'email'       => $p1['email'],
        'mobilePhone' => $p1['whatsapp'],
        'observations'=> 'AUTONOM/IA Mulheres' . ($cnpj ? ' · contato: ' . $p1['nome'] : ''),
        'externalReference' => 'autonomia',
    ], fn($v) => $v !== null));
    $customerId = $r['data']['id'] ?? null;
    if (!$customerId) json_out(['ok' => false, 'erro' => asaas_erro($r)], 502);
}

// ---- cobrança ----
$id = bin2hex(random_bytes(8));
$desc = EVENTO['descricao'] . ' · ' . ($calc['vagas'] === 2 ? '2 vagas (dupla): ' . $p1['nome'] . ' e ' . $p2['nome'] : '1 vaga: ' . $p1['nome']);
$pay = [
    'customer'          => $customerId,
    'billingType'       => $forma === 'pix' ? 'PIX' : 'CREDIT_CARD',
    'dueDate'           => date('Y-m-d', strtotime('+' . EVENTO['vencimento_dias'] . ' days')),
    'description'       => mb_substr($desc, 0, 480),
    'externalReference' => $id,
    'callback'          => ['successUrl' => (cfg()['retorno_url'] ?: rtrim(cfg()['site'], '/') . '/obrigado/') . '?pedido=' . $id, 'autoRedirect' => true],
];
if ($forma === 'cartao' && $calc['parcelas'] > 1) {
    $pay['installmentCount'] = $calc['parcelas'];
    $pay['totalValue'] = $calc['total'];
} else {
    $pay['value'] = $calc['total'];
}
$r = asaas('POST', '/payments', $pay);
$url = $r['data']['invoiceUrl'] ?? null;
if (!$url && $r['code'] === 400) {
    // se o Asaas recusar o endereço de retorno (domínio), cria a cobrança sem ele: a venda não pode travar por isso
    unset($pay['callback']);
    $r = asaas('POST', '/payments', $pay);
    $url = $r['data']['invoiceUrl'] ?? null;
}
if (!$url) json_out(['ok' => false, 'erro' => asaas_erro($r)], 502);

pedido_salvar([
    'id'           => $id,
    'status'       => 'aguardando',
    'criado_em'    => date('c'),
    'tipo'         => $tipo,
    'vagas'        => $calc['vagas'],
    'forma'        => $forma,
    'parcelas'     => $calc['parcelas'],
    'total'        => $calc['total'],
    'cupom'        => $calc['cupom'],
    'p1'           => $p1,
    'p2'           => $p2,
    'nota_cnpj'    => $cnpj ?: null,
    'nota_razao'   => $cnpj ? $razao : null,
    'asaas_cliente'=> $customerId,
    'asaas_cobranca'=> $r['data']['id'] ?? null,
    'asaas_parcelamento' => $r['data']['installment'] ?? null,
    'invoice_url'  => $url,
    'ambiente'     => cfg()['env'],
]);

json_out(['ok' => true, 'url' => $url, 'pedido' => $id, 'total' => $calc['total']]);
