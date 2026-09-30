<?php
// Núcleo do checkout AUTONOM/IA: configuração, preço, Asaas, pedidos e utilidades.
// Segredos (chave do Asaas, token do webhook, cupons) ficam em _config-secret.php,
// gerado pelo deploy a partir dos secrets do GitHub. Nunca commitar esse arquivo.
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');
error_reporting(0);
ini_set('display_errors', '0');

// ---- o que muda de turma pra turma ---------------------------------------
const EVENTO = [
    'nome'           => 'AUTONOM/IA Mulheres · Turma 1',
    'descricao'      => 'AUTONOM/IA Mulheres · Turma 1 · 11/11/2026, 8h às 18h30 · Hard Rock Café Curitiba',
    'vagas'          => 50,
    'preco'          => 1297.00,   // por vaga, no cartão
    'desconto_pix'   => 0.05,
    'desconto_dupla' => 0.05,      // por vaga, na compra em dupla
    'max_parcelas'   => 10,
    'vencimento_dias'=> 2,         // prazo da cobrança PIX
    'vendas_ate'     => '2026-11-10 23:59:59',
];

function cfg(): array
{
    static $c = null;
    if ($c === null) {
        $f = __DIR__ . '/_config-secret.php';
        $c = is_file($f) ? (include $f) : [];
        if (!is_array($c)) $c = [];
        $c += [
            'env'            => 'sandbox',        // 'sandbox' ou 'production'
            'asaas_key'      => '',
            'webhook_token'  => '',
            'admin_key'      => '',
            'cupons'         => [],               // 'CODIGO' => ['preco' => 997, 'nome' => 'Comunidade X', 'limite' => 20]
            'grupo_whatsapp' => '',
            'site'           => 'https://autonomia.vc',
            'notificar_url'  => 'https://guizanoni.com/enviar.php',
        ];
    }
    return $c;
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function body_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

function so_digitos(?string $s): string { return preg_replace('/\D+/', '', (string)$s); }
function limpa(?string $s, int $max = 120): string { return mb_substr(trim(strip_tags((string)$s)), 0, $max); }
function brl(float $v): string { return 'R$ ' . number_format($v, 2, ',', '.'); }

function cpf_valido(string $cpf): bool
{
    $cpf = so_digitos($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $s = 0;
        for ($i = 0; $i < $t; $i++) $s += (int)$cpf[$i] * (($t + 1) - $i);
        $d = ((10 * $s) % 11) % 10;
        if ((int)$cpf[$t] !== $d) return false;
    }
    return true;
}

function cnpj_valido(string $cnpj): bool
{
    $c = so_digitos($cnpj);
    if (strlen($c) !== 14 || preg_match('/^(\d)\1{13}$/', $c)) return false;
    $calc = function (string $base): int {
        $pesos = strlen($base) === 12 ? [5,4,3,2,9,8,7,6,5,4,3,2] : [6,5,4,3,2,9,8,7,6,5,4,3,2];
        $s = 0;
        foreach (str_split($base) as $i => $n) $s += (int)$n * $pesos[$i];
        $r = $s % 11;
        return $r < 2 ? 0 : 11 - $r;
    };
    $d1 = $calc(substr($c, 0, 12));
    $d2 = $calc(substr($c, 0, 12) . $d1);
    return $c[12] == $d1 && $c[13] == $d2;
}

function wa_numero(string $n): string
{
    $n = so_digitos($n);
    return strlen($n) <= 11 ? '55' . ltrim($n, '0') : $n;
}

function email_valido(string $e): bool { return (bool)filter_var($e, FILTER_VALIDATE_EMAIL); }

// ---- preço -----------------------------------------------------------------
// Regras: sem cupom, PIX e dupla dão 5% cada, e os descontos NÃO se somam (vale o maior).
// Com cupom de comunidade, o preço do cupom é o preço final da vaga (sem outro desconto),
// a menos que o cupom tenha 'acumula_pix' => true.
function cupom_info(?string $codigo): ?array
{
    $codigo = strtoupper(trim((string)$codigo));
    if ($codigo === '') return null;
    $cupons = array_change_key_case(cfg()['cupons'] ?: [], CASE_UPPER);
    if (!isset($cupons[$codigo])) return null;
    $c = $cupons[$codigo];
    if (!is_array($c) || empty($c['preco'])) return null;
    if (!empty($c['limite']) && cupom_usos($codigo) >= (int)$c['limite']) return ['esgotado' => true] + $c + ['codigo' => $codigo];
    return $c + ['codigo' => $codigo];
}

function calcular(string $tipo, string $forma, ?string $cupom, int $parcelas): array
{
    $vagas = $tipo === 'dupla' ? 2 : 1;
    $forma = $forma === 'pix' ? 'pix' : 'cartao';
    $c = cupom_info($cupom);
    $aviso = null;
    if ($cupom && !$c) $aviso = 'Código não encontrado. Confira com quem te convidou.';
    if ($c && !empty($c['esgotado'])) { $aviso = 'Esse código já atingiu o limite de usos.'; $c = null; }

    if ($c) {
        $unit = (float)$c['preco'];
        if ($forma === 'pix' && !empty($c['acumula_pix'])) $unit *= (1 - EVENTO['desconto_pix']);
        $rotulo = 'Código ' . $c['codigo'] . (isset($c['nome']) ? ' · ' . $c['nome'] : '');
    } else {
        $desc = 0.0; $rotulo = null;
        if ($forma === 'pix') { $desc = EVENTO['desconto_pix']; $rotulo = '5% de desconto no PIX'; }
        if ($vagas === 2 && EVENTO['desconto_dupla'] >= $desc) { $desc = EVENTO['desconto_dupla']; $rotulo = '5% de desconto pra cada uma (dupla)'; }
        $unit = EVENTO['preco'] * (1 - $desc);
    }
    $unit = round($unit, 2);
    $total = round($unit * $vagas, 2);
    $parcelas = $forma === 'pix' ? 1 : max(1, min(EVENTO['max_parcelas'], $parcelas));
    return [
        'vagas'        => $vagas,
        'forma'        => $forma,
        'unitario'     => $unit,
        'total'        => $total,
        'parcelas'     => $parcelas,
        'valor_parcela'=> round($total / $parcelas, 2),
        'max_parcelas' => EVENTO['max_parcelas'],
        'desconto'     => $rotulo,
        'cupom'        => $c['codigo'] ?? null,
        'cupom_ok'     => (bool)$c,
        'aviso'        => $aviso,
    ];
}

// ---- Asaas -----------------------------------------------------------------
function asaas_base(): string
{
    if (!empty(cfg()['asaas_url'])) return cfg()['asaas_url'];   // só pra teste local com API simulada
    return cfg()['env'] === 'production' ? 'https://api.asaas.com/v3' : 'https://api-sandbox.asaas.com/v3';
}

function asaas(string $method, string $path, ?array $body = null): array
{
    $ch = curl_init(asaas_base() . $path);
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: autonomia.vc-checkout/1.0',
        'access_token: ' . cfg()['asaas_key'],
    ];
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    $json = json_decode((string)$raw, true);
    return ['code' => $code, 'data' => is_array($json) ? $json : [], 'erro_rede' => $err ?: null];
}

function asaas_erro(array $r): string
{
    $d = $r['data']['errors'][0]['description'] ?? null;
    if ($d) return $d;
    if ($r['erro_rede']) return 'Falha de conexão com o sistema de pagamento.';
    return 'O sistema de pagamento recusou o pedido (código ' . $r['code'] . ').';
}

// ---- armazenamento (arquivos JSON em _data/, bloqueado pro público) -------
function data_dir(string $sub = ''): string
{
    $d = __DIR__ . '/_data' . ($sub ? '/' . $sub : '');
    if (!is_dir($d)) @mkdir($d, 0750, true);
    $ht = __DIR__ . '/_data/.htaccess';
    if (!is_file($ht)) @file_put_contents($ht, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    return $d;
}

function pedido_path(string $id): string { return data_dir('pedidos') . '/' . preg_replace('/[^a-f0-9]/', '', $id) . '.json'; }

function pedido_ler(string $id): ?array
{
    $f = pedido_path($id);
    if (!is_file($f)) return null;
    $d = json_decode((string)file_get_contents($f), true);
    return is_array($d) ? $d : null;
}

function pedido_salvar(array $p): void
{
    $p['atualizado_em'] = date('c');
    file_put_contents(pedido_path($p['id']), json_encode($p, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function pedidos_todos(): array
{
    $out = [];
    foreach (glob(data_dir('pedidos') . '/*.json') ?: [] as $f) {
        $d = json_decode((string)file_get_contents($f), true);
        if (is_array($d)) $out[] = $d;
    }
    usort($out, fn($a, $b) => strcmp($b['criado_em'] ?? '', $a['criado_em'] ?? ''));
    return $out;
}

function vagas_vendidas(): int
{
    $n = 0;
    foreach (pedidos_todos() as $p) if (($p['status'] ?? '') === 'pago') $n += (int)($p['vagas'] ?? 1);
    return $n;
}

function cupom_usos(string $codigo): int
{
    $n = 0;
    foreach (pedidos_todos() as $p) if (($p['cupom'] ?? '') === $codigo && ($p['status'] ?? '') === 'pago') $n += (int)($p['vagas'] ?? 1);
    return $n;
}

function situacao_vagas(): array
{
    $vendidas = vagas_vendidas();
    $restantes = max(0, EVENTO['vagas'] - $vendidas);
    $encerrado = time() > strtotime(EVENTO['vendas_ate']);
    return ['vagas' => EVENTO['vagas'], 'vendidas' => $vendidas, 'restantes' => $restantes, 'esgotado' => $restantes <= 0, 'encerrado' => $encerrado];
}

// ---- proteção simples contra abuso -----------------------------------------
function limitar(string $chave, int $max, int $janela): bool
{
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'x';
    $f = data_dir('limites') . '/' . md5($chave . $ip) . '.json';
    $agora = time();
    $h = is_file($f) ? (json_decode((string)file_get_contents($f), true) ?: []) : [];
    $h = array_values(array_filter($h, fn($t) => $t > $agora - $janela));
    if (count($h) >= $max) return false;
    $h[] = $agora;
    file_put_contents($f, json_encode($h), LOCK_EX);
    return true;
}

// ---- aviso pra equipe (usa o enviar.php do guizanoni.com, que já tem SMTP) --
function notificar_equipe(array $p, string $titulo): void
{
    $url = cfg()['notificar_url'];
    if (!$url) return;
    $linhas = [
        $titulo,
        'Pedido: ' . $p['id'],
        'Tipo: ' . ($p['vagas'] == 2 ? 'Dupla (2 vagas)' : 'Individual'),
        'Pagamento: ' . ($p['forma'] === 'pix' ? 'PIX' : 'Cartão em ' . $p['parcelas'] . 'x') . ' · ' . brl((float)$p['total']),
        $p['cupom'] ? 'Código: ' . $p['cupom'] : null,
        'Compradora: ' . $p['p1']['nome'] . ' · ' . $p['p1']['email'] . ' · ' . $p['p1']['whatsapp'],
        $p['p2'] ? 'Amiga: ' . $p['p2']['nome'] . ' · ' . $p['p2']['email'] . ' · ' . $p['p2']['whatsapp'] : null,
        $p['nota_cnpj'] ? 'Nota no CNPJ: ' . $p['nota_cnpj'] . ' · ' . $p['nota_razao'] : null,
        'Vagas vendidas: ' . vagas_vendidas() . ' de ' . EVENTO['vagas'],
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_POSTFIELDS => [
            'nome'     => $p['p1']['nome'],
            'email'    => $p['p1']['email'],
            'whatsapp' => $p['p1']['whatsapp'],
            'produto'  => 'AUTONOM/IA Mulheres · ' . $titulo,
            'mensagem' => implode("\n", array_filter($linhas)),
        ],
    ]);
    curl_exec($ch);
    curl_close($ch);
}
