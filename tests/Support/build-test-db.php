<?php

/**
 * Build the SQLite test database by applying all InvoicePlane SQL migrations.
 *
 * Converts MySQL DDL/DML to SQLite-compatible syntax on the fly.
 *
 * Usage:
 *   php tests/Support/build-test-db.php [/path/to/database.sqlite]
 */

$dbPath = $argv[1] ?? __DIR__ . '/../../storage/test.sqlite';

$dir = dirname($dbPath);
if ( ! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

if (file_exists($dbPath)) {
    unlink($dbPath);
}

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=OFF;');

$failures = [];

$sqlDir = __DIR__ . '/../../application/modules/setup/sql';
$files  = glob($sqlDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);

    $stripped = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $stripped = preg_replace('/#[^\n]*/', '', $stripped);
    if (trim($stripped) === '') {
        continue;
    }

    $statements = convertMySqlToSqlite($stripped);

    foreach ($statements as $stmt) {
        // Strip any leading -- comment lines that ended up prepended to the statement
        $stmt = trim(preg_replace('/^--[^\n]*\n?/m', '', trim($stmt)));
        if ($stmt === '' || $stmt === ';') {
            continue;
        }

        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (
                str_contains($msg, 'duplicate column') ||
                str_contains($msg, 'already exists') ||
                str_contains($msg, 'no such column')
            ) {
                continue;
            }

            $label = basename($file) . ': ' . mb_substr($stmt, 0, 80);
            fwrite(STDERR, "ERROR in {$label} → {$msg}\n");
            $failures[] = $label;
        }
    }
}

if ($failures !== []) {
    fwrite(STDERR, "\n" . count($failures) . " migration statement(s) failed — cannot seed.\n");
    exit(1);
}

seedDefaults($pdo);
fixupMissingColumns($pdo);

echo "Test database built: {$dbPath}\n";

// ---------------------------------------------------------------------------

function convertMySqlToSqlite(string $sql): array
{
    // Remove MySQL comments (# style and -- style)
    $sql = preg_replace('/^#[^\n]*/m', '', $sql);
    $sql = preg_replace('/^--[^\n]*/m', '', $sql);

    // Replace backtick identifiers with double-quoted
    $sql = preg_replace('/`([^`]+)`/', '"$1"', $sql);

    // Remove CREATE TABLE options — handles any order of ENGINE/CHARSET/COLLATE/AUTO_INCREMENT
    // The closing ) may have ENGINE = x, AUTO_INCREMENT = n, DEFAULT CHARSET = x, COLLATE = x in any order
    $sql = preg_replace_callback(
        '/\)\s*((?:(?:ENGINE|AUTO_INCREMENT|DEFAULT\s+CHARSET|COLLATE|ROW_FORMAT|COMMENT|PACK_KEYS|CHECKSUM|DELAY_KEY_WRITE|MAX_ROWS|MIN_ROWS|AVG_ROW_LENGTH)\s*=\s*\S+\s*)*)\s*;/i',
        fn ($m) => ');',
        $sql
    );

    // Convert AUTO_INCREMENT column definition to INTEGER NOT NULL
    $sql = preg_replace(
        '/INT(?:\(\d+\))?\s+NOT NULL\s+AUTO_INCREMENT/i',
        'INTEGER NOT NULL',
        $sql
    );

    // Remove KEY / INDEX definitions inside CREATE TABLE
    $sql = preg_replace('/,\s*(?:UNIQUE\s+)?(?:KEY|INDEX)\s+"[^"]+"\s+\([^)]+\)/i', '', $sql);

    // Strip inline COMMENT '...' from column definitions (before other transforms)
    $sql = preg_replace("/\\s+COMMENT\\s+'(?:[^'\\\\]|\\\\.)*'/i", '', $sql);

    // Remove AFTER clauses (both quoted and unquoted identifiers)
    $sql = preg_replace('/\s+AFTER\s+(?:"[^"]+"|`[^`]+`|\w+)/i', '', $sql);

    // Convert ALTER TABLE ... CHANGE [COLUMN] → no-op (quoted or unquoted table)
    $sql = preg_replace(
        '/ALTER TABLE\s+(?:"[^"]+"|[\w]+)\s+CHANGE\s+(?:COLUMN\s+)?(?:"[^"]+"|[\w]+)\s+(?:"[^"]+"|[\w]+)\s+[^;]+;/i',
        '-- CHANGE COLUMN skipped (SQLite unsupported);',
        $sql
    );

    // Convert ALTER TABLE ... MODIFY [COLUMN] → no-op
    $sql = preg_replace(
        '/ALTER TABLE\s+(?:"[^"]+"|[\w]+)\s+MODIFY\s+(?:COLUMN\s+)?(?:"[^"]+"|[\w]+)\s+[^;]+;/i',
        '-- MODIFY COLUMN skipped (SQLite unsupported);',
        $sql
    );

    // Convert ALTER TABLE that only has DROP actions → no-op
    $sql = preg_replace(
        '/ALTER TABLE\s+(?:"[^"]+"|[\w]+)\s+(?:DROP\s+(?:COLUMN\s+)?(?:"[^"]+"|[\w]+)\s*,?\s*)+;/i',
        '-- DROP COLUMN skipped (SQLite unsupported);',
        $sql
    );

    // Split multi-column ALTER TABLE ADD [COLUMN] into individual statements
    // Match both quoted and unquoted table names
    $sql = preg_replace_callback(
        '/ALTER TABLE\s+("([^"]+)"|(\w+))\s+ADD\s+(?:COLUMN\s+)?(.*?)(?=\nALTER TABLE|\Z)/is',
        function ($m) {
            $table = $m[1];
            $cols  = $m[4];
            // Split on , ADD [COLUMN]
            $parts = preg_split('/,\s*ADD\s+(?:COLUMN\s+)?/i', $cols);
            $stmts = [];
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '' || $part === ';') {
                    continue;
                }
                $part = rtrim($part, ';,');
                // Skip INDEX and PRIMARY KEY additions
                if (preg_match('/^\s*(?:PRIMARY\s+KEY|(?:UNIQUE\s+)?INDEX)/i', $part)) {
                    continue;
                }
                $stmts[] = "ALTER TABLE {$table} ADD COLUMN {$part};";
            }

            return implode("\n", $stmts) . "\n";
        },
        $sql
    );

    // Types
    $sql = preg_replace('/ENUM\s*\([^)]+\)/i', 'TEXT', $sql);
    $sql = preg_replace('/LONGTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/TINYTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/MEDIUMTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/MEDIUMBLOB/i', 'BLOB', $sql);
    $sql = preg_replace('/VARCHAR\(\d+\)/i', 'TEXT', $sql);
    $sql = preg_replace('/INT\(\d+\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bTINYINT\(\d+\)\b/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bSMALLINT\(\d+\)\b/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bBIGINT\(\d+\)\b/i', 'INTEGER', $sql);
    $sql = preg_replace('/DECIMAL\(\d+,\s*\d+\)/i', 'REAL', $sql);
    $sql = preg_replace('/\bBOOLEAN\b/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bTINYINT\b/i', 'INTEGER', $sql);

    // ON DUPLICATE KEY UPDATE → INSERT OR REPLACE
    $sql = preg_replace(
        '/INSERT INTO\s+("([^"]+)")\s+\(([^)]+)\)\s+VALUES\s+\(([^)]+)\)\s+ON DUPLICATE KEY UPDATE[^;]+;/i',
        'INSERT OR REPLACE INTO $1 ($3) VALUES ($4);',
        $sql
    );

    // Split into individual statements
    $parts = preg_split('/;\s*\n/m', $sql);

    return array_map(fn (string $s) => trim($s), $parts);
}

function seedDefaults(PDO $pdo): void
{
    try {
        $pdo->query('SELECT 1 FROM "ip_settings" LIMIT 1');
    } catch (PDOException) {
        fwrite(STDERR, "WARNING: ip_settings table not found — check migration errors above.\n");

        return;
    }

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
        ['quotes_expire_after',      '30'],
        ['decimal_point',            '.'],
        ['thousands_separator',      ','],
        ['tax_rate_decimal_places',  '2'],
        ['default_item_decimals',    '2'],
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO "ip_settings" ("setting_key","setting_value") VALUES (?,?)');
    foreach ($settings as [$key, $val]) {
        $stmt->execute([$key, $val]);
    }

    // Seed admin user (user_id=1) with all NOT NULL columns
    $hash = password_hash('password', PASSWORD_DEFAULT);
    $pdo->exec("INSERT OR IGNORE INTO \"ip_users\"
        (\"user_id\",\"user_name\",\"user_email\",\"user_password\",\"user_psalt\",
         \"user_type\",\"user_active\",\"user_date_created\",\"user_date_modified\")
        VALUES (1,'Admin','admin@test.local','{$hash}','salt1234567890123456',
                1,1,'2024-01-01 00:00:00','2024-01-01 00:00:00')");

    // Seed default invoice group
    $pdo->exec("INSERT OR IGNORE INTO \"ip_invoice_groups\"
        (\"invoice_group_id\",\"invoice_group_name\",\"invoice_group_identifier_format\",
         \"invoice_group_next_id\",\"invoice_group_left_pad\")
        VALUES (1,'Default','INV-{{{id}}}',1,4)");

    // Seed default payment method
    $pdo->exec("INSERT OR IGNORE INTO \"ip_payment_methods\"
        (\"payment_method_id\",\"payment_method_name\")
        VALUES (1,'Cash')");
}

function fixupMissingColumns(PDO $pdo): void
{
    // These columns exist in the MySQL production schema but were never captured
    // in any migration SQL file. Add them here so SQLite tests work correctly.
    $fixups = [
        'ALTER TABLE "ip_client_custom"  ADD COLUMN "client_custom_fieldid"    INTEGER',
        'ALTER TABLE "ip_client_custom"  ADD COLUMN "client_custom_fieldvalue" TEXT',
        'ALTER TABLE "ip_quote_custom"   ADD COLUMN "quote_custom_fieldid"     INTEGER',
        'ALTER TABLE "ip_quote_custom"   ADD COLUMN "quote_custom_fieldvalue"  TEXT',
        'ALTER TABLE "ip_payment_custom" ADD COLUMN "payment_custom_fieldid"   INTEGER',
        'ALTER TABLE "ip_payment_custom" ADD COLUMN "payment_custom_fieldvalue" TEXT',
        'ALTER TABLE "ip_invoice_custom" ADD COLUMN "invoice_custom_fieldid"   INTEGER',
        'ALTER TABLE "ip_invoice_custom" ADD COLUMN "invoice_custom_fieldvalue" TEXT',
        'ALTER TABLE "ip_user_custom"    ADD COLUMN "user_custom_fieldid"      INTEGER',
        'ALTER TABLE "ip_user_custom"    ADD COLUMN "user_custom_fieldvalue"   TEXT',
    ];

    foreach ($fixups as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'duplicate column') || str_contains($e->getMessage(), 'already exists')) {
                continue;
            }
            fwrite(STDERR, "WARN fixup: " . mb_substr($sql, 0, 80) . " → " . $e->getMessage() . "\n");
        }
    }
}
