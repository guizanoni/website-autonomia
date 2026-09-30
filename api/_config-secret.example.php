<?php
// Modelo. O arquivo real (_config-secret.php) é gerado pelo deploy a partir dos secrets
// do GitHub e NUNCA vai pro repositório. Pra testar localmente, copie este arquivo e preencha.
return [
    'env'            => 'sandbox',            // 'sandbox' pra testar, 'production' pra valer
    'asaas_key'      => '$aact_...',          // Asaas > Integrações > Chave de API
    'webhook_token'  => 'um-texto-longo-aleatorio-de-32-caracteres-ou-mais',
    'admin_key'      => 'senha-do-painel-de-pedidos',
    'grupo_whatsapp' => 'https://chat.whatsapp.com/...',
    'cupons'         => [
        // 'CODIGO' => ['preco' => 997, 'nome' => 'Comunidade X', 'limite' => 20, 'acumula_pix' => false],
    ],
];
