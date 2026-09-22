<?php

/**
 * Tiny DB helper for the E2E suite — the equivalent of
 * InteractsWithDatabase::databaseInsert() / assertDatabase* for setup rows that
 * have no clean UI path (quote/invoice tax-rate rows, invoice items, …).
 *
 * Usage (called from tests/E2E/support/db.js, never by hand):
 *   php tests/Support/e2e-sql.php insert ip_quote_tax_rates '{"quote_id":3,...}'
 *       -> prints the new row id
 *   php tests/Support/e2e-sql.php query 'SELECT ... '
 *       -> prints a JSON array of rows
 *
 * Connection details come from the DB_* environment (same as seed-test-db.php).
 */
$host = getenv('DB_HOSTNAME') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_DATABASE') ?: 'invoiceplane_test';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'root';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $host, $port, $name),
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$mode = $argv[1] ?? '';

if ($mode === 'insert') {
    $table = $argv[2] ?? '';
    $row   = json_decode($argv[3] ?? '{}', true, 512, JSON_THROW_ON_ERROR);
    if ($table === '' || $row === []) {
        fwrite(STDERR, "insert needs a table and a non-empty JSON object\n");
        exit(1);
    }
    $cols         = array_keys($row);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $quoted       = implode(', ', array_map(static fn ($c) => '`' . str_replace('`', '``', $c) . '`', $cols));
    $pdo->prepare("INSERT INTO `{$table}` ({$quoted}) VALUES ({$placeholders})")
        ->execute(array_values($row));
    echo $pdo->lastInsertId();

    return;
}

if ($mode === 'query') {
    $rows = $pdo->query($argv[2] ?? 'SELECT 1')->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_THROW_ON_ERROR);

    return;
}

if ($mode === 'exec') {
    echo $pdo->exec($argv[2] ?? '');

    return;
}

fwrite(STDERR, "unknown mode '{$mode}' (expected: insert | query | exec)\n");
exit(1);
