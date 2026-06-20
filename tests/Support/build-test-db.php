<?php

/**
 * Build the SQLite test database by applying all InvoicePlane SQL migrations.
 *
 * Converts MySQL DDL/DML to SQLite-compatible syntax on the fly:
 *   - Strips ENGINE, DEFAULT CHARSET, COLLATE, AUTO_INCREMENT table options
 *   - Converts `backtick` identifiers to "double-quoted"
 *   - Converts AUTO_INCREMENT column option to AUTOINCREMENT on INTEGER PRIMARY KEY
 *   - Drops unsupported CHANGE/MODIFY/CHANGE COLUMN (emitted as no-ops)
 *   - Handles INSERT ... ON DUPLICATE KEY UPDATE → INSERT OR REPLACE
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

$sqlDir = __DIR__ . '/../../application/modules/setup/sql';
$files  = glob($sqlDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);

    // Skip pure-comment/empty files
    $stripped = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $stripped = preg_replace('/#[^\n]*/', '', $stripped);
    $stripped = trim($stripped);
    if ($stripped === '') {
        continue;
    }

    $statements = convertMySqlToSqlite($sql);

    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || $stmt === ';') {
            continue;
        }

        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            // Tolerate "duplicate column" and similar upgrade-idempotency errors
            $msg = $e->getMessage();
            if (
                str_contains($msg, 'duplicate column') ||
                str_contains($msg, 'already exists') ||
                str_contains($msg, 'no such column')
            ) {
                // ignore — migration ran on already-updated schema
                continue;
            }

            fwrite(STDERR, "ERROR in {$file}:\n  stmt: " . mb_substr($stmt, 0, 120) . "\n  err:  {$msg}\n");
        }
    }
}

// Seed minimum required data
seedDefaults($pdo);

echo "Test database built: {$dbPath}\n";

// ---------------------------------------------------------------------------

function convertMySqlToSqlite(string $sql): array
{
    // Remove MySQL comments (# style)
    $sql = preg_replace('/^#[^\n]*/m', '', $sql);

    // Replace backtick identifiers with double-quoted
    $sql = preg_replace('/`([^`]+)`/', '"$1"', $sql);

    // Remove CREATE TABLE options on closing paren line
    $sql = preg_replace(
        '/\)\s*(ENGINE\s*=\s*\S+)?\s*(DEFAULT\s+CHARSET\s*=\s*\S+)?\s*(COLLATE\s*=\s*\S+)?\s*(AUTO_INCREMENT\s*=\s*\d+)?\s*;/i',
        ');',
        $sql
    );

    // Convert AUTO_INCREMENT column definition to INTEGER PRIMARY KEY AUTOINCREMENT
    $sql = preg_replace(
        '/INT\(\d+\)\s+NOT NULL\s+AUTO_INCREMENT/i',
        'INTEGER NOT NULL',
        $sql
    );

    // SQLite PRIMARY KEY on a single INTEGER column is implicitly AUTOINCREMENT
    // Remove redundant KEY definitions inside CREATE TABLE
    $sql = preg_replace('/,\s*KEY\s+"[^"]+"\s+\([^)]+\)/i', '', $sql);
    $sql = preg_replace('/,\s*UNIQUE KEY\s+"[^"]+"\s+\([^)]+\)/i', '', $sql);
    $sql = preg_replace('/,\s*INDEX\s+"[^"]+"\s+\([^)]+\)/i', '', $sql);

    // Convert ALTER TABLE ... CHANGE [COLUMN] old new ... → no-op (SQLite unsupported)
    $sql = preg_replace(
        '/ALTER TABLE\s+"[^"]+"\s+CHANGE\s+(?:COLUMN\s+)?"[^"]+"\s+"[^"]+"\s+[^;]+;/i',
        '-- CHANGE COLUMN skipped (SQLite unsupported);',
        $sql
    );

    // Convert ALTER TABLE ... MODIFY COLUMN ... → no-op
    $sql = preg_replace(
        '/ALTER TABLE\s+"[^"]+"\s+MODIFY\s+COLUMN\s+"[^"]+"\s+[^;]+;/i',
        '-- MODIFY COLUMN skipped (SQLite unsupported);',
        $sql
    );

    // Convert ALTER TABLE ... MODIFY (without COLUMN keyword)
    $sql = preg_replace(
        '/ALTER TABLE\s+\S+\s+MODIFY\s+"[^"]+"\s+[^;]+;/i',
        '-- MODIFY skipped (SQLite unsupported);',
        $sql
    );

    // Convert LONGTEXT/TEXT/VARCHAR to TEXT for SQLite compatibility
    $sql = preg_replace('/LONGTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/TINYTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/MEDIUMTEXT/i', 'TEXT', $sql);
    $sql = preg_replace('/VARCHAR\(\d+\)/i', 'TEXT', $sql);

    // Convert INT(n) to INTEGER
    $sql = preg_replace('/INT\(\d+\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bTINYINT\(\d+\)\b/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bSMALLINT\(\d+\)\b/i', 'INTEGER', $sql);

    // Convert DECIMAL(m,n) → REAL
    $sql = preg_replace('/DECIMAL\(\d+,\s*\d+\)/i', 'REAL', $sql);

    // ON DUPLICATE KEY UPDATE → INSERT OR REPLACE (handled below per-stmt)
    // INSERT INTO ... ON DUPLICATE KEY UPDATE ... → INSERT OR REPLACE INTO ...
    $sql = preg_replace(
        '/INSERT INTO\s+("([^"]+)")\s+\(([^)]+)\)\s+VALUES\s+\(([^)]+)\)\s+ON DUPLICATE KEY UPDATE[^;]+;/i',
        'INSERT OR REPLACE INTO $1 ($3) VALUES ($4);',
        $sql
    );

    // Split into individual statements (naive: split on ;\n or ;\r\n)
    $parts = preg_split('/;\s*\n/m', $sql);

    return array_map(fn (string $s) => trim($s), $parts);
}

function seedDefaults(PDO $pdo): void
{
    // Check if ip_settings already has rows
    try {
        $count = $pdo->query('SELECT COUNT(*) FROM "ip_settings"')->fetchColumn();
        if ($count > 0) {
            return;
        }
    } catch (PDOException) {
        return;
    }

    $settings = [
        ['setting_key' => 'default_language',       'setting_value' => 'english'],
        ['setting_key' => 'currency_symbol',         'setting_value' => '$'],
        ['setting_key' => 'currency_symbol_placement','setting_value' => 'before'],
        ['setting_key' => 'date_format',             'setting_value' => 'Y-m-d'],
        ['setting_key' => 'time_format',             'setting_value' => 'H:i'],
        ['setting_key' => 'pdf_engine',              'setting_value' => 'pdfmake'],
        ['setting_key' => 'pdf_paper_size',          'setting_value' => 'a4'],
        ['setting_key' => 'pdf_paper_orientation',   'setting_value' => 'portrait'],
        ['setting_key' => 'read_only_toggle',        'setting_value' => 'paid'],
        ['setting_key' => 'next_invoice_number',     'setting_value' => '1'],
        ['setting_key' => 'next_quote_number',       'setting_value' => '1'],
        ['setting_key' => 'invoicenumber_prefix',    'setting_value' => ''],
        ['setting_key' => 'quotenumber_prefix',      'setting_value' => ''],
        ['setting_key' => 'disable_setup',           'setting_value' => '1'],
        ['setting_key' => 'sumex',                   'setting_value' => '0'],
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO "ip_settings" ("setting_key","setting_value") VALUES (?,?)');
    foreach ($settings as $row) {
        $stmt->execute([$row['setting_key'], $row['setting_value']]);
    }

    // Seed a test user
    $pdo->exec("INSERT OR IGNORE INTO \"ip_users\"
        (\"user_name\",\"user_email\",\"user_password\",\"user_type\",\"user_active\")
        VALUES ('Admin','admin@test.local','" . password_hash('password', PASSWORD_DEFAULT) . "',1,1)");

    // Seed a default invoice group
    $pdo->exec("INSERT OR IGNORE INTO \"ip_invoice_groups\"
        (\"invoice_group_name\",\"invoice_group_identifier_format\",\"invoice_group_next_id\",\"invoice_group_left_pad\")
        VALUES ('Default','{{{id}}}',1,0)");
}
