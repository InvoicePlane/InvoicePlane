<?php

declare(strict_types=1);

namespace Tests\Hmvc\Concerns;

trait InteractsWithDatabase
{
    protected function databaseInsert(string $table, array $row): int
    {
        $CI = &get_instance();

        if (!isset($CI->db)) {
            static::markTestSkipped(
                'Database connection unavailable. Ensure the test environment has a configured database.'
            );
        }

        $CI->db->insert($table, $row);

        $id = (int) $CI->db->insert_id();

        if ($id === 0) {
            static::fail(
                sprintf('INSERT into [%s] returned insert_id 0 — row was not persisted.', $table)
            );
        }

        return $id;
    }

    protected function databaseUpdate(string $table, array $set, array $where): void
    {
        $CI = &get_instance();

        if (!isset($CI->db)) {
            static::markTestSkipped('Database connection unavailable.');
        }

        $CI->db->update($table, $set, $where);
    }

    protected function databaseDelete(string $table, array $where): void
    {
        $CI = &get_instance();

        if (!isset($CI->db)) {
            static::markTestSkipped('Database connection unavailable.');
        }

        $CI->db->delete($table, $where);
    }

    protected function databaseFetchOne(string $table, array $where): ?array
    {
        $CI = &get_instance();

        if (!isset($CI->db)) {
            static::markTestSkipped('Database connection unavailable.');
        }

        $row = $CI->db->get_where($table, $where, 1)->row_array();

        return $row ?: null;
    }

    protected function assertDatabaseHas(string $table, array $conditions): void
    {
        $CI    = &get_instance();
        $query = $CI->db->get_where($table, $conditions);

        static::assertGreaterThan(
            0,
            $query->num_rows(),
            sprintf(
                'Failed asserting that table [%s] contains a row matching: %s',
                $table,
                json_encode($conditions, JSON_PRETTY_PRINT)
            )
        );
    }

    protected function assertDatabaseMissing(string $table, array $conditions): void
    {
        $CI    = &get_instance();
        $query = $CI->db->get_where($table, $conditions);

        static::assertSame(
            0,
            $query->num_rows(),
            sprintf(
                'Failed asserting that table [%s] does NOT contain a row matching: %s',
                $table,
                json_encode($conditions, JSON_PRETTY_PRINT)
            )
        );
    }

    protected function assertDatabaseCount(string $table, int $expected, array $conditions = []): void
    {
        $CI = &get_instance();

        $actual = empty($conditions)
            ? $CI->db->count_all($table)
            : $CI->db->where($conditions)->count_all_results($table);

        static::assertSame(
            $expected,
            (int) $actual,
            sprintf(
                'Expected [%d] rows in table [%s] matching %s but found [%d].',
                $expected,
                $table,
                empty($conditions) ? '(no conditions)' : json_encode($conditions),
                $actual
            )
        );
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
}
