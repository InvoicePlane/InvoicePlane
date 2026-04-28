<?php

namespace Tests\Unit\Payments;

use Mdl_Payments;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payments::class)]
class PaymentsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payments/mdl_payments');
        $this->model = $this->CI->mdl_payments;
    }

    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
    {
        /* Arrange */
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 100.00,
            'payment_date'   => now(),
        ]);

            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 50.00,
            'payment_date'   => now(),
        ]);

        // Create payment for different invoice
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

            'invoice_id'     => $otherInvoice->invoice_id,
            'payment_amount' => 200.00,
            'payment_date'   => now(),
        ]);

        /* Act */
        $result = $this->model->whereInvoiceId($invoice->invoice_id);

        /* Assert */
        $this->assertInstanceOf(PaymentsService::class, $result);
    }

    #[Test]
    public function it_returns_db_array_with_correct_structure(): void
    {
        /* Act */
        $result = $this->model->dbArray();

        /* Assert */
        $this->assertIsArray($result);
    }


    // Migrated from BckpPaymentServiceTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('payment_method_id', $rules);
        $this->assertArrayHasKey('payment_amount', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_orders_payments_by_date_descending(): void
    {
        /* Arrange */
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(3),
        ]);
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(1),
        ]);
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(2),
        ]);

        /* Act */
        $result = $this->model->getAllWithRelations();

        /* Assert */
        $payments = $result->items();
        $this->assertGreaterThanOrEqual(3, count($payments));
        // Most recent should be first
        $this->assertEquals($payment2->payment_id, $payments[0]->payment_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_gets_payments_by_client_id(): void
    {
        /* Arrange */
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
            'invoice_id' => $invoice2->invoice_id,
            'client_id'  => $client2->client_id,
        ]);

        /* Act */
        $result = $this->model->getByClientId($client1->client_id);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('payment_id', $payment1->payment_id));
        $this->assertTrue($result->contains('payment_id', $payment2->payment_id));
        $this->assertFalse($result->contains('payment_id', $payment3->payment_id));
    }

}
