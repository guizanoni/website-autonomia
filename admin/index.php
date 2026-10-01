<?php
// Painel financeiro da AUTONOM/IA, integrado ao Asaas. Login: usuário + hash bcrypt nos secrets do GitHub.
require __DIR__ . '/../api/lib.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
session_name('AUTONOMIAADM');
session_set_cookie_params(['lifetime' => 0, 'path' => '/admin/', 'secure' => !empty($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Strict']);
session_start();

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$csrf = $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$logado = !empty($_SESSION['ok']) && ($_SESSION['ate'] ?? 0) > time();
$aviso = $erro = null;

// ---------- login / logout ----------
if (($_POST['acao'] ?? '') === 'login') {
    $u = (string)($_POST['usuario'] ?? ''); $s = (string)($_POST['senha'] ?? '');
    $users = admin_usuarios();
    $conta = $users[strtolower(trim($u))] ?? null;
    if (!limitar('login', 8, 900)) $erro = 'Muitas tentativas. Espere 15 minutos.';
    elseif ($conta && !empty($conta['hash']) && password_verify($s, (string)$conta['hash'])) {
        session_regenerate_id(true);
        $_SESSION['ok'] = 1; $_SESSION['ate'] = time() + 8 * 3600; $_SESSION['csrf'] = bin2hex(random_bytes(16));
        $_SESSION['user'] = strtolower(trim($u)); $_SESSION['papel'] = ($conta['papel'] ?? '') === 'admin' ? 'admin' : 'leitura';
        header('Location: /admin/'); exit;
    } else $erro = 'Usuário ou senha incorretos.';
}
if ($logado && ($_POST['acao'] ?? '') === 'sair' && hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $_SESSION = []; session_destroy(); header('Location: /admin/'); exit;
}

if (!$logado): ?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<link rel="icon" href="/brand/favicon.svg" type="image/svg+xml"/>
<link rel="icon" href="/favicon.ico" sizes="32x32"/>
<link rel="apple-touch-icon" href="/brand/apple-touch-icon.png"/>
<meta name="theme-color" content="#111111"/>
<title>Entrar · AUTONOM/IA</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&display=swap">
<style>
:root{--ink:#191c1f;--mut:#6b6e73;--line:#e3e3df;--soft:#f4f4f2;--sig:#ff5b2e}
*{box-sizing:border-box;margin:0}body{min-height:100vh;display:grid;place-items:center;background:var(--soft);font:16px/1.5 "Geist",system-ui,sans-serif;color:var(--ink);padding:16px}
form{background:#fff;border-radius:24px;padding:32px;width:100%;max-width:380px;display:grid;gap:10px}
.logo{font-weight:600;font-size:22px;letter-spacing:-.04em;margin-bottom:6px}.logo i{font-style:normal;color:var(--sig)}
.tag{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;background:var(--ink);color:#fff;padding:3px 8px;border-radius:999px;vertical-align:4px;margin-left:4px}
h1{font-size:24px;letter-spacing:-.03em;margin-bottom:8px}
label{font-size:14px;font-weight:500}input{font:inherit;padding:13px 14px;border:1.5px solid var(--line);border-radius:14px}
input:focus{outline:none;border-color:var(--ink)}
button{font:inherit;font-weight:500;padding:14px;border:0;border-radius:999px;background:var(--ink);color:#fff;cursor:pointer;margin-top:8px}
.err{color:#b42318;font-size:14px}
</style></head><body>
<form method="post" autocomplete="on">
  <div class="logo">autonom<i>.</i>ia<i>/</i>mulheres <span class="tag">admin</span></div>
  <h1>Painel financeiro</h1>
  <?php if ($erro): ?><p class="err"><?= $h($erro) ?></p><?php endif; ?>
  <input type="hidden" name="acao" value="login">
  <label for="u">Usuário</label><input id="u" name="usuario" autocomplete="username" required autofocus>
  <label for="s">Senha</label><input id="s" name="senha" type="password" autocomplete="current-password" required>
  <button type="submit">Entrar</button>
</form></body></html>
<?php exit; endif;

// ---------- ações (POST com CSRF) ----------
$acao = $_POST['acao'] ?? '';
$user = $_SESSION['user'] ?? 'gui';
$isAdmin = ($_SESSION['papel'] ?? 'admin') === 'admin';
if ($acao && !hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { $erro = 'Sessão expirada. Recarregue a página.'; $acao = ''; }
if ($acao === 'sacar' && empty(cfg()['saque_habilitado'])) { $erro = 'O saque por aqui está desligado. Use o painel financeiro da empresa.'; $acao = ''; }
if (in_array($acao, ['estornar', 'sacar'], true) && !$isAdmin) { $erro = 'Seu usuário é só de visualização: saque e estorno ficam com o administrador.'; $acao = ''; }
if ($acao === 'sacar') {
    // aceita "1.234,56", "1234,56" e "1234.56"; com vírgula, ponto é milhar; sem vírgula, ponto é decimal
    $v = preg_replace('/[^0-9.,]/', '', (string)($_POST['valor'] ?? '0'));
    $v = str_contains($v, ',') ? str_replace(',', '.', str_replace('.', '', $v)) : $v;
    $centavos = substr_count($v, '.') > 1 ? 0 : (int)round(((float)$v) * 100);
    $conta = admin_usuarios()[$user] ?? null;
    $rb0 = asaas('GET', '/finance/balance');
    $disp = (int)round(((float)($rb0['data']['balance'] ?? 0)) * 100);
    if (!$conta || !password_verify((string)($_POST['senha'] ?? ''), (string)$conta['hash'])) $erro = 'Senha incorreta. O saque não foi feito.';
    elseif (!limitar('saque', 5, 3600)) $erro = 'Muitos saques seguidos. Espere uma hora.';
    elseif ($centavos < 100) $erro = 'Valor mínimo de saque: R$ 1,00.';
    elseif ($centavos > $disp) $erro = 'Valor maior que o saldo disponível (' . brl($disp / 100) . ').';
    else {
        $r = saque_criar($centavos);
        if ($r['ok']) $aviso = 'Saque de ' . brl($centavos / 100) . ' solicitado. O Pix sai em instantes para a chave cadastrada.';
        else $erro = 'O saque não foi aceito: ' . $r['erro'];
    }
}
if ($acao === 'sincronizar') {
    $n = 0;
    foreach (pedidos_todos() as $p) { if (in_array($p['status'], ['aguardando', 'pago', 'vencido'], true)) { sincronizar_pedido($p); $n++; } }
    $aviso = "Pagamentos atualizados: $n pedido(s) conferido(s).";
}
if ($acao === 'estornar') {
    $p = pedido_ler((string)($_POST['pedido'] ?? ''));
    if (!$p || $p['status'] !== 'pago') $erro = 'Esse pedido não está pago.';
    else {
        $r = $p['asaas_parcelamento'] ? asaas('POST', '/installments/' . $p['asaas_parcelamento'] . '/refund') : asaas('POST', '/payments/' . $p['asaas_cobranca'] . '/refund');
        if ($r['code'] === 200) { $p['estorno_pedido_em'] = date('c'); pedido_salvar($p); sincronizar_pedido($p); $aviso = 'Estorno solicitado para ' . $p['p1']['nome'] . '.'; }
        else $erro = 'O estorno não foi aceito: ' . asaas_erro($r);
    }
}
if (($_GET['csv'] ?? '') === '1') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="autonomia-pedidos-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['pedido','status','criado_em','pago_em','vagas','forma','parcelas','total','liquido_asaas','cupom','nome','email','whatsapp','cpf','amiga_nome','amiga_email','amiga_whatsapp','nota_cnpj','nota_razao'], ';');
    foreach (pedidos_todos() as $p) {
        $liq = array_sum(array_map(fn($x) => in_array($x['status'], PAGO_STATUS, true) ? $x['liquido'] : 0, $p['pagamentos'] ?? []));
        fputcsv($out, [$p['id'],$p['status'],$p['criado_em'],$p['pago_em'] ?? '',$p['vagas'],$p['forma'],$p['parcelas'],number_format((float)$p['total'],2,',',''),number_format($liq,2,',',''),$p['cupom'] ?? '',
            $p['p1']['nome'],$p['p1']['email'],$p['p1']['whatsapp'],$p['p1']['cpf'],$p['p2']['nome'] ?? '',$p['p2']['email'] ?? '',$p['p2']['whatsapp'] ?? '',$p['nota_cnpj'] ?? '',$p['nota_razao'] ?? ''], ';');
    }
    exit;
}

// ---------- dados ----------
$pedidos = pedidos_todos();
$fin = financeiro($pedidos);
$sit = situacao_vagas();
$saldo = null;
$rb = asaas('GET', '/finance/balance');
if ($rb['code'] === 200) $saldo = (float)($rb['data']['balance'] ?? 0);
$transfers = []; $extrato = [];
$rt = asaas('GET', '/transfers?limit=10');
if ($rt['code'] === 200) $transfers = $rt['data']['data'] ?? [];
$re = asaas('GET', '/financialTransactions?limit=15');
if ($re['code'] === 200) $extrato = $re['data']['data'] ?? [];
$destino = saque_destino();
$stTr = ['PENDING' => 'pendente', 'BANK_PROCESSING' => 'no banco', 'DONE' => 'concluído', 'CANCELLED' => 'cancelado', 'FAILED' => 'falhou', 'AWAITING_AUTHORIZATION' => 'aguardando autorização'];
$filtro = $_GET['status'] ?? 'todos';
$lista = array_values(array_filter($pedidos, fn($p) => $filtro === 'todos' || $p['status'] === $filtro));
$conv = $fin['criados'] ? round(100 * $fin['pagos'] / $fin['criados']) : 0;
$ticket = $fin['pagos'] ? $fin['bruto'] / $fin['pagos'] : 0;
$cor = ['pago' => ['#127a3a', '#e7f4ec'], 'aguardando' => ['#9a6700', '#fff4d6'], 'vencido' => ['#6b6e73', '#efefec'], 'estornado' => ['#b42318', '#fdecea'], 'cancelado' => ['#6b6e73', '#efefec']];

// vendas por dia: últimos 30 dias
$dias = [];
for ($i = 29; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-$i days")); $dias[$d] = $fin['dias'][$d] ?? 0; }
$max = max(1, max($dias));
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<link rel="icon" href="/brand/favicon.svg" type="image/svg+xml"/>
<link rel="icon" href="/favicon.ico" sizes="32x32"/>
<link rel="apple-touch-icon" href="/brand/apple-touch-icon.png"/>
<meta name="theme-color" content="#111111"/>
<title>Financeiro · AUTONOM/IA</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap">
<style>
:root{--ink:#191c1f;--mut:#6b6e73;--line:#e3e3df;--soft:#f4f4f2;--sig:#ff5b2e;--ok:#127a3a}
*{box-sizing:border-box;margin:0;padding:0}
body{background:var(--soft);color:var(--ink);font:15px/1.5 "Geist",system-ui,sans-serif;-webkit-font-smoothing:antialiased}
.wrap{max-width:1240px;margin:0 auto;padding:20px clamp(16px,3vw,32px) 60px}
header{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:22px}
.logo{font-weight:600;font-size:24px;letter-spacing:-.04em}.logo i{font-style:normal;color:var(--sig)}
.tag{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;background:var(--ink);color:#fff;padding:3px 8px;border-radius:999px;vertical-align:4px;margin-left:4px}
.top{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.btn{font:inherit;font-size:14px;font-weight:500;padding:10px 16px;border-radius:999px;border:1.5px solid var(--line);background:#fff;color:var(--ink);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.btn.dark{background:var(--ink);color:#fff;border-color:var(--ink)}
.btn.danger{border-color:#f3c3bd;color:#b42318}
.msg{padding:12px 16px;border-radius:14px;margin-bottom:16px;font-size:14.5px}.msg.ok{background:#e7f4ec;color:var(--ok)}.msg.err{background:#fdecea;color:#b42318}
h1{font-size:28px;letter-spacing:-.035em;font-weight:600}
.sub{color:var(--mut);font-size:14px}
.kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0}
.k{background:#fff;border-radius:20px;padding:18px 20px;min-width:0}
.k span{display:block;font-size:13px;color:var(--mut)}
.k b{display:block;font-size:clamp(22px,2.4vw,30px);font-weight:600;letter-spacing:-.035em;margin-top:4px;font-variant-numeric:tabular-nums;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.k small{display:block;color:var(--mut);font-size:12.5px;margin-top:2px}
.k.hl{background:var(--ink);color:#fff}.k.hl span,.k.hl small{color:#a8abb0}
.bar{height:8px;border-radius:8px;background:var(--soft);margin-top:10px;overflow:hidden}.bar i{display:block;height:100%;background:var(--sig);border-radius:8px}
.grid2{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(0,1fr);gap:12px;margin-bottom:12px}
.card{background:#fff;border-radius:20px;padding:20px;min-width:0}
.card h2{font-size:16px;font-weight:600;margin-bottom:12px;letter-spacing:-.01em}
.chart{display:flex;align-items:flex-end;gap:3px;height:150px;border-bottom:1px solid var(--line);padding-top:8px}
.chart div{flex:1;background:var(--sig);border-radius:4px 4px 0 0;min-height:2px;position:relative;opacity:.9}
.chart div.z{background:var(--line)}
.chart div:hover{opacity:1}
.chart div[title]:hover::after{content:attr(title);position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:var(--ink);color:#fff;font-size:12px;padding:4px 8px;border-radius:6px;white-space:nowrap;margin-bottom:4px;z-index:2}
.axis{display:flex;justify-content:space-between;font-size:11.5px;color:var(--mut);margin-top:6px;font-family:"Geist Mono",monospace}
.rows div{display:flex;justify-content:space-between;gap:10px;padding:9px 0;border-bottom:1px solid var(--soft);font-size:14.5px}
.rows div:last-child{border:0}.rows b{font-weight:600;font-variant-numeric:tabular-nums}
.filters{display:flex;flex-wrap:wrap;gap:6px;margin:6px 0 12px}
.filters a{font-size:13.5px;padding:7px 12px;border-radius:999px;background:#fff;color:var(--mut);text-decoration:none;border:1px solid var(--line)}
.filters a.on{background:var(--ink);color:#fff;border-color:var(--ink)}
.tbl{overflow-x:auto;background:#fff;border-radius:20px}
table{width:100%;border-collapse:collapse;font-size:14px;min-width:900px}
th{text-align:left;font-weight:500;color:var(--mut);font-size:12.5px;padding:12px 14px;border-bottom:1px solid var(--line);white-space:nowrap}
td{padding:12px 14px;border-bottom:1px solid var(--soft);vertical-align:top}
tr:last-child td{border:0}
td small{display:block;color:var(--mut);font-size:12.5px}
.st{display:inline-block;font-size:12.5px;font-weight:600;padding:3px 10px;border-radius:999px;white-space:nowrap}
.num{font-variant-numeric:tabular-nums;white-space:nowrap}
td a{color:var(--ink)}
.acts{display:flex;gap:6px;flex-wrap:wrap}
.acts .btn{padding:6px 10px;font-size:12.5px}
.note{font-size:12.5px;color:var(--mut);margin-top:10px}
.btn.sig{background:var(--sig);border-color:var(--sig);color:#fff}
dialog{border:0;border-radius:24px;padding:26px;width:min(440px,92vw);box-shadow:0 30px 80px rgba(0,0,0,.3)}
dialog::backdrop{background:rgba(14,15,17,.5);backdrop-filter:blur(4px)}
dialog h2{font-size:22px;letter-spacing:-.03em;margin-bottom:6px}
dialog form{display:grid;gap:8px}
dialog label{font-size:14px;font-weight:500;margin-top:6px}
dialog input{font:inherit;padding:12px 14px;border:1.5px solid var(--line);border-radius:14px}
@media(max-width:900px){.kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.grid2{grid-template-columns:1fr}}
</style></head><body><div class="wrap">

<header>
  <div><div class="logo">autonom<i>.</i>ia<i>/</i>mulheres <span class="tag">admin</span></div><p class="sub"><?= $h(EVENTO['nome']) ?><?= cfg()['env'] === 'production' ? '' : ' · modo de teste' ?></p></div>
  <div class="top">
    <form method="post"><input type="hidden" name="csrf" value="<?= $h($csrf) ?>"><input type="hidden" name="acao" value="sincronizar"><button class="btn">↻ Atualizar pagamentos</button></form>
    <a class="btn" href="?csv=1">Baixar planilha</a>
    <?php if ($isAdmin): ?><button class="btn sig" type="button" onclick="document.getElementById('saque').showModal()">Sacar</button><?php endif; ?>
    <span class="sub"><?= $h($user) ?> · <?= $isAdmin ? 'administrador' : 'visualização' ?></span>
    <form method="post"><input type="hidden" name="csrf" value="<?= $h($csrf) ?>"><input type="hidden" name="acao" value="sair"><button class="btn dark">Sair</button></form>
  </div>
</header>

<?php if ($aviso): ?><div class="msg ok"><?= $h($aviso) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="msg err"><?= $h($erro) ?></div><?php endif; ?>

<div class="kpis">
  <div class="k hl"><span>Vagas vendidas</span><b><?= $sit['vendidas'] ?> de <?= $sit['vagas'] ?></b><small><?= $sit['restantes'] ?> restantes</small><div class="bar" style="background:#2b2f33"><i style="width:<?= min(100, round(100 * $sit['vendidas'] / max(1, $sit['vagas']))) ?>%"></i></div></div>
  <div class="k"><span>Faturamento bruto</span><b><?= $h(brl($fin['bruto'])) ?></b><small><?= $fin['pagos'] ?> pedido(s) pago(s)</small></div>
  <div class="k"><span>Líquido (depois das taxas)</span><b><?= $h(brl($fin['liquido'])) ?></b><small><?= $fin['liquido_conhecido'] ? 'já descontadas as taxas de pagamento' : 'clique em Atualizar pagamentos' ?></small></div>
  <div class="k"><span>Saldo em conta</span><b><?= $saldo === null ? '—' : $h(brl($saldo)) ?></b><small>conta da empresa (inclui outros produtos)</small></div>
  <div class="k"><span>A receber (cartão)</span><b><?= $h(brl($fin['a_receber'])) ?></b><small>aprovado, ainda não caiu</small></div>
  <div class="k"><span>Aguardando pagamento</span><b><?= $fin['aguardando'] ?></b><small><?= $h(brl($fin['aguardando_valor'])) ?> em aberto</small></div>
  <div class="k"><span>Conversão</span><b><?= $conv ?>%</b><small>pedidos criados que viraram pagos</small></div>
  <div class="k"><span>Ticket médio</span><b><?= $h(brl($ticket)) ?></b><small><?= $fin['estornados'] ?> estorno(s)</small></div>
</div>

<div class="grid2">
  <div class="card">
    <h2>Vendas por dia (últimos 30 dias)</h2>
    <div class="chart"><?php foreach ($dias as $d => $v): ?><div class="<?= $v ? '' : 'z' ?>" style="height:<?= $v ? max(4, round(100 * $v / $max)) : 1 ?>%" title="<?= $h(date('d/m', strtotime($d)) . ' · ' . brl($v)) ?>"></div><?php endforeach; ?></div>
    <div class="axis"><span><?= date('d/m', strtotime('-29 days')) ?></span><span><?= date('d/m', strtotime('-15 days')) ?></span><span>hoje</span></div>
  </div>
  <div class="card">
    <h2>De onde vem o dinheiro</h2>
    <div class="rows">
      <div><span>PIX</span><b><?= $h(brl($fin['pix'])) ?></b></div>
      <div><span>Cartão</span><b><?= $h(brl($fin['cartao'])) ?></b></div>
      <?php foreach ($fin['cupons'] as $c => $v): ?><div><span><?= $h($c) ?> · <?= (int)$v['vagas'] ?> vaga(s)</span><b><?= $h(brl($v['valor'])) ?></b></div><?php endforeach; ?>
    </div>
  </div>
</div>

<div class="filters">
<?php foreach (['todos' => 'Todos', 'pago' => 'Pagos', 'aguardando' => 'Aguardando', 'vencido' => 'Vencidos', 'estornado' => 'Estornados', 'cancelado' => 'Cancelados'] as $k => $l): ?>
  <a class="<?= $filtro === $k ? 'on' : '' ?>" href="?status=<?= $k ?>"><?= $l ?></a>
<?php endforeach; ?>
</div>

<div class="tbl"><table>
<tr><th>Status</th><th>Quando</th><th>Compradora</th><th>Amiga</th><th>Pagamento</th><th>Líquido</th><th>Código</th><th>Nota</th><th></th></tr>
<?php if (!$lista): ?><tr><td colspan="9" class="sub">Nenhum pedido aqui ainda.</td></tr><?php endif; ?>
<?php foreach ($lista as $p): [$c1, $c2] = $cor[$p['status']] ?? ['#191c1f', '#eee'];
  $liq = array_sum(array_map(fn($x) => in_array($x['status'], PAGO_STATUS, true) ? $x['liquido'] : 0, $p['pagamentos'] ?? [])); ?>
<tr>
  <td><span class="st" style="color:<?= $c1 ?>;background:<?= $c2 ?>"><?= $h($p['status']) ?></span></td>
  <td class="num"><?= $h(date('d/m H:i', strtotime($p['criado_em']))) ?><?= !empty($p['pago_em']) ? '<small>pago ' . $h(date('d/m H:i', strtotime($p['pago_em']))) . '</small>' : '' ?></td>
  <td><?= $h($p['p1']['nome']) ?><small><?= $h($p['p1']['email']) ?></small><small><a href="https://wa.me/<?= $h(wa_numero($p['p1']['whatsapp'])) ?>" target="_blank" rel="noopener">WhatsApp <?= $h($p['p1']['whatsapp']) ?></a></small></td>
  <td><?php if (!empty($p['p2'])): ?><?= $h($p['p2']['nome']) ?><small><?= $h($p['p2']['email']) ?></small><small><a href="https://wa.me/<?= $h(wa_numero($p['p2']['whatsapp'])) ?>" target="_blank" rel="noopener">WhatsApp <?= $h($p['p2']['whatsapp']) ?></a></small><?php else: ?><span class="sub">—</span><?php endif; ?></td>
  <td class="num"><?= $h(brl((float)$p['total'])) ?><small><?= $p['forma'] === 'pix' ? 'PIX' : 'Cartão ' . (int)$p['parcelas'] . 'x' ?> · <?= (int)$p['vagas'] ?> vaga(s)</small></td>
  <td class="num"><?= $liq ? $h(brl($liq)) : '<span class="sub">—</span>' ?></td>
  <td><?= $h($p['cupom'] ?? '') ?></td>
  <td><?= !empty($p['nota_cnpj']) ? $h($p['nota_cnpj']) . '<small>' . $h($p['nota_razao']) . '</small>' : '<span class="sub">CPF</span>' ?></td>
  <td><div class="acts">
    <?php if (!empty($p['invoice_url'])): ?><a class="btn" href="<?= $h($p['invoice_url']) ?>" target="_blank" rel="noopener">Fatura</a><?php endif; ?>
    <?php if ($p['status'] === 'pago' && $isAdmin): ?>
    <form method="post" onsubmit="return confirm('Estornar <?= $h(brl((float)$p['total'])) ?> para <?= $h(addslashes($p['p1']['nome'])) ?>? O dinheiro volta pra cliente e a vaga é liberada.')">
      <input type="hidden" name="csrf" value="<?= $h($csrf) ?>"><input type="hidden" name="acao" value="estornar"><input type="hidden" name="pedido" value="<?= $h($p['id']) ?>">
      <button class="btn danger">Estornar</button></form>
    <?php endif; ?>
  </div></td>
</tr>
<?php endforeach; ?>
</table></div>
<div class="grid2" style="margin-top:12px">
  <div class="card">
    <h2>Extrato da conta</h2>
    <div class="rows">
    <?php if (!$extrato): ?><div><span class="sub">Sem movimentações por enquanto.</span></div><?php endif; ?>
    <?php foreach ($extrato as $t): $v = (float)($t['value'] ?? 0); ?>
      <div><span><?= $h(date('d/m', strtotime($t['date'] ?? 'now'))) ?> · <?= $h(mb_strimwidth((string)($t['description'] ?? $t['type'] ?? ''), 0, 60, '…')) ?></span><b style="color:<?= $v < 0 ? '#b42318' : 'var(--ok)' ?>"><?= $h(($v < 0 ? '− ' : '+ ') . brl(abs($v))) ?></b></div>
    <?php endforeach; ?>
    </div>
    <p class="note">Extrato da conta da empresa (inclui outros produtos).</p>
  </div>
  <div class="card">
    <h2>Saques</h2>
    <p class="sub" style="margin-bottom:8px">Destino fixo: <?= $destino ? $h(saque_mascara($destino)) : 'não configurado' ?></p>
    <div class="rows">
    <?php if (!$transfers): ?><div><span class="sub">Nenhum saque ainda.</span></div><?php endif; ?>
    <?php foreach ($transfers as $t): $daqui = saque_criado((string)$t['id']); ?>
      <div><span><?= $h(date('d/m H:i', strtotime($t['dateCreated'] ?? 'now'))) ?> · <?= $h($stTr[$t['status'] ?? ''] ?? strtolower((string)($t['status'] ?? ''))) ?><?= $daqui ? ' · AUTONOM/IA' : '' ?></span><b><?= $h(brl((float)($t['value'] ?? 0))) ?></b></div>
    <?php endforeach; ?>
    </div>
  </div>
</div>

<?php if ($isAdmin): ?>
<dialog id="saque">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= $h($csrf) ?>"><input type="hidden" name="acao" value="sacar">
    <h2>Sacar pro banco</h2>
    <?php if (empty(cfg()['saque_habilitado'])): ?>
      <p class="sub">Por segurança, o saque é feito no painel financeiro da empresa, que usa a mesma conta e o mesmo saldo.</p>
      <div class="acts" style="margin-top:14px;justify-content:flex-end"><button class="btn" type="button" onclick="this.closest('dialog').close()">Fechar</button><a class="btn sig" href="https://pipo.guru/financeiro" target="_blank" rel="noopener">Abrir painel financeiro</a></div>
    <?php elseif (!$destino): ?>
      <p class="sub">A chave Pix de destino ainda não foi configurada (secrets SAQUE_PIX_CHAVE e SAQUE_PIX_TIPO).</p>
      <div class="acts" style="margin-top:14px"><button class="btn" type="button" onclick="this.closest('dialog').close()">Fechar</button></div>
    <?php else: ?>
      <p class="sub">Disponível: <b><?= $saldo === null ? '—' : $h(brl($saldo)) ?></b> · destino <?= $h(saque_mascara($destino)) ?></p>
      <label for="valor">Valor (R$)</label>
      <div style="display:flex;gap:8px"><input id="valor" name="valor" inputmode="decimal" placeholder="0,00" required style="flex:1"><button class="btn" type="button" onclick="document.getElementById('valor').value='<?= $saldo ? number_format($saldo, 2, ',', '.') : '0,00' ?>'">Tudo</button></div>
      <label for="senha">Sua senha, pra confirmar</label>
      <input id="senha" name="senha" type="password" autocomplete="current-password" required>
      <div class="acts" style="margin-top:14px;justify-content:flex-end"><button class="btn" type="button" onclick="this.closest('dialog').close()">Cancelar</button><button class="btn sig">Confirmar saque</button></div>
      <p class="note">O saldo é da conta da empresa. O Pix sai sempre pra chave acima, que não pode ser mudada por aqui.</p>
    <?php endif; ?>
  </form>
</dialog>
<?php endif; ?>

<p class="note">Os números vêm dos pedidos feitos pelo site e são atualizados sozinhos a cada pagamento. "Atualizar pagamentos" confere tudo de novo, caso algo não tenha chegado.</p>
</div></body></html>
