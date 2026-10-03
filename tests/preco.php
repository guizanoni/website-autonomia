<?php
// Execute: php tests/preco.php. Usa apenas configuração fictícia, sem acessar o Asaas.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

$tmp = sys_get_temp_dir() . '/autonomia-preco-' . bin2hex(random_bytes(8));
mkdir($tmp, 0700);
copy(__DIR__ . '/../api/lib.php', $tmp . '/lib.php');
file_put_contents($tmp . '/_config-secret.php', '<?php return ' . var_export([
    'cupons' => [
        'FIXO' => ['preco' => 997],
        'ACUMULA' => ['preco' => 997, 'acumula_pix' => true],
    ],
], true) . ';');

try {
    require $tmp . '/lib.php';
    $cases = [
        // tipo, forma, cupom, parcelas pedidas, unitário, total, parcelas, valor da parcela, desconto
        ['individual', 'pix', '', 10, 1167.30, 1167.30, 1, 1167.30, '10% de desconto no PIX'],
        ['dupla', 'pix', '', 10, 1167.30, 2334.60, 1, 2334.60, '10% de desconto no PIX'],
        ['individual', 'cartao', '', 10, 1297.00, 1297.00, 10, 129.70, null],
        ['dupla', 'cartao', '', 10, 1193.24, 2386.48, 10, 238.65, '8% de desconto pra cada uma (dupla)'],
        ['dupla', 'cartao', '', 1, 1193.24, 2386.48, 1, 2386.48, '8% de desconto pra cada uma (dupla)'],
        ['individual', 'cartao', '', 99, 1297.00, 1297.00, 10, 129.70, null],
        ['individual', 'cartao', '', 0, 1297.00, 1297.00, 1, 1297.00, null],
        ['individual', 'pix', 'FIXO', 10, 997.00, 997.00, 1, 997.00, 'Código FIXO'],
        ['dupla', 'pix', 'FIXO', 10, 997.00, 1994.00, 1, 1994.00, 'Código FIXO'],
        ['dupla', 'cartao', 'FIXO', 10, 997.00, 1994.00, 10, 199.40, 'Código FIXO'],
        ['individual', 'pix', 'ACUMULA', 10, 897.30, 897.30, 1, 897.30, 'Código ACUMULA'],
        ['dupla', 'pix', 'ACUMULA', 10, 897.30, 1794.60, 1, 1794.60, 'Código ACUMULA'],
        ['individual', 'cartao', 'ACUMULA', 10, 997.00, 997.00, 10, 99.70, 'Código ACUMULA'],
        ['individual', 'pix', 'INVALIDO', 1, 1167.30, 1167.30, 1, 1167.30, '10% de desconto no PIX'],
        ['dupla', 'cartao', 'INVALIDO', 10, 1193.24, 2386.48, 10, 238.65, '8% de desconto pra cada uma (dupla)'],
    ];
    foreach ($cases as $i => $case) {
        [$tipo, $forma, $cupom, $parcelas, $unit, $total, $n, $valor, $desconto] = $case;
        $result = calcular($tipo, $forma, $cupom, $parcelas);
        $expected = ['vagas' => $tipo === 'dupla' ? 2 : 1, 'unitario' => $unit, 'total' => $total, 'parcelas' => $n,
            'valor_parcela' => $valor, 'desconto' => $desconto,
            'cupom_ok' => in_array($cupom, ['FIXO', 'ACUMULA'], true)];
        foreach ($expected as $field => $value) {
            if ($result[$field] !== $value) throw new RuntimeException('Caso ' . ($i + 1) . ': campo ' . $field . ' divergente.');
        }
    }
    echo count($cases) . " cenários de preço passaram.\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    $failed = true;
} finally {
    unlink($tmp . '/lib.php');
    unlink($tmp . '/_config-secret.php');
    rmdir($tmp);
}
exit(empty($failed) ? 0 : 1);
