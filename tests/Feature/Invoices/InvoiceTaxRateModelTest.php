<?php

namespace Tests\Feature\Invoices;

use Mdl_Invoice_Tax_Rates;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Invoice_Tax_Rates::class)]
class InvoiceTaxRateModelTest extends CiTestCase
{
    use InteractsWithDatabase;

    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoice_tax_rates');
        $this->model = $this->CI->mdl_invoice_tax_rates;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        $this->assertEquals('ip_invoice_tax_rates', $this->model->table);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_in_legacy_mode(): void
    {
        $this->skipWithoutDatabase();
        $client   = $this->seedModel('Client');
        $invoice  = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $taxRate  = $this->seedModel('TaxRate');
        $inserted = $this->seedModel('InvoiceTaxRate', [
            'invoice_id'  => $invoice->invoice_id,
            'tax_rate_id' => $taxRate->tax_rate_id,
        ]);

        $this->assertDatabaseHas('ip_invoice_tax_rates', [
            'invoice_id'  => $invoice->invoice_id,
            'tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_tax_rate_in_legacy_mode(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $taxRate = $this->seedModel('TaxRate');
        $record  = $this->seedModel('InvoiceTaxRate', [
            'invoice_id'       => $invoice->invoice_id,
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'include_item_tax' => 0,
        ]);

        $this->databaseUpdate(
            'ip_invoice_tax_rates',
            ['include_item_tax' => 1],
            ['invoice_tax_rate_id' => $record->invoice_tax_rate_id]
        );

        $updated = $this->databaseFetchOne('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $record->invoice_tax_rate_id]);
        $this->assertEquals(1, (int) $updated['include_item_tax']);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_include_item_tax_flag(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('include_item_tax', $rules);
        $this->assertArrayHasKey('rules', $rules['include_item_tax']);
        $this->assertStringContainsString('required', $rules['include_item_tax']['rules']);
    }
}
