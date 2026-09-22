<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * LetsPeppol — POST /integrations/send_invoice for a LetsPeppol merchant client.
 *
 * LetsPeppol (Peppol BIS / UBL profile) authenticates via OAuth2
 * client-credentials (answered from the fake's token slot), then POSTs the
 * document to /v1/invoices as multipart. The external id is normalised out of
 * the response and logged to ip_merchant_responses.
 *
 * Provider-client scenario coverage lives in
 * tests/Unit/Core/LetsPeppolScenarioTest.php and LetsPeppolApiClientTest.php.
 */
#[Group('integration')]
final class LetsPeppolInvoiceTransmissionTest extends AbstractInvoiceTransmissionTestCase
{
    private const PROFILE = 'UblPeppolV21';

    #[Test]
    public function it_authenticates_then_transmits_and_logs_the_external_reference(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', self::PROFILE);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['id' => 'lp-ext-777', 'status' => 'sent']],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A completed send redirects back to the invoice.');
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'letspeppol',
            'direction'                    => 'out',
            'merchant_response_reference'  => 'lp-ext-777',
            'merchant_response_successful' => 1,
        ]);
    }

    #[Test]
    public function it_records_a_failure_when_the_provider_rejects_the_document(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', self::PROFILE);
        $this->mockResponses([
            ['success' => false, 'http_code' => 422, 'message' => 'Recipient not reachable on the Peppol network', 'response' => []],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantId,
            'merchant_response_driver'     => 'letspeppol',
            'direction'                    => 'out',
            'merchant_response_successful' => 0,
        ]);
    }

    #[Test]
    public function it_records_a_failure_when_oauth_authentication_fails(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', self::PROFILE);
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
