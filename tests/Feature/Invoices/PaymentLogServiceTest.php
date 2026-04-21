<?php

namespace Modules\Payments\Tests\Unit;

use Modules\Invoices\Models\Invoice;
use Modules\Payments\Models\PaymentLog;
use Modules\Payments\Services\PaymentLogService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(PaymentLogService::class)]

class PaymentLogServiceTest extends AbstractServiceTestCase
{
    private PaymentLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentLogService();
    }

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_payment_logs_with_relations_paginated(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        PaymentLog::factory()->count(3)->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(1),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $this->assertGreaterThanOrEqual(3, $result->total());
        $this->assertTrue($result->first()->relationLoaded('invoice'));
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_payment_logs_by_date_descending(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(3),
        ]);
        $log2 = PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(1),
        ]);
        PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(2),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /** Assert */
        $logs = $result->items();
        $this->assertGreaterThanOrEqual(3, count($logs));
        // Most recent should be first
        $this->assertEquals($log2->payment_log_id, $logs[0]->payment_log_id);
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->count(10)->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations(['invoice'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }

    #[Group('relationships')]
    #[Test]
    public function it_loads_custom_relations(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations(['invoice']);

        /* Assert */
        $this->assertTrue($result->first()->relationLoaded('invoice'));
        $this->assertNotNull($result->first()->invoice);
    }
}
