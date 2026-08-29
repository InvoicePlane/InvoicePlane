<?php

namespace Tests\Feature\Payments;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\AbstractTestCase;

class PaymentCaptureServiceTest extends AbstractTestCase
{
    use \Tests\InteractsWithDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_processes_successful_completed_payment(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-1',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-123',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-1');

        $payment = $this->db->where('payment_external_id', 'CAP-123')->get('ip_payments')->row();
        $this->assertNotNull($payment);
        $this->assertEquals($invoice->invoice_id, $payment->invoice_id);
        $this->assertEquals('100.00', $payment->payment_amount);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_processes_pending_payment(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 50.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-2',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-456',
                            'status' => 'PENDING',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '50.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-2');

        $payment = $this->db->where('payment_external_id', 'CAP-456')->get('ip_payments')->row();
        $this->assertNotNull($payment);
        $this->assertStringContainsString('pending', mb_strtolower($payment->payment_note ?? ''));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_detects_duplicate_payment_attempt(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 75.00);

        // First payment
        $paypal_response1 = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-3',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-DUP',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '75.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response1))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-3');

        $payment_count_1 = $this->db->where('payment_external_id', 'CAP-DUP')->count_all_results('ip_payments');
        $this->assertEquals(1, $payment_count_1);

        // Second attempt with same capture ID
        $paypal_response2 = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-3-RETRY',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-DUP',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '75.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response2))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-3-RETRY');

        $payment_count_2 = $this->db->where('payment_external_id', 'CAP-DUP')->count_all_results('ip_payments');
        $this->assertEquals(1, $payment_count_2); // Still only 1 payment
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_payment_with_mismatched_currency(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-4',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-CURR',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'EUR'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-4');

        $payment = $this->db->where('payment_external_id', 'CAP-CURR')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_payment_with_insufficient_amount(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-5',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-AMOUNT',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '50.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-5');

        $payment = $this->db->where('payment_external_id', 'CAP-AMOUNT')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_payment_within_tolerance(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-6',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-TOL',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00005', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-6');

        $payment = $this->db->where('payment_external_id', 'CAP-TOL')->get('ip_payments')->row();
        $this->assertNotNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_payment_for_already_paid_invoice(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 0);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-7',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-PAID',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-7');

        $payment = $this->db->where('payment_external_id', 'CAP-PAID')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_payment_for_nonexistent_invoice(): void
    {
        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-8',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-NOTFOUND',
                            'status' => 'COMPLETED',
                            'invoice_id' => 99999,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-8');

        $payment = $this->db->where('payment_external_id', 'CAP-NOTFOUND')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_declined_payment(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-9',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-DECLINED',
                            'status' => 'DECLINED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                            'processor_response' => ['response_code' => '1111'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-9');

        $payment = $this->db->where('payment_external_id', 'CAP-DECLINED')->get('ip_payments')->row();
        $this->assertNull($payment);

        $merchant_response = $this->db->where('invoice_id', $invoice->invoice_id)
            ->where('merchant_response_successful', false)
            ->get('ip_merchant_responses')
            ->row();
        $this->assertNotNull($merchant_response);
        $this->assertStringContainsString('DECLINED', $merchant_response->merchant_response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_invalid_paypal_response_structure(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode(['invalid' => 'structure'])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-10');

        $payment = $this->db->where('payment_external_id', 'like', 'CAP-%')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_required_fields_in_paypal_response(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-11',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'status' => 'COMPLETED',
                            // Missing id, invoice_id, amount
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-11');

        $payment = $this->db->where('payment_external_id', 'like', 'CAP-%')->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_capture_id_exceeding_max_length(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $long_capture_id = str_repeat('A', 300);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-12',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => $long_capture_id,
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-12');

        $payment = $this->db->where('payment_external_id', $long_capture_id)->get('ip_payments')->row();
        $this->assertNull($payment);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_records_merchant_response_on_successful_capture(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-RESP',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-RESP',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-RESP');

        $merchant_response = $this->db->where('invoice_id', $invoice->invoice_id)
            ->where('merchant_response_successful', true)
            ->get('ip_merchant_responses')
            ->row();

        $this->assertNotNull($merchant_response);
        $this->assertEquals('COMPLETED', $merchant_response->merchant_response);
        $this->assertStringContainsString('ORDER-RESP', $merchant_response->merchant_response_reference);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_order_id_with_hyphens(): void
    {
        $invoice = $this->seedPayableInvoice(invoice_balance: 100.00);

        $paypal_response = [
            ['status' => 200, 'body' => json_encode(['access_token' => 'token123'])],
            ['status' => 200, 'body' => json_encode([
                'id' => 'ORDER-WITH-HYPHENS',
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAP-HYPHEN',
                            'status' => 'COMPLETED',
                            'invoice_id' => $invoice->invoice_id,
                            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
                        ]],
                    ],
                ]],
            ])],
        ];

        $this->withEnvironment('PAYPAL_MOCK_RESPONSES', json_encode($paypal_response))
            ->post('guest/gateways/paypal/paypal_capture_payment/ORDER-WITH-HYPHENS');

        $payment = $this->db->where('payment_external_id', 'CAP-HYPHEN')->get('ip_payments')->row();
        $this->assertNotNull($payment);
    }

    private function seedPayableInvoice(float $invoice_balance = 100.00)
    {
        $client_id = $this->db->insert('ip_clients', [
            'client_name' => 'Test Client',
            'client_active' => 1,
        ]) ? $this->db->insert_id() : null;

        $invoice_id = $this->db->insert('ip_invoices', [
            'client_id' => $client_id,
            'invoice_number' => 'INV-' . uniqid(),
            'invoice_date' => date('Y-m-d'),
            'invoice_due_date' => date('Y-m-d', strtotime('+30 days')),
            'invoice_balance' => $invoice_balance,
            'invoice_status_id' => 1,
            'invoice_url_key' => hash('sha256', uniqid()),
            'invoice_active' => 1,
        ]) ? $this->db->insert_id() : null;

        return $this->db->where('invoice_id', $invoice_id)->get('ip_invoices')->row();
    }
}
