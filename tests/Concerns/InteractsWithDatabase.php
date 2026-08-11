<?php

namespace Tests\Concerns;

use InvalidArgumentException;
use PDO;
use PDOException;

trait InteractsWithDatabase
{
    private static ?PDO $testDb = null;

    /**
     * Reset the MariaDB test database to a clean baseline before each test.
     *
     * Truncates every table and reseeds the baseline rows, giving each test a
     * fresh slate. MariaDB is the only supported test database (it exercises the
     * same SQL the application runs in production — NOW(), IF(), DATEDIFF(), …);
     * db() rejects any other driver.
     */
    protected function setUpDatabase(): void
    {
        $this->resetMysqlDatabase();
    }

    /**
     * Drop the cached PDO connection so the next DB call opens a fresh one.
     *
     * Use this after an HTTP subprocess writes to the database so the test
     * process reconnects and sees every row the subprocess committed.
     */
    protected function resetDatabaseConnection(): void
    {
        self::$testDb = null;
    }

    protected function databaseInsert(string $table, array $row): int
    {
        $db = $this->db();

        $columns      = array_keys($row);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);

        $quotedTable   = $this->qi($table);
        $quotedColumns = implode(', ', array_map($this->qi(...), $columns));

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $quotedTable,
            $quotedColumns,
            implode(', ', $placeholders)
        );
        $stmt = $db->prepare($sql);
        if ( ! $stmt->execute($row)) {
            static::fail('Failed inserting test row into ' . $table);
        }

        return (int) $db->lastInsertId();
    }

    protected function databaseInsertOrIgnore(string $table, array $row): void
    {
        $db = $this->db();

        $columns      = array_keys($row);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);

        $quotedTable   = $this->qi($table);
        $quotedColumns = implode(', ', array_map($this->qi(...), $columns));

        $sql = sprintf(
            'INSERT IGNORE INTO %s (%s) VALUES (%s)',
            $quotedTable,
            $quotedColumns,
            implode(', ', $placeholders)
        );
        $db->prepare($sql)->execute($row);
    }

    protected function databaseUpdate(string $table, array $set, array $where): void
    {
        $db = $this->db();

        $setParts = [];
        $params   = [];
        foreach ($set as $key => $value) {
            $setParts[]            = $this->qi($key) . ' = :set_' . $key;
            $params['set_' . $key] = $value;
        }

        $whereParts = [];
        foreach ($where as $key => $value) {
            $whereParts[]         = $this->qi($key) . ' = :wh_' . $key;
            $params['wh_' . $key] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->qi($table),
            implode(', ', $setParts),
            implode(' AND ', $whereParts)
        );
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    protected function databaseTruncate(string $table): void
    {
        $this->db()->exec('DELETE FROM ' . $this->qi($table));
    }

    /**
     * Fetch a single row by its insert id (the `id` AUTO_INCREMENT column).
     */
    protected function databaseFetchByRowid(string $table, int $rowid): array
    {
        $db = $this->db();

        $stmt = $db->prepare(
            'SELECT * FROM ' . $this->qi($table) . ' WHERE ' . $this->qi('id') . ' = ?'
        );
        $stmt->execute([$rowid]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected function databaseDelete(string $table, array $where): void
    {
        $db = $this->db();

        $whereParts = [];
        $params     = [];
        foreach ($where as $key => $value) {
            $whereParts[] = $this->qi($key) . ' = :' . $key;
            $params[$key] = $value;
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $this->qi($table),
            implode(' AND ', $whereParts)
        );
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    protected function databaseFetchOne(string $table, array $where): ?array
    {
        $db = $this->db();

        $whereParts = [];
        $params     = [];
        foreach ($where as $key => $value) {
            $whereParts[] = $this->qi($key) . ' = :' . $key;
            $params[$key] = $value;
        }

        $sql = sprintf(
            'SELECT * FROM %s WHERE %s LIMIT 1',
            $this->qi($table),
            implode(' AND ', $whereParts)
        );
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    protected function assertDatabaseHas(string $table, array $conditions): void
    {
        static::assertNotNull(
            $this->databaseFetchOne($table, $conditions),
            "Expected row in [{$table}] not found."
        );
    }

    /** Assert the complete stored row, not merely that a matching row exists. */
    protected function assertDatabaseRow(string $table, array $conditions, array $expected): void
    {
        $row = $this->databaseFetchOne($table, $conditions);
        static::assertNotNull($row, "Expected row in [{$table}] not found.");
        static::assertSame($expected, array_intersect_key($row, $expected));
    }

    /** @return array<int, array<string, mixed>> */
    protected function databaseSnapshot(string $table, array $where = []): array
    {
        $db = $this->db();
        $sql = 'SELECT * FROM ' . $this->qi($table);
        $params = [];
        if ($where !== []) {
            $parts = [];
            foreach ($where as $key => $value) {
                $parts[] = $this->qi($key) . ' = :' . $key;
                $params[$key] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $parts);
        }
        $sql .= ' ORDER BY 1';
        $statement = $db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function assertDatabaseMissing(string $table, array $conditions): void
    {
        static::assertNull(
            $this->databaseFetchOne($table, $conditions),
            "Unexpected row found in [{$table}]."
        );
    }

    protected function assertDatabaseCount(string $table, int $expected, array $conditions = []): void
    {
        $db     = $this->db();
        $params = [];

        $sql = sprintf('SELECT COUNT(*) AS c FROM %s', $this->qi($table));
        if ($conditions !== []) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = $this->qi($key) . ' = :' . $key;
                $params[$key] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $count = (int) $stmt->fetchColumn();

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

    protected function seedInvoice(int $clientId, array $overrides = [], array $amountOverrides = []): int
    {
        $invoiceId = $this->databaseInsert('ip_invoices', array_merge([
            'user_id'                  => 1,
            'client_id'                => $clientId,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'payment_method'           => 1,
            'invoice_discount_amount'  => '0.00',
            'invoice_discount_percent' => '0.00',
            'invoice_number'           => 'INV-' . time() . '-' . random_int(100, 999),
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ], $overrides));

        // ip_invoice_amounts is required for INNER JOIN queries on this invoice
        $this->databaseInsert('ip_invoice_amounts', array_merge([
            'invoice_id'             => $invoiceId,
            'invoice_item_subtotal'  => '0.00',
            'invoice_item_tax_total' => '0.00',
            'invoice_tax_total'      => '0.00',
            'invoice_total'          => '0.00',
            'invoice_paid'           => '0.00',
            'invoice_balance'        => '0.00',
            'invoice_sign'           => 1,
        ], array_merge(['invoice_id' => $invoiceId], $amountOverrides)));

        return $invoiceId;
    }

    protected function seedPayment(int $invoiceId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_payments', array_merge([
            'invoice_id'        => $invoiceId,
            'payment_method_id' => 1,
            'payment_amount'    => '100.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => '',
        ], $overrides));
    }

    /**
     * Seed a supported model and return the inserted row as an object.
     *
     * @param class-string $modelClass
     */
    protected function seedModel(string $modelClass, array $overrides = []): object
    {
        [$table, $idColumn] = $this->resolveModelTableAndId($modelClass);
        $row                = $this->buildDefaultSeedRow($table, $overrides);
        $id                 = $this->databaseInsert($table, $row);

        $stored = $this->databaseFetchOne($table, [$idColumn => $id]);

        return (object) ($stored ?? array_merge([$idColumn => $id], $row));
    }

    /**
     * Seed multiple rows for a supported model.
     *
     * @param class-string $modelClass
     *
     * @return array<int, object>
     */
    protected function seedModelMany(string $modelClass, int $count, array $overrides = []): array
    {
        $items = [];

        for ($index = 0; $index < $count; $index++) {
            $items[] = $this->seedModel($modelClass, $overrides);
        }

        return $items;
    }

    /**
     * Truncate all application tables and reseed the baseline rows so each
     * MariaDB test starts from the same clean state.
     */
    private function resetMysqlDatabase(): void
    {
        $db = $this->db();

        $db->exec('SET FOREIGN_KEY_CHECKS = 0');
        $tables = $db->query(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()'
        )->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $db->exec('TRUNCATE TABLE ' . $this->qi((string) $table));
        }
        $db->exec('SET FOREIGN_KEY_CHECKS = 1');

        require_once dirname(__DIR__) . '/Support/seed_baseline.php';
        ip_seed_baseline($db);
    }

    /**
     * @param class-string $modelClass
     *
     * @return array{0: string, 1: string}
     */
    private function resolveModelTableAndId(string $modelClass): array
    {
        $map = [
            'Client'                                     => ['ip_clients', 'client_id'],
            'Modules\\Crm\\Models\\Client'               => ['ip_clients', 'client_id'],
            'Invoice'                                    => ['ip_invoices', 'invoice_id'],
            'Modules\\Invoices\\Models\\Invoice'         => ['ip_invoices', 'invoice_id'],
            'Item'                                       => ['ip_invoice_items', 'item_id'],
            'Modules\\Invoices\\Models\\Item'            => ['ip_invoice_items', 'item_id'],
            'InvoiceItem'                                => ['ip_invoice_items', 'item_id'],
            'ItemAmount'                                 => ['ip_invoice_item_amounts', 'item_amount_id'],
            'Modules\\Invoices\\Models\\ItemAmount'      => ['ip_invoice_item_amounts', 'item_amount_id'],
            'InvoiceGroup'                               => ['ip_invoice_groups', 'invoice_group_id'],
            'Modules\\Invoices\\Models\\InvoiceGroup'    => ['ip_invoice_groups', 'invoice_group_id'],
            'InvoiceTaxRate'                             => ['ip_invoice_tax_rates', 'invoice_tax_rate_id'],
            'Modules\\Invoices\\Models\\InvoiceTaxRate'  => ['ip_invoice_tax_rates', 'invoice_tax_rate_id'],
            'InvoicesRecurring'                          => ['ip_invoices_recurring', 'invoice_recurring_id'],
            'RecurringInvoice'                           => ['ip_invoices_recurring', 'invoice_recurring_id'],
            'Product'                                    => ['ip_products', 'product_id'],
            'Modules\\Products\\Models\\Product'         => ['ip_products', 'product_id'],
            'Family'                                     => ['ip_families', 'family_id'],
            'Modules\\Products\\Models\\Family'          => ['ip_families', 'family_id'],
            'Unit'                                       => ['ip_units', 'unit_id'],
            'Modules\\Products\\Models\\Unit'            => ['ip_units', 'unit_id'],
            'Project'                                    => ['ip_projects', 'project_id'],
            'Modules\\Projects\\Models\\Project'         => ['ip_projects', 'project_id'],
            'Task'                                       => ['ip_tasks', 'task_id'],
            'Modules\\Projects\\Models\\Task'            => ['ip_tasks', 'task_id'],
            'Quote'                                      => ['ip_quotes', 'quote_id'],
            'Modules\\Quotes\\Models\\Quote'             => ['ip_quotes', 'quote_id'],
            'QuoteItem'                                  => ['ip_quote_items', 'item_id'],
            'QuoteTaxRate'                               => ['ip_quote_tax_rates', 'quote_tax_rate_id'],
            'User'                                       => ['ip_users', 'user_id'],
            'AuthUser'                                   => ['ip_users', 'user_id'],
            'Modules\\Core\\Models\\User'                => ['ip_users', 'user_id'],
            'Modules\\Users\\Models\\User'               => ['ip_users', 'user_id'],
            'Payment'                                    => ['ip_payments', 'payment_id'],
            'Modules\\Payments\\Models\\Payment'         => ['ip_payments', 'payment_id'],
            'PaymentMethod'                              => ['ip_payment_methods', 'payment_method_id'],
            'Modules\\Payments\\Models\\PaymentMethod'   => ['ip_payment_methods', 'payment_method_id'],
            'TaxRate'                                    => ['ip_tax_rates', 'tax_rate_id'],
            'Modules\\Core\\Models\\TaxRate'             => ['ip_tax_rates', 'tax_rate_id'],
            'Modules\\Products\\Models\\TaxRate'         => ['ip_tax_rates', 'tax_rate_id'],
            'CustomField'                                => ['ip_custom_fields', 'custom_field_id'],
            'Modules\\CustomFields\\Models\\CustomField' => ['ip_custom_fields', 'custom_field_id'],
            'CustomValue'                                => ['ip_custom_values', 'custom_value_id'],
            'EmailTemplate'                              => ['ip_email_templates', 'email_template_id'],
            'UserClient'                                 => ['ip_user_clients', 'user_client_id'],
            '\\Modules\\Crm\\app\\Models\\ClientNote'    => ['ip_client_notes', 'client_note_id'],
        ];

        if ( ! array_key_exists($modelClass, $map)) {
            throw new InvalidArgumentException(
                "Unknown model class '{$modelClass}' in resolveModelTableAndId(). "
                . 'Add an entry to the map or use a fully-qualified class name.'
            );
        }

        return $map[$modelClass];
    }

    private function buildDefaultSeedRow(string $table, array $overrides): array
    {
        $defaults = match ($table) {
            'ip_clients' => [
                'client_name'          => 'Test Client ' . bin2hex(random_bytes(4)),
                'client_active'        => 1,
                'client_date_created'  => date('Y-m-d H:i:s'),
                'client_date_modified' => date('Y-m-d H:i:s'),
            ],
            'ip_invoices' => [
                'user_id'              => 1,
                'client_id'            => (string) ($overrides['client_id'] ?? 1),
                'invoice_group_id'     => (string) ($overrides['invoice_group_id'] ?? 1),
                'invoice_status_id'    => 1,
                'invoice_date_created' => date('Y-m-d'),
                'invoice_date_due'     => date('Y-m-d', strtotime('+30 days')),
                'invoice_number'       => 'INV-' . time() . '-' . random_int(100, 999),
                'invoice_url_key'      => bin2hex(random_bytes(16)),
            ],
            'ip_projects' => [
                'client_id'    => (string) ($overrides['client_id'] ?? 1),
                'project_name' => 'Test Project ' . bin2hex(random_bytes(3)),
            ],
            'ip_tasks' => [
                'task_name'        => 'Test Task ' . bin2hex(random_bytes(3)),
                'task_description' => '',
                'task_price'       => '0.00',
                'task_finish_date' => date('Y-m-d'),
                'task_status'      => 1,
                'project_id'       => (string) ($overrides['project_id'] ?? 0),
            ],
            'ip_quotes' => [
                'client_id'           => (string) ($overrides['client_id'] ?? 1),
                'user_id'             => 1,
                'invoice_group_id'    => 1,
                'quote_date_created'  => date('Y-m-d'),
                'quote_date_modified' => date('Y-m-d'),
                'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),
                'quote_number'        => 'QUO-' . time() . '-' . random_int(100, 999),
                'quote_url_key'       => bin2hex(random_bytes(16)),
            ],
            'ip_users' => [
                'user_name'          => 'test_' . bin2hex(random_bytes(3)),
                'user_email'         => 'test+' . bin2hex(random_bytes(3)) . '@example.com',
                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
                'user_psalt'         => bin2hex(random_bytes(10)),
                'user_type'          => 1,
                'user_active'        => 1,
                'user_date_created'  => date('Y-m-d H:i:s'),
                'user_date_modified' => date('Y-m-d H:i:s'),
            ],
            'ip_tax_rates' => [
                'tax_rate_name'    => 'Test Rate ' . bin2hex(random_bytes(2)),
                'tax_rate_percent' => 10.00,
            ],
            'ip_invoice_items' => [
                'invoice_id'           => (string) ($overrides['invoice_id'] ?? 1),
                'item_name'            => 'Test Item ' . bin2hex(random_bytes(2)),
                'item_quantity'        => 1,
                'item_price'           => '100.00',
                'item_order'           => 1,
                'item_discount_amount' => '0.00',
            ],
            'ip_quote_items' => [
                'quote_id'             => (string) ($overrides['quote_id'] ?? 1),
                'item_name'            => 'Test Quote Item ' . bin2hex(random_bytes(2)),
                'item_quantity'        => 1,
                'item_price'           => '100.00',
                'item_order'           => 1,
                'item_discount_amount' => '0.00',
            ],
            'ip_invoice_groups' => [
                'invoice_group_name'              => 'Test Group ' . bin2hex(random_bytes(2)),
                'invoice_group_identifier_format' => 'INV-{{{id}}}',
                'invoice_group_next_id'           => 1,
                'invoice_group_left_pad'          => 4,
            ],
            'ip_invoice_tax_rates' => [
                'invoice_id'               => (string) ($overrides['invoice_id'] ?? 1),
                'tax_rate_id'              => (string) ($overrides['tax_rate_id'] ?? 1),
                'include_item_tax'         => 0,
                'invoice_tax_rate_percent' => '10.00',
                'invoice_tax_rate_amount'  => '0.00',
            ],
            'ip_quote_tax_rates' => [
                'quote_id'               => (string) ($overrides['quote_id'] ?? 1),
                'tax_rate_id'            => (string) ($overrides['tax_rate_id'] ?? 1),
                'include_item_tax'       => 0,
                'quote_tax_rate_percent' => '10.00',
                'quote_tax_rate_amount'  => '0.00',
            ],
            'ip_invoices_recurring' => [
                'invoice_id'       => (string) ($overrides['invoice_id'] ?? 1),
                'recur_start_date' => date('Y-m-d'),
                'recur_next_date'  => date('Y-m-d'),
                'recur_frequency'  => 3,
                'recur_status'     => (string) ($overrides['recur_status'] ?? 0),
            ],
            'ip_custom_fields' => [
                'custom_field_entity' => 'clients',
                'custom_field_name'   => 'Test Field ' . bin2hex(random_bytes(2)),
                'custom_field_type'   => 'text',
            ],
            'ip_custom_values' => [
                'custom_field_id'     => (string) ($overrides['custom_field_id'] ?? 1),
                'custom_field_entity' => 'clients',
                'entity_id'           => (string) ($overrides['entity_id'] ?? 1),
                'custom_value'        => 'Test Value',
            ],
            'ip_email_templates' => [
                'email_template_subject' => 'Test Subject ' . bin2hex(random_bytes(2)),
                'email_template_body'    => 'Test body',
                'email_template_pdf'     => 0,
            ],
            'ip_user_clients' => [
                'user_id'   => (string) ($overrides['user_id'] ?? 1),
                'client_id' => (string) ($overrides['client_id'] ?? 1),
            ],
            'ip_client_notes' => [
                'client_id'                => (string) ($overrides['client_id'] ?? 1),
                'client_note'              => 'Test note ' . bin2hex(random_bytes(2)),
                'client_note_date_created' => date('Y-m-d H:i:s'),
            ],
            default => [],
        };

        return array_merge($defaults, $overrides);
    }

    private function db(): PDO
    {
        if (self::$testDb instanceof PDO) {
            return self::$testDb;
        }

        $basePath = dirname(__DIR__, 2);
        require_once $basePath . '/bootstrap/kernel.php';

        $active_group = null;
        $db           = [];
        require $basePath . '/application/config/database.php';

        $group  = $active_group ?? 'default';
        $cfg    = $db[$group] ?? [];
        $driver = (string) ($cfg['dbdriver'] ?? 'mysqli');

        // MariaDB/MySQL is the only supported test database. Fail loudly rather
        // than silently falling back, so a stray dbdriver can never let the
        // suite pass against a database the application never runs on.
        if ( ! in_array($driver, ['mysqli', 'mysql', 'pdo'], true)) {
            static::fail(sprintf(
                'Unsupported test database driver "%s": the test suite requires MariaDB/MySQL. '
                . 'Set DB_DRIVER=mysqli and the DB_* connection variables.',
                $driver
            ));
        }

        $host = (string) ($cfg['hostname'] ?? '127.0.0.1');
        $port = (int) ($cfg['port'] ?? 3306);
        $user = (string) ($cfg['username'] ?? '');
        $pass = (string) ($cfg['password'] ?? '');
        $name = (string) ($cfg['database'] ?? '');

        try {
            self::$testDb = new PDO(
                sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8', $host, $port, $name),
                $user,
                $pass,
            );
            self::$testDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            static::markTestSkipped('Database unavailable for integration tests: ' . $e->getMessage());
        }

        return self::$testDb;
    }

    /**
     * Quote a single identifier with MariaDB/MySQL backticks.
     */
    private function qi(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
