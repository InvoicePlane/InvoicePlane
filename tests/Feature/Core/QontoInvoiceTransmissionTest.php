<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Qonto — POST /integrations/send_invoice for a Qonto merchant client.
 *
 * Qonto (Factur-X profile) is a two-call pipeline: multipart POST to
 * /v2/client_invoices/bulk (the "import"), then POST to
 * /v2/client_invoices/{id}/send_by_einvoice. It acknowledges asynchronously,
 * so a completed send lands in ip_merchant_responses with status "pending".
 *
 * Unit-level coverage of the request shapes lives in
 * tests/Unit/Core/QontoClientTest.php.
 */
#[Group('integration')]
final class QontoInvoiceTransmissionTest extends AbstractInvoiceTransmissionTestCase
{
    private const PROFILE = 'Facturxv10';

    #[Test]
    public function it_imports_then_sends_by_einvoice_and_logs_the_client_invoice_id(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', self::PROFILE);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['client_invoices' => [['invoice_id' => 'ci-qonto-9']]]],
            ['success' => true, 'http_code' => 200, 'response' => []],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                  => $invoiceId,
            'merchant_client_id'          => $merchantId,
            'merchant_response_driver'    => 'qonto',
            'direction'                   => 'out',
            'merchant_response_reference' => 'ci-qonto-9',
            'status'                      => 'pending',
        ]);
    }

    #[Test]
    public function it_records_a_failure_when_the_import_returns_no_client_invoice_id(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', self::PROFILE);
        $this->mockResponses([
            ['success' => true, 'http_code' => 200, 'response' => ['client_invoices' => [[]]]],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'qonto',
            'direction'                    => 'out',
            'merchant_response_successful' => 0,
        ]);
    }

    #[Test]
    public function it_records_the_import_error_without_attempting_send_by_einvoice(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', self::PROFILE);
        $this->mockResponses([
            ['success' => false, 'http_code' => 422, 'message' => 'Unprocessable Factur-X document', 'response' => []],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'qonto',
            'direction'                    => 'out',
            'merchant_response_successful' => 0,
            'http_code'                    => 422,
        ]);
    }
}
