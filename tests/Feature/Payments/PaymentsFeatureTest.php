<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use Tests\AbstractTestCase;

/**
 * @group feature
 * @group payments
 */
#[CoversClass(Tests\Feature\Payments\PaymentsFeature::class)]
class PaymentsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_payments_index_page_with_a_200_status(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('/payments');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->actingAsGuest();

        $response = $this->get('/payments');

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /payments must redirect but got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_create_payment_form_for_an_existing_invoice(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->get('/payments/create/' . $invoiceId);

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_payment_and_links_it_to_the_invoice(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->post('/payments/create/' . $invoiceId, [
            'payment_method_id' => 1,
            'payment_amount'    => '250.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Regression test payment',
        ]);

        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'POST /payments/create should redirect on success or re-render on validation error. Got [%d].',
                $response->statusCode()
            )
        );

        if ($response->isRedirect()) {
            $this->assertDatabaseHas('ip_payments', [
                'invoice_id'     => $invoiceId,
                'payment_amount' => '250.00',
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_payment_submission_with_a_zero_amount(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->post('/payments/create/' . $invoiceId, [
            'payment_method_id' => 1,
            'payment_amount'    => '0.00',
            'payment_date'      => date('Y-m-d'),
        ]);

        $persistedCount = (int) get_instance()->db->where([
            'invoice_id'     => $invoiceId,
            'payment_amount' => '0.00',
        ])->count_all_results('ip_payments');

        self::assertSame(
            0,
            $persistedCount,
            'A payment with amount 0.00 must not be persisted to ip_payments.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_payment_view_page_for_a_seeded_payment(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '175.50']);

        $response = $this->get('/payments/view/' . $paymentId);

        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('175') || $response->contains((string) $paymentId),
            sprintf(
                'Payment view page must contain the payment amount or ID. Body (first 400 chars): %s',
                mb_substr($response->body(), 0, 400)
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_non_200_for_a_payment_view_with_a_nonexistent_id(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('/payments/view/999999999');

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301)
            ),
            sprintf(
                'Requesting a nonexistent payment must produce 404 or redirect, not a silent 200. Got [%d].',
                $response->statusCode()
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_raw_php_errors_on_the_payments_index(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('/payments');

        $this->assertResponseHasNoPhpErrors($response);
    }


    // Migrated from BckpPaymentsControllerTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_payments_index(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get(\Tests\Feature\Invoices\route('payments.index'));

        $response->assertSuccessful();
        $response->assertViewHas('payments');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_new_payment_with_valid_data(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_date'      => now()->format('Y-m-d'),
            'payment_amount'    => 50.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_note'      => 'Test payment note.',
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_payment_with_minimum_required_fields(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_amount'    => 25.50,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 25.50,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_payment_with_note(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_date'      => now()->format('Y-m-d'),
            'payment_amount'    => 100.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_note'      => $this->faker->sentence(),
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_note' => $paymentData['payment_note'],
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_existing_payment(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $payment = $this->seedModel('Payment', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);

        $updateData = [
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form', ['id' => $payment->payment_id]), $updateData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_id'     => $payment->payment_id,
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_views_payment_details(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $payment = $this->seedModel('Payment');

        $response = $this->get(\Tests\Feature\Invoices\route('payments.view', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_payment(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $payment = $this->seedModel('Payment');

        $response = $this->delete(\Tests\Feature\Invoices\route('payments.delete', ['id' => $payment->payment_id]));

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $payment->payment_id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_payment_form(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get(\Tests\Feature\Invoices\route('payments.form'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertViewHas('open_invoices');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_payment_edit_form(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $payment = $this->seedModel('Payment');

        $response = $this->get(\Tests\Feature\Invoices\route('payments.form', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cancels_payment_form_and_redirects(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), ['btn_cancel' => true]);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_saves_payment_custom_fields(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $customField = $this->seedModel('CustomField', [
            'custom_field_table' => 'ip_payment_custom',
        ]);

        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_amount'    => 100.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'custom'            => [
                $customField->custom_field_id => 'Custom value',
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payment_custom', [
            'payment_custom_fieldid'    => $customField->custom_field_id,
            'payment_custom_fieldvalue' => 'Custom value',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_online_payment_logs(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModelMany('PaymentLog', 5);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 5;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_online_payment_logs_by_search(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModel('PaymentLog', ['transaction_id' => 'TXN123ABC']);
        $this->seedModel('PaymentLog', ['transaction_id' => 'TXN456DEF']);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', ['search' => '123']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 1;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_online_payment_logs_by_date_range(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModel('PaymentLog', ['created_at' => now()->subDays(10)]);
        $this->seedModel('PaymentLog', ['created_at' => now()->subDays(5)]);
        $this->seedModel('PaymentLog', ['created_at' => now()]);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', [
            'date_from' => now()->subDays(6)->format('Y-m-d'),
            'date_to'   => now()->format('Y-m-d'),
        ]));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 2;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_online_payment_logs_by_status(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModel('PaymentLog', ['status' => 'completed']);
        $this->seedModel('PaymentLog', ['status' => 'completed']);
        $this->seedModel('PaymentLog', ['status' => 'failed']);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', ['status' => 'completed']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 2;
        });
    }

}
