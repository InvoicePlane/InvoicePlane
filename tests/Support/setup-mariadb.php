<?php

/**
 * Set up the MariaDB/MySQL test database by applying all InvoicePlane SQL migrations.
 *
 * Usage:
 *   php tests/Support/setup-mariadb.php [host] [user] [password] [database]
 */

$host   = $argv[1] ?? '127.0.0.1';
$user   = $argv[2] ?? 'root';
$pass   = $argv[3] ?? 'root';
$dbName = $argv[4] ?? 'invoiceplane_test';

$pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

$sqlDir = __DIR__ . '/../../application/modules/setup/sql';
$files  = glob($sqlDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql     = file_get_contents($file);
    $stripped = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $stripped = preg_replace('/#[^\n]*/', '', $stripped);
    if (trim($stripped) === '') {
        continue;
    }

    $statements = array_filter(array_map('trim', explode(";\n", $sql)));

    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || $stmt === ';' || str_starts_with($stmt, '--') || str_starts_with($stmt, '/*')) {
            continue;
        }

        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            // Ignore idempotent errors (duplicate column, table already exists)
            if (
                str_contains($msg, 'Duplicate column') ||
                str_contains($msg, 'already exists') ||
                str_contains($msg, "doesn't exist")
            ) {
                continue;
            }

            fwrite(STDERR, "WARN in " . basename($file) . ": " . mb_substr($stmt, 0, 80) . " → {$msg}\n");
        }
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

seedDefaults($pdo);

echo "MariaDB test database ready: {$dbName}@{$host}\n";

function seedDefaults(PDO $pdo): void
{
    // Settings
    $settings = [
        ['default_language',        'english'],
        ['currency_symbol',         '$'],
        ['currency_symbol_placement','before'],
        ['date_format',             'Y-m-d'],
        ['time_format',             'H:i'],
        ['pdf_engine',              'pdfmake'],
        ['pdf_paper_size',          'a4'],
        ['pdf_paper_orientation',   'portrait'],
        ['read_only_toggle',        'paid'],
        ['next_invoice_number',     '1'],
        ['next_quote_number',       '1'],
        ['invoicenumber_prefix',    ''],
        ['quotenumber_prefix',      ''],
        ['disable_setup',           '1'],
        ['sumex',                   '0'],
    ];

    $stmt = $pdo->prepare('INSERT IGNORE INTO `ip_settings` (`setting_key`,`setting_value`) VALUES (?,?)');
    foreach ($settings as [$key, $val]) {
        $stmt->execute([$key, $val]);
    }

    // Admin user (user_id=1)
    $hash = password_hash('password', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO `ip_users`
        (`user_id`,`user_name`,`user_email`,`user_password`,`user_psalt`,
         `user_type`,`user_active`,`user_date_created`,`user_date_modified`)
        VALUES (1,'Admin','admin@test.local','{$hash}','salt1234567890123456',
                1,1,NOW(),NOW())");

    // Default invoice group (invoice_group_id=1)
    $pdo->exec("INSERT IGNORE INTO `ip_invoice_groups`
        (`invoice_group_id`,`invoice_group_name`,`invoice_group_identifier_format`,
         `invoice_group_next_id`,`invoice_group_left_pad`)
        VALUES (1,'Default','INV-{{{id}}}',1,4)");

    // Default payment method (payment_method_id=1)
    $pdo->exec("INSERT IGNORE INTO `ip_payment_methods`
        (`payment_method_id`,`payment_method_name`)
        VALUES (1,'Cash')");
}
