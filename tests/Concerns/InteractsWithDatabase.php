<?php

namespace Tests\Concerns;

use mysqli;

trait InteractsWithDatabase
{
    private static ?mysqli $testDb = null;

    protected function databaseInsert(string $table, array $row): int
    {
        $db = $this->db();

        $columns = array_keys($row);
        $values  = array_values($row);

        $escapedColumns = array_map(static fn ($column) => '`' . $column . '`', $columns);
        $escapedValues  = array_map([$db, 'real_escape_string'], array_map('strval', $values));
        $wrappedValues  = array_map(static fn ($value) => "'{$value}'", $escapedValues);

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $escapedColumns),
            implode(', ', $wrappedValues)
        );

        if ( ! $db->query($sql)) {
            static::fail('Failed inserting test row: ' . $db->error);
        }

        return (int) $db->insert_id;
    }

    protected function databaseUpdate(string $table, array $set, array $where): void
    {
        $db = $this->db();

        $setParts = [];
        foreach ($set as $key => $value) {
            $setParts[] = sprintf("`%s`='%s'", $key, $db->real_escape_string((string) $value));
        }

        $whereParts = [];
        foreach ($where as $key => $value) {
            $whereParts[] = sprintf("`%s`='%s'", $key, $db->real_escape_string((string) $value));
        }

        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, implode(', ', $setParts), implode(' AND ', $whereParts));
        $db->query($sql);
    }

    protected function databaseDelete(string $table, array $where): void
    {
        $db = $this->db();

        $whereParts = [];
        foreach ($where as $key => $value) {
            $whereParts[] = sprintf("`%s`='%s'", $key, $db->real_escape_string((string) $value));
        }

        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $whereParts));
        $db->query($sql);
    }

    protected function databaseFetchOne(string $table, array $where): ?array
    {
        $db = $this->db();

        $whereParts = [];
        foreach ($where as $key => $value) {
            $whereParts[] = sprintf("`%s`='%s'", $key, $db->real_escape_string((string) $value));
        }

        $sql    = sprintf('SELECT * FROM `%s` WHERE %s LIMIT 1', $table, implode(' AND ', $whereParts));
        $result = $db->query($sql);
        $row    = $result ? $result->fetch_assoc() : false;

        return $row ?: null;
    }

    protected function assertDatabaseHas(string $table, array $conditions): void
    {
        static::assertNotNull($this->databaseFetchOne($table, $conditions));
    }

    protected function assertDatabaseMissing(string $table, array $conditions): void
    {
        static::assertNull($this->databaseFetchOne($table, $conditions));
    }

    protected function assertDatabaseCount(string $table, int $expected, array $conditions = []): void
    {
        $db = $this->db();

        $sql = sprintf('SELECT COUNT(*) AS c FROM `%s`', $table);
        if ($conditions !== []) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = sprintf("`%s`='%s'", $key, $db->real_escape_string((string) $value));
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }

        $result = $db->query($sql);
        $count  = (int) (($result ? $result->fetch_assoc()['c'] : 0));

        static::assertSame($expected, $count);
    }

    protected function seedClient(array $overrides = []): int
    {
        return $this->databaseInsert('ip_clients', array_merge([
            'client_name'          => 'Test Client ' . bin2hex(random_bytes(4)),
            'client_address_1'     => '1 Test Street',
            'client_city'          => 'Testville',
            'client_country'       => 'NL',
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    protected function seedInvoice(int $clientId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_invoices', array_merge([
            'user_id'              => 1,
            'client_id'            => $clientId,
            'invoice_status_id'    => 1,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due'     => date('Y-m-d', strtotime('+30 days')),
            'invoice_number'       => 'INV-' . time() . '-' . random_int(100, 999),
            'invoice_terms'        => '',
            'invoice_url_key'      => bin2hex(random_bytes(16)),
        ], $overrides));
    }

    protected function seedPayment(int $invoiceId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_payments', array_merge([
            'invoice_id'           => $invoiceId,
            'payment_method_id'    => 1,
            'payment_amount'       => '100.00',
            'payment_date'         => date('Y-m-d'),
            'payment_note'         => '',
            'payment_date_created' => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    private function db(): mysqli
    {
        if (self::$testDb instanceof mysqli) {
            return self::$testDb;
        }

        $basePath = dirname(__DIR__, 2);
        require_once $basePath . '/bootstrap/kernel.php';

        $active_group = null;
        $db           = [];
        require $basePath . '/application/config/database.php';

        $group = $active_group ?? 'default';
        $cfg   = $db[$group] ?? [];

        self::$testDb = new mysqli(
            (string) ($cfg['hostname'] ?? '127.0.0.1'),
            (string) ($cfg['username'] ?? ''),
            (string) ($cfg['password'] ?? ''),
            (string) ($cfg['database'] ?? ''),
            (int) ($cfg['port'] ?? 3306),
        );

        if (self::$testDb->connect_errno) {
            static::markTestSkipped('Database unavailable for integration tests: ' . self::$testDb->connect_error);
        }

        return self::$testDb;
    }
}
