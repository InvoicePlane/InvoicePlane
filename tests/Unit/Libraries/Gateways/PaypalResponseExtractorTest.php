<?php

namespace Tests\Unit\Libraries\Gateways;

use Exception;
use PaypalResponseExtractor;
use PHPUnit\Framework\TestCase;

class PaypalResponseExtractorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_capture_data_from_valid_response(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [[
                        'id'         => 'CAP-123',
                        'status'     => 'COMPLETED',
                        'invoice_id' => '42',
                        'amount'     => ['value' => '100.00', 'currency_code' => 'USD'],
                    ]],
                ],
            ]],
        ]));

        $capture_data = PaypalResponseExtractor::extractCaptureData($response);

        $this->assertNotNull($capture_data);
        $this->assertEquals('CAP-123', $capture_data->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_when_capture_data_missing(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => ['captures' => []],
            ]],
        ]));

        $capture_data = PaypalResponseExtractor::extractCaptureData($response);

        $this->assertNull($capture_data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_on_invalid_response_structure(): void
    {
        $response = json_decode(json_encode([
            'invalid' => 'structure',
        ]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid PayPal response structure');

        PaypalResponseExtractor::extractCaptureData($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_capture_status_uppercase(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [['status' => 'completed']],
                ],
            ]],
        ]));

        $status = PaypalResponseExtractor::extractCaptureStatus($response);

        $this->assertEquals('COMPLETED', $status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_pending_status(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [['status' => 'pending']],
                ],
            ]],
        ]));

        $status = PaypalResponseExtractor::extractCaptureStatus($response);

        $this->assertEquals('PENDING', $status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_missing_capture_status(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => ['captures' => [['id' => 'CAP-123']]],
            ]],
        ]));

        $status = PaypalResponseExtractor::extractCaptureStatus($response);

        $this->assertNull($status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_invalid_response_structure_status(): void
    {
        $response = json_decode(json_encode(['invalid' => 'structure']));

        $status = PaypalResponseExtractor::extractCaptureStatus($response);

        $this->assertNull($status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_invoice_id_from_capture_data(): void
    {
        $capture_data = json_decode(json_encode(['invoice_id' => '42']));

        $invoice_id = PaypalResponseExtractor::extractInvoiceId((object) [], $capture_data);

        $this->assertEquals('42', $invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_invoice_id_from_full_response(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [['invoice_id' => '99']],
                ],
            ]],
        ]));

        $invoice_id = PaypalResponseExtractor::extractInvoiceId($response);

        $this->assertEquals('99', $invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_missing_invoice_id(): void
    {
        $response = json_decode(json_encode([
            'purchase_units' => [[
                'payments' => ['captures' => [['id' => 'CAP-123']]],
            ]],
        ]));

        $invoice_id = PaypalResponseExtractor::extractInvoiceId($response);

        $this->assertNull($invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_malformed_response_for_invoice_id(): void
    {
        $response = json_decode(json_encode(['invalid' => 'data']));

        $invoice_id = PaypalResponseExtractor::extractInvoiceId($response);

        $this->assertNull($invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_amount_and_currency(): void
    {
        $capture_data = json_decode(json_encode([
            'amount' => ['value' => '150.75', 'currency_code' => 'eur'],
        ]));

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertEquals('150.75', $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_amount_data(): void
    {
        $capture_data = json_decode(json_encode(['id' => 'CAP-123']));

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertNull($result['amount']);
        $this->assertEquals('', $result['currency']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_processor_response_code(): void
    {
        $capture_data = json_decode(json_encode([
            'processor_response' => ['response_code' => '0000'],
        ]));

        $code = PaypalResponseExtractor::extractProcessorResponseCode($capture_data);

        $this->assertEquals('0000', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_default_for_missing_processor_response_code(): void
    {
        $capture_data = json_decode(json_encode(['id' => 'CAP-123']));

        $code = PaypalResponseExtractor::extractProcessorResponseCode($capture_data);

        $this->assertEquals('Unknown error', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_edge_case_empty_amount_string(): void
    {
        $capture_data = json_decode(json_encode([
            'amount' => ['value' => '0', 'currency_code' => 'USD'],
        ]));

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertEquals('0', $result['amount']);
        $this->assertEquals('USD', $result['currency']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_edge_case_null_currency_code(): void
    {
        $capture_data = json_decode(json_encode([
            'amount' => ['value' => '100', 'currency_code' => null],
        ]));

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertEquals('100', $result['amount']);
        $this->assertEquals('', $result['currency']);
    }
}
