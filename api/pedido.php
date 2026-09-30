<?php
// Status público e mínimo de um pedido (sem dados pessoais), usado pela página de obrigado.
require __DIR__ . '/lib.php';
$p = pedido_ler((string)($_GET['id'] ?? ''));
if (!$p) json_out(['ok' => false, 'erro' => 'Pedido não encontrado.'], 404);
$pago = ($p['status'] ?? '') === 'pago';
json_out([
    'ok'     => true,
    'status' => $p['status'],
    'pago'   => $pago,
    'vagas'  => $p['vagas'],
    'total'  => $p['total'],
    'nome'   => explode(' ', $p['p1']['nome'])[0],
    'grupo'  => $pago ? (cfg()['grupo_whatsapp'] ?: null) : null,
    'url'    => $pago ? null : ($p['invoice_url'] ?? null),
]);
