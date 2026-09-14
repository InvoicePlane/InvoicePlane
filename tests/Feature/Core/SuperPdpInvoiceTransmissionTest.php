<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * SuperPDP — POST /integrations/send_invoice for a SuperPDP merchant client.
 *
 * SuperPDP (Factur-X profile) authenticates via OAuth2 client-credentials
 * (answered from the fake's token slot), then streams the PDF as a raw
 * application/pdf body to /v1.beta/invoices. The provider's external id is
 * normalised out of the response and logged to ip_merchant_responses.
 *
 * Unit-level coverage of the request shape lives in
 * tests/Unit/Core/SuperPdpClientTest.php.
 */
#[Group('integration')]
final class SuperPdpInvoiceTransmissionTest extends AbstractInvoiceTransmissionTestCase
{
    private const PROFILE = 'Facturxv10';

    #[Test]
    public function it_authenticates_then_uploads_the_pdf_and_logs_the_external_reference(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('superpdp', self::PROFILE);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['id' => 'sp-ext-55', 'status' => 'sent']],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'superpdp',
            'direction'                    => 'out',
            'merchant_response_reference'  => 'sp-ext-55',
            'merchant_response_successful' => 1,
        ]);
    }

    #[Test]
    public function it_records_a_failure_when_the_provider_rejects_the_upload(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('superpdp', self::PROFILE);
        $this->mockResponses([
            ['success' => false, 'http_code' => 400, 'message' => 'Invalid Factur-X payload', 'response' => []],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'superpdp',
            'direction'                    => 'out',
            'merchant_response_successful' => 0,
        ]);
    }

    #[Test]
    public function it_records_a_failure_when_oauth_authentication_fails(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('superpdp', self::PROFILE);
        $this->mockResponses([], tokenError: 'invalid_client');

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert — auth throws before any transmission; nothing is logged */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
            'direction'          => 'out',
        ]);
    }
}
