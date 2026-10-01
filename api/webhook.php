<?php
// Recebe os eventos do Asaas. Configure em Asaas > Integrações > Webhooks:
//   URL: https://autonomia.vc/api/webhook.php · Token de autenticação: o mesmo de ASAAS_WEBHOOK_TOKEN
//   Eventos: cobranças (PAYMENT_CONFIRMED, PAYMENT_RECEIVED, PAYMENT_OVERDUE, PAYMENT_REFUNDED, PAYMENT_DELETED...)
require __DIR__ . '/lib.php';

$token = cfg()['webhook_token'];
$recebido = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '';
if (!$token || !hash_equals($token, $recebido)) json_out(['ok' => false], 401);

$raw = file_get_contents('php://input') ?: '';
$ev = json_decode($raw, true);
if (!is_array($ev) || empty($ev['event'])) json_out(['ok' => true, 'ignorado' => 'sem evento']);

// idempotência: o Asaas entrega "pelo menos uma vez"
$evId = preg_replace('/[^A-Za-z0-9_\-]/', '', (string)($ev['id'] ?? md5($raw)));
$evFile = data_dir('eventos') . '/' . $evId . '.json';
if (is_file($evFile)) json_out(['ok' => true, 'repetido' => true]);
file_put_contents($evFile, $raw, LOCK_EX);

$pay = $ev['payment'] ?? [];
$pedido = pedido_ler((string)($pay['externalReference'] ?? ''));
if (!$pedido) json_out(['ok' => true, 'ignorado' => 'pedido desconhecido']);

$notificar = null;
switch ($ev['event']) {
    case 'PAYMENT_CONFIRMED':   // cartão aprovado
    case 'PAYMENT_RECEIVED':    // PIX caiu
        if ($pedido['status'] !== 'pago') {
            $pedido['status'] = 'pago';
            $pedido['pago_em'] = date('c');
            $notificar = 'Nova inscrição confirmada';
        }
        break;
    case 'PAYMENT_OVERDUE':
        if ($pedido['status'] === 'aguardando') $pedido['status'] = 'vencido';
        break;
    case 'PAYMENT_REFUNDED':
    case 'PAYMENT_PARTIALLY_REFUNDED':
    case 'PAYMENT_CHARGEBACK_REQUESTED':
    case 'PAYMENT_DELETED':
        if ($pedido['status'] === 'pago') $notificar = 'Inscrição cancelada ou estornada';
        $pedido['status'] = $ev['event'] === 'PAYMENT_DELETED' && $pedido['status'] !== 'pago' ? 'cancelado' : 'estornado';
        break;
}
if (!empty($pay['id'])) $pedido['pagamentos'][$pay['id']] = pagamento_resumo($pay);   // espelho financeiro (valor, líquido, previsão de crédito)
$pedido['eventos'][] = ['evento' => $ev['event'], 'em' => date('c')];
pedido_salvar($pedido);

// responde rápido pro Asaas e só depois manda o e-mail pra equipe
if ($notificar) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo '{"ok":true}';
    if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
    notificar_equipe($pedido, $notificar);
    exit;
}
json_out(['ok' => true]);
