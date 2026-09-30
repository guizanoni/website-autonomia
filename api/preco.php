<?php
// Simula o preço (com ou sem código de convite) pra mostrar no formulário antes de pagar.
require __DIR__ . '/lib.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false], 405);
if (!limitar('preco', 60, 600)) json_out(['ok' => false, 'erro' => 'Muitas tentativas. Espere alguns minutos.'], 429);
$b = body_json();
$calc = calcular(($b['tipo'] ?? '') === 'dupla' ? 'dupla' : 'individual', (string)($b['forma'] ?? 'pix'), (string)($b['cupom'] ?? ''), (int)($b['parcelas'] ?? 1));
json_out(['ok' => true] + $calc + ['situacao' => situacao_vagas()]);
