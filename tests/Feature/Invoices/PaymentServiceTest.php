<?php

namespace Tests\Feature\Invoices;

use Tests\Concerns\InteractsWithDatabase;

use Modules\Invoices\Models\Invoice;
use Modules\Payments\Models\PaymentLog;
use Modules\Payments\Services\PaymentLogService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(PaymentLogService::class)]

class PaymentServiceTest extends AbstractServiceTestCase
{
    use InteractsWithDatabase;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('payment_method_id', $rules);
        $this->assertArrayHasKey('payment_amount', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_payments_by_date_descending(): void
    {
        /** Arrange */
        $invoice = $this->seedModel('Invoice');
        $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(3),
        ]);
        $payment2 = $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(1),
        ]);
        $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(2),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /** Assert */
        $payments = $result->items();
        $this->assertGreaterThanOrEqual(3, count($payments));
        // Most recent should be first
        $this->assertEquals($payment2->payment_id, $payments[0]->payment_id);
    }

    #[Group('queries')]
    #[Test]
    public function it_gets_payments_by_client_id(): void
    {
        /** Arrange */
        $client1  = $this->seedModel('\Modules\Crm\Models\Client');
        $client2  = $this->seedModel('\Modules\Crm\Models\Client');
        $invoice1 = $this->seedModel('\Modules\Invoices\Models\Invoice', ['client_id' => $client1->client_id]);
        $invoice2 = $this->seedModel('\Modules\Invoices\Models\Invoice', ['client_id' => $client2->client_id]);
        $payment1 = $this->seedModel('Payment', [
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment2 = $this->seedModel('Payment', [
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment3 = $this->seedModel('Payment', [
            'invoice_id' => $invoice2->invoice_id,
            'client_id'  => $client2->client_id,
        ]);

        /** Act */
        $result = $this->service->getByClientId($client1->client_id);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('payment_id', $payment1->payment_id));
        $this->assertTrue($result->contains('payment_id', $payment2->payment_id));
        $this->assertFalse($result->contains('payment_id', $payment3->payment_id));
    }
}
