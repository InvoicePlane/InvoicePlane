<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Invoices;

use Modules\Invoices\Services\InvoiceAmountService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(InvoiceAmountService::class)]
#[CoversClass(Tests\Feature\Invoices\InvoicesRecurringService::class)]

class InvoicesRecurringModelTest extends AbstractTestCase
{
    private InvoicesRecurringService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new InvoicesRecurringService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('recur_start_date', $rules);
        $this->assertArrayHasKey('recur_end_date', $rules);
        $this->assertArrayHasKey('recur_frequency', $rules);
        $this->assertArrayHasKey('recur_next_date', $rules);
    }

    #[Test]
    public function it_validates_invoice_id_as_required_integer(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertStringContainsString('required', $rules['invoice_id']);
        $this->assertStringContainsString('integer', $rules['invoice_id']);
    }

    #[Test]
    public function it_validates_recur_start_date_as_required_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertStringContainsString('required', $rules['recur_start_date']);
        $this->assertStringContainsString('date', $rules['recur_start_date']);
    }

    #[Test]
    public function it_validates_recur_end_date_as_nullable_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertStringContainsString('nullable', $rules['recur_end_date']);
        $this->assertStringContainsString('date', $rules['recur_end_date']);
    }

    #[Test]
    public function it_validates_recur_frequency_as_required_string(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertStringContainsString('required', $rules['recur_frequency']);
        $this->assertStringContainsString('string', $rules['recur_frequency']);
    }

    #[Test]
    public function it_validates_recur_next_date_as_nullable_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $this->assertStringContainsString('nullable', $rules['recur_next_date']);
        $this->assertStringContainsString('date', $rules['recur_next_date']);
    }

    #[Test]
    public function it_provides_all_required_validation_keys(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->getValidationRules();

        $expectedKeys = [
            'invoice_id',
            'recur_start_date',
            'recur_end_date',
            'recur_frequency',
            'recur_next_date',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $rules);
        }
    }
}
