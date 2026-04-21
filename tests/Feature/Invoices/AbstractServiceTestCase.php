<?php

namespace Tests;

use Modules\Core\Models\Setting;
use Modules\Crm\Models\Client;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceGroup;
use Modules\Invoices\Models\Item;
use Modules\Invoices\Models\ItemAmount;
use Modules\Quotes\Models\Quote;
use Tests\Unit\UnitTestCase;

/**
 * Abstract base class for service tests with shared fixtures and utilities.
 */
abstract class AbstractServiceTestCase extends UnitTestCase
{
    /**
     * Create a test invoice with common defaults.
     *
     * @param array $overrides
     *
     * @return Invoice
     */
    protected function createInvoiceFixture(array $overrides = []): Invoice
    {
        $defaults = [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-' . uniqid(),
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-' . uniqid(),
        ];

        return Invoice::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test invoice item with common defaults.
     *
     * @param int   $invoiceId
     * @param array $overrides
     *
     * @return Item
     */
    protected function createItemFixture(int $invoiceId, array $overrides = []): Item
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

        return Item::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test item amount with common defaults.
     *
     * @param int   $itemId
     * @param array $overrides
     *
     * @return ItemAmount
     */
    protected function createItemAmountFixture(int $itemId, array $overrides = []): ItemAmount
    {
        $defaults = [
            'item_id'        => $itemId,
            'item_subtotal'  => 100,
            'item_tax_total' => 10,
            'item_discount'  => 0,
            'item_total'     => 110,
        ];

        return ItemAmount::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test quote with common defaults.
     *
     * @param array $overrides
     *
     * @return Quote
     */
    protected function createQuoteFixture(array $overrides = []): Quote
    {
        $defaults = [
            'client_id'              => 1,
            'user_id'                => 1,
            'quote_group_id'         => 1,
            'quote_status_id'        => 1,
            'quote_number'           => 'QUO-' . uniqid(),
            'quote_date_created'     => '2024-01-01',
            'quote_date_modified'    => '2024-01-01',
            'quote_date_expires'     => '2024-01-31',
            'quote_password'         => '',
            'quote_discount_amount'  => 0,
            'quote_discount_percent' => 0,
            'quote_terms'            => '',
            'quote_url_key'          => 'key-' . uniqid(),
        ];

        return Quote::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test invoice group with common defaults.
     *
     * @param array $overrides
     *
     * @return InvoiceGroup
     */
    protected function createInvoiceGroupFixture(array $overrides = []): InvoiceGroup
    {
        $defaults = [
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ];

        return InvoiceGroup::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test client with common defaults.
     *
     * @param array $overrides
     *
     * @return Client
     */
    protected function createClientFixture(array $overrides = []): Client
    {
        $defaults = [
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ];

        return Client::query()->create(array_merge($defaults, $overrides));
    }

    /**
     * Set a setting value for tests.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function setSettingFixture(string $key, $value): void
    {
        Setting::setValue($key, $value);
    }

    /**
     * Clean up common invoice-related tables.
     *
     * @return void
     */
    protected function cleanupInvoiceTables(): void
    {
        $this->cleanupTables([
            'ip_invoice_amounts',
            'ip_invoice_item_amounts',
            'ip_invoice_items',
            'ip_invoice_tax_rates',
            'ip_payments',
            'ip_invoices',
            'ip_invoice_groups',
        ]);
    }

    /**
     * Clean up common quote-related tables.
     *
     * @return void
     */
    protected function cleanupQuoteTables(): void
    {
        $this->cleanupTables([
            'ip_quote_amounts',
            'ip_quote_item_amounts',
            'ip_quote_items',
            'ip_quote_tax_rates',
            'ip_quotes',
            'ip_quote_groups',
        ]);
    }
}
