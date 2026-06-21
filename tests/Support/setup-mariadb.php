<?php

/**
 * Set up the MariaDB/MySQL test database by applying all InvoicePlane SQL migrations.
 *
 * Usage:
 *   php tests/Support/setup-mariadb.php [host] [user] [password] [database]
 *
 * Exits non-zero if any migration statement fails for a non-idempotent reason.
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

$failures = [];

foreach ($files as $file) {
    $sql      = file_get_contents($file);
    $stripped = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $stripped = preg_replace('/#[^\n]*/', '', $stripped);
    if (trim($stripped) === '') {
        continue;
    }

    $statements = array_filter(array_map('trim', explode(";\n", $sql)));

    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || $stmt === ';') {
            continue;
        }

        // Strip leading comment lines so a comment-prefixed block doesn't
        // cause the actual DDL on the next line to be silently dropped.
        $stmt = trim(preg_replace('/^(?:--[^\n]*\n|\/\*.*?\*\/\s*)/s', '', $stmt));

        if ($stmt === '' || $stmt === ';') {
            continue;
        }

        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            $msg = $e->getMessage();

            // Idempotent: safe to ignore on re-runs or out-of-order application
            if (
                str_contains($msg, 'Duplicate column') ||
                str_contains($msg, 'already exists') ||
                str_contains($msg, "doesn't exist") ||
                str_contains($msg, 'Can\'t DROP')
            ) {
                continue;
            }

            $label = basename($file) . ': ' . mb_substr($stmt, 0, 120);
            fwrite(STDERR, "ERROR in {$label}\n  → {$msg}\n");
            $failures[] = $label;
        }
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

fixupMissingColumns($pdo);

if ($failures !== []) {
    fwrite(STDERR, "\n" . count($failures) . " migration statement(s) failed — cannot seed.\n");
    exit(1);
}

seedDefaults($pdo);

echo "MariaDB test database ready: {$dbName}@{$host}\n";

function fixupMissingColumns(PDO $pdo): void
{
    // These columns exist in the production schema but were never captured in
    // any migration SQL file (same gap patched by build-test-db.php for SQLite).
    $fixups = [
        'ALTER TABLE `ip_client_custom`  ADD COLUMN `client_custom_fieldid`    INT(11)',
        'ALTER TABLE `ip_client_custom`  ADD COLUMN `client_custom_fieldvalue` TEXT',
        'ALTER TABLE `ip_quote_custom`   ADD COLUMN `quote_custom_fieldid`     INT(11)',
        'ALTER TABLE `ip_quote_custom`   ADD COLUMN `quote_custom_fieldvalue`  TEXT',
        'ALTER TABLE `ip_payment_custom` ADD COLUMN `payment_custom_fieldid`   INT(11)',
        'ALTER TABLE `ip_payment_custom` ADD COLUMN `payment_custom_fieldvalue` TEXT',
        'ALTER TABLE `ip_invoice_custom` ADD COLUMN `invoice_custom_fieldid`   INT(11)',
        'ALTER TABLE `ip_invoice_custom` ADD COLUMN `invoice_custom_fieldvalue` TEXT',
        'ALTER TABLE `ip_user_custom`    ADD COLUMN `user_custom_fieldid`      INT(11)',
        'ALTER TABLE `ip_user_custom`    ADD COLUMN `user_custom_fieldvalue`   TEXT',
    ];

    foreach ($fixups as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate column') || str_contains($e->getMessage(), 'already exists')) {
                continue;
            }
            fwrite(STDERR, "WARN fixup: " . mb_substr($sql, 0, 80) . " → " . $e->getMessage() . "\n");
        }
    }
}

function seedDefaults(PDO $pdo): void
{
    $settings = [
        ['default_language',         'english'],
        ['currency_symbol',          '$'],
        ['currency_symbol_placement', 'before'],
        ['date_format',              'Y-m-d'],
        ['time_format',              'H:i'],
        ['pdf_engine',               'pdfmake'],
        ['pdf_paper_size',           'a4'],
        ['pdf_paper_orientation',    'portrait'],
        ['read_only_toggle',         'paid'],
        ['next_invoice_number',      '1'],
        ['next_quote_number',        '1'],
        ['invoicenumber_prefix',     ''],
        ['quotenumber_prefix',       ''],
        ['disable_setup',            '1'],
        ['sumex',                    '0'],
    ];

    $stmt = $pdo->prepare('INSERT IGNORE INTO `ip_settings` (`setting_key`,`setting_value`) VALUES (?,?)');
    foreach ($settings as [$key, $val]) {
        $stmt->execute([$key, $val]);
    }

    $hash = password_hash('password', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO `ip_users`
        (`user_id`,`user_name`,`user_email`,`user_password`,`user_psalt`,
         `user_type`,`user_active`,`user_date_created`,`user_date_modified`)
        VALUES (1,'Admin','admin@test.local','{$hash}','salt1234567890123456',
                1,1,NOW(),NOW())");

    $pdo->exec("INSERT IGNORE INTO `ip_invoice_groups`
        (`invoice_group_id`,`invoice_group_name`,`invoice_group_identifier_format`,
         `invoice_group_next_id`,`invoice_group_left_pad`)
        VALUES (1,'Default','INV-{{{id}}}',1,4)");

    $pdo->exec("INSERT IGNORE INTO `ip_payment_methods`
        (`payment_method_id`,`payment_method_name`)
        VALUES (1,'Cash')");
}
