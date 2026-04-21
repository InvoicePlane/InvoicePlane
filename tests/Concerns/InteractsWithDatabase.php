<?php

namespace Tests\Concerns;

use InvalidArgumentException;
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
            'PaymentLog'                                 => ['ip_payment_logs', 'payment_log_id'],
            'Modules\\Payments\\Models\\PaymentLog'      => ['ip_payment_logs', 'payment_log_id'],
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
                'invoice_status_id'    => 1,
                'invoice_date_created' => date('Y-m-d'),
                'invoice_date_due'     => date('Y-m-d', strtotime('+30 days')),
                'invoice_number'       => 'INV-' . time() . '-' . random_int(100, 999),
                'invoice_url_key'      => bin2hex(random_bytes(16)),
            ],
            'ip_projects' => [
                'client_id'            => (string) ($overrides['client_id'] ?? 1),
                'project_name'         => 'Test Project ' . bin2hex(random_bytes(3)),
                'project_date_created' => date('Y-m-d'),
            ],
            'ip_tasks' => [
                'task_name'       => 'Test Task ' . bin2hex(random_bytes(3)),
                'task_date_added' => date('Y-m-d H:i:s'),
                'task_status'     => 1,
                'project_id'      => (string) ($overrides['project_id'] ?? 0),
            ],
            'ip_quotes' => [
                'client_id'          => (string) ($overrides['client_id'] ?? 1),
                'quote_date_created' => date('Y-m-d'),
                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
                'quote_number'       => 'QUO-' . time() . '-' . random_int(100, 999),
                'quote_url_key'      => bin2hex(random_bytes(16)),
            ],
            'ip_users' => [
                'user_name'     => 'test_' . bin2hex(random_bytes(3)),
                'user_email'    => 'test+' . bin2hex(random_bytes(3)) . '@example.com',
                'user_password' => password_hash('secret', PASSWORD_DEFAULT),
                'user_active'   => 1,
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
