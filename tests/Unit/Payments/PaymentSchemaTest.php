<?php

namespace Tests\Unit\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class PaymentSchemaTest extends AbstractTestCase
{
    #[Test]
    public function it_has_payment_external_id_column(): void
    {
        $this->assertTrue(
            $this->columnExists('ip_payments', 'payment_external_id'),
            'The payment_external_id column should exist in ip_payments table'
        );
    }

    #[Test]
    public function it_has_payment_external_id_index(): void
    {
        $this->assertTrue(
            $this->indexExists('ip_payments', 'idx_payment_external_id'),
            'The idx_payment_external_id index should exist in ip_payments table'
        );
    }

    private function columnExists(string $table, string $column): bool
    {
        $result = $this->db()->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", [$table, $column]);
        return $result !== false && $result->num_rows() > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = $this->db()->query("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?", [$table, $index]);
        return $result !== false && $result->num_rows() > 0;
    }

    private function db()
    {
        return $this->getCI()->db;
    }
}
