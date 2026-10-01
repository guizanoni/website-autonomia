<?php
// Consultado pelo validador de saque do pipo.guru (conta Asaas compartilhada).
// Só responde "sim" pra transferência criada pelo /admin da AUTONOM/IA, com o mesmo valor.
// Não revela nada além disso e não aprova nada sozinho: quem decide é o pipo.guru.
require __DIR__ . '/lib.php';
if (!limitar('saque-confirma', 30, 600)) json_out(['ok' => false], 429);
$id = preg_replace('/[^A-Za-z0-9_\-]/', '', (string)($_GET['id'] ?? ''));
$centavos = (int)($_GET['centavos'] ?? -1);
$s = $id !== '' ? saque_criado($id) : null;
json_out(['ok' => (bool)$s && (int)$s['centavos'] === $centavos]);
