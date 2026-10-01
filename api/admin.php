<?php
// Painel simples dos pedidos: /api/admin.php?key=SENHA  (e &csv=1 pra baixar planilha)
require __DIR__ . '/lib.php';

$key = cfg()['admin_key'];
if (!$key || !hash_equals($key, (string)($_GET['key'] ?? ''))) { http_response_code(403); exit('Acesso negado.'); }

$pedidos = pedidos_todos();
$sit = situacao_vagas();

if (!empty($_GET['csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="autonomia-pedidos-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['pedido','status','criado_em','pago_em','vagas','forma','parcelas','total','cupom','nome','email','whatsapp','cpf','amiga_nome','amiga_email','amiga_whatsapp','nota_cnpj','nota_razao','ambiente'], ';');
    foreach ($pedidos as $p) {
        fputcsv($out, [$p['id'],$p['status'],$p['criado_em'],$p['pago_em'] ?? '',$p['vagas'],$p['forma'],$p['parcelas'],number_format((float)$p['total'],2,',',''),$p['cupom'] ?? '',
            $p['p1']['nome'],$p['p1']['email'],$p['p1']['whatsapp'],$p['p1']['cpf'],$p['p2']['nome'] ?? '',$p['p2']['email'] ?? '',$p['p2']['whatsapp'] ?? '',$p['nota_cnpj'] ?? '',$p['nota_razao'] ?? '',$p['ambiente'] ?? ''], ';');
    }
    exit;
}

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$cor = ['pago' => '#127a3a', 'aguardando' => '#9a6700', 'vencido' => '#777', 'estornado' => '#b42318', 'cancelado' => '#777'];
$receita = array_sum(array_map(fn($p) => $p['status'] === 'pago' ? (float)$p['total'] : 0, $pedidos));
?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title>Pedidos · AUTONOM/IA</title>
<style>
body{font:15px/1.5 system-ui,sans-serif;margin:0;padding:24px;background:#f4f4f2;color:#191c1f}
h1{font-size:22px;margin:0 0 4px}.sub{color:#6b6e73;margin-bottom:20px}
.kpis{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px}.kpis div{background:#fff;border-radius:14px;padding:14px 18px}.kpis b{display:block;font-size:24px}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:14px;overflow:hidden;font-size:14px}th,td{padding:10px 12px;text-align:left;border-bottom:1px solid #eee;vertical-align:top}th{background:#191c1f;color:#fff;font-weight:500}
.st{font-weight:600}a{color:#e0441a}.wrap{overflow-x:auto}
</style></head><body>
<h1>Pedidos · <?= $h(EVENTO['nome']) ?></h1>
<p class="sub">Ambiente: <b><?= $h(cfg()['env']) ?></b> · <a href="?key=<?= $h($_GET['key']) ?>&csv=1">Baixar planilha (CSV)</a></p>
<div class="kpis">
  <div><b><?= $sit['vendidas'] ?>/<?= $sit['vagas'] ?></b>vagas pagas</div>
  <div><b><?= $sit['restantes'] ?></b>restantes</div>
  <div><b><?= $h(brl($receita)) ?></b>recebido (bruto)</div>
  <div><b><?= count(array_filter($pedidos, fn($p) => $p['status'] === 'aguardando')) ?></b>aguardando pagamento</div>
</div>
<?php
$evs = glob(data_dir('eventos') . '/*.json') ?: [];
usort($evs, fn($a, $b) => filemtime($b) <=> filemtime($a));
$evs = array_slice($evs, 0, 8);
?>
<p class="sub">Últimos avisos do Asaas recebidos: <?php if (!$evs): ?>nenhum ainda.<?php else: foreach ($evs as $f): $e = json_decode((string)file_get_contents($f), true); ?>
  <br>· <?= $h(date('d/m H:i:s', filemtime($f))) ?> · <?= $h($e['event'] ?? '?') ?> · <?= $h($e['payment']['externalReference'] ?? '') ?>
<?php endforeach; endif; ?></p>
<div class="wrap"><table>
<tr><th>Status</th><th>Quando</th><th>Compradora</th><th>Amiga</th><th>Pagamento</th><th>Código</th><th>Nota</th></tr>
<?php foreach ($pedidos as $p): ?>
<tr>
  <td class="st" style="color:<?= $cor[$p['status']] ?? '#191c1f' ?>"><?= $h($p['status']) ?></td>
  <td><?= $h(date('d/m H:i', strtotime($p['criado_em']))) ?><?= !empty($p['pago_em']) ? '<br>pago ' . $h(date('d/m H:i', strtotime($p['pago_em']))) : '' ?></td>
  <td><?= $h($p['p1']['nome']) ?><br><?= $h($p['p1']['email']) ?><br><a href="https://wa.me/<?= $h(wa_numero($p['p1']['whatsapp'])) ?>" target="_blank"><?= $h($p['p1']['whatsapp']) ?></a></td>
  <td><?php if ($p['p2']): ?><?= $h($p['p2']['nome']) ?><br><?= $h($p['p2']['email']) ?><br><a href="https://wa.me/<?= $h(wa_numero($p['p2']['whatsapp'])) ?>" target="_blank"><?= $h($p['p2']['whatsapp']) ?></a><?php else: ?>—<?php endif; ?></td>
  <td><?= $h(brl((float)$p['total'])) ?><br><?= $p['forma'] === 'pix' ? 'PIX' : 'Cartão ' . (int)$p['parcelas'] . 'x' ?></td>
  <td><?= $h($p['cupom'] ?? '') ?></td>
  <td><?= $p['nota_cnpj'] ? $h($p['nota_cnpj']) . '<br>' . $h($p['nota_razao']) : 'CPF' ?></td>
</tr>
<?php endforeach; ?>
</table></div>
</body></html>
