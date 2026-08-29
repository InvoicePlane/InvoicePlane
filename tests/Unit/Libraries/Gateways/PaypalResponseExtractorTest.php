<?php

namespace Tests\Unit\Libraries\Gateways;

use PaypalResponseExtractor;
use PHPUnit\Framework\TestCase;

class PaypalResponseExtractorTest extends TestCase
{
    private function paypalResponse(array $overrides = []): object
    {
        $default = [
            'purchase_units' => [[
                'payments' => ['captures' => [$overrides]],
            ]],
        ];
        return json_decode(json_encode($default));
    }

    private function captureData(array $overrides = []): object
    {
        $default = [
            'id' => 'CAP-123',
            'status' => 'COMPLETED',
            'invoice_id' => '42',
            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
        ];
        return json_decode(json_encode(array_merge($default, $overrides)));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_capture_data_from_valid_response(): void
    {
        $response = $this->paypalResponse([
            'id' => 'CAP-123',
            'status' => 'COMPLETED',
            'invoice_id' => '42',
            'amount' => ['value' => '100.00', 'currency_code' => 'USD'],
        ]);

        $capture_data = PaypalResponseExtractor::extractCaptureData($response);

        $this->assertNotNull($capture_data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_capture_id_from_response(): void
    {
        $response = $this->paypalResponse(['id' => 'CAP-123']);
        $capture_data = PaypalResponseExtractor::extractCaptureData($response);

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
        $response = json_decode(json_encode(['invalid' => 'structure']));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid PayPal response structure');

        PaypalResponseExtractor::extractCaptureData($response);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('captureStatusScenarios')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_capture_status(string $inputStatus, string $expectedStatus): void
    {
        $response = $this->paypalResponse(['status' => $inputStatus]);

        $status = PaypalResponseExtractor::extractCaptureStatus($response);

        $this->assertEquals($expectedStatus, $status);
    }

    public static function captureStatusScenarios(): array
    {
        return [
            'lowercase_completed' => ['completed', 'COMPLETED'],
            'lowercase_pending' => ['pending', 'PENDING'],
            'uppercase_completed' => ['COMPLETED', 'COMPLETED'],
            'uppercase_pending' => ['PENDING', 'PENDING'],
            'mixed_case' => ['CoMpLeTeD', 'COMPLETED'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_missing_capture_status(): void
    {
        $response = $this->paypalResponse(['id' => 'CAP-123']);

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
        $capture_data = $this->captureData(['invoice_id' => '42']);

        $invoice_id = PaypalResponseExtractor::extractInvoiceId((object) [], $capture_data);

        $this->assertEquals('42', $invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_invoice_id_from_full_response(): void
    {
        $response = $this->paypalResponse(['invoice_id' => '99']);

        $invoice_id = PaypalResponseExtractor::extractInvoiceId($response);

        $this->assertEquals('99', $invoice_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_missing_invoice_id(): void
    {
        $response = $this->paypalResponse(['id' => 'CAP-123']);

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

    #[\PHPUnit\Framework\Attributes\DataProvider('amountAndCurrencyScenarios')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_amount_and_currency(?string $expectedAmount, string $expectedCurrency, array $amountData): void
    {
        $capture_data = $this->captureData(['amount' => $amountData]);

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertEquals($expectedAmount, $result['amount']);
        $this->assertEquals($expectedCurrency, $result['currency']);
    }

    public static function amountAndCurrencyScenarios(): array
    {
        return [
            'valid_with_lowercase_currency' => [
                '150.75',
                'EUR',
                ['value' => '150.75', 'currency_code' => 'eur'],
            ],
            'zero_amount' => [
                '0',
                'USD',
                ['value' => '0', 'currency_code' => 'USD'],
            ],
            'null_currency_code' => [
                '100',
                '',
                ['value' => '100', 'currency_code' => null],
            ],
            'missing_value' => [
                null,
                '',
                ['currency_code' => 'USD'],
            ],
            'empty_structure' => [
                null,
                '',
                [],
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_amount_data(): void
    {
        $capture_data = $this->captureData(['amount' => null]);

        $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);

        $this->assertNull($result['amount']);
        $this->assertEquals('', $result['currency']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_processor_response_code(): void
    {
        $capture_data = $this->captureData([
            'processor_response' => ['response_code' => '0000'],
        ]);

        $code = PaypalResponseExtractor::extractProcessorResponseCode($capture_data);

        $this->assertEquals('0000', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_default_for_missing_processor_response_code(): void
    {
        $capture_data = $this->captureData();

        $code = PaypalResponseExtractor::extractProcessorResponseCode($capture_data);

        $this->assertEquals('Unknown error', $code);
    }
}
