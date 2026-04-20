<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    /**
     * Setup the test environment.
     *
     * Note: Since unit tests extend PHPUnit\Framework\TestCase (not Laravel's TestCase),
     * Laravel components are bootstrapped via tests/bootstrap.php which is loaded by PHPUnit.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Clean up database tables commonly used in tests.
     *
     * @param  array  $tables
     * @return void
     */
    protected function cleanupTables(array $tables): void
    {
        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::table($table)->delete();
        }
    }

    /**
     * Create a test invoice with common defaults.
     *
     * @param  array  $overrides
     * @return \Modules\Invoices\Models\Invoice
     */
    protected function createTestInvoice(array $overrides = [])
    {
        $defaults = [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-TEST-' . uniqid(),
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-' . uniqid(),
        ];

        return \Modules\Invoices\Models\Invoice::query()->create(
            array_merge($defaults, $overrides)
        );
    }

    /**
     * Create a test invoice item with common defaults.
     *
     * @param  int  $invoiceId
     * @param  array  $overrides
     * @return \Modules\Invoices\Models\Item
     */
    protected function createTestItem(int $invoiceId, array $overrides = [])
    {
        $defaults = [
            'invoice_id'           => $invoiceId,
            'item_tax_rate_id'     => null,
            'item_product_id'      => null,
            'item_name'            => 'Test Item',
            'item_description'     => 'Test Description',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
            'item_product_unit'    => null,
            'item_product_unit_id' => null,
        ];

        return \Modules\Invoices\Models\Item::query()->create(
            array_merge($defaults, $overrides)
        );
    }

    /**
     * Create a test quote with common defaults.
     *
     * @param  array  $overrides
     * @return \Modules\Quotes\Models\Quote
     */
    protected function createTestQuote(array $overrides = [])
    {
        $defaults = [
            'client_id'              => 1,
            'user_id'                => 1,
            'invoice_group_id'       => 1,
            'quote_status_id'        => 1,
            'quote_number'           => 'QUO-TEST-' . uniqid(),
            'quote_date_created'     => '2024-01-01',
            'quote_date_modified'    => '2024-01-01',
            'quote_date_expires'     => '2024-01-31',
            'quote_password'         => '',
            'quote_discount_amount'  => 0,
            'quote_discount_percent' => 0,
            'quote_terms'            => '',
            'quote_url_key'          => 'key-' . uniqid(),
        ];

        return \Modules\Quotes\Models\Quote::query()->create(
            array_merge($defaults, $overrides)
        );
    }
}
