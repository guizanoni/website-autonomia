<?php
// Quantas vagas ainda existem. A página usa pra mostrar "restam X" e virar lista de espera ao esgotar.
require __DIR__ . '/lib.php';
json_out(['ok' => true] + situacao_vagas());
