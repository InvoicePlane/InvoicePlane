<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Provider-agnostic guards on POST /integrations/send_invoice — the checks
 * that run before any provider client is built. Exercised once here rather
 * than repeated in every per-provider transmission test.
 */
#[Group('integration')]
final class SendInvoiceGuardTest extends AbstractInvoiceTransmissionTestCase
{
    #[Test]
    public function it_rejects_a_send_to_a_provider_that_does_not_support_the_invoice_profile(): void
    {
        /* Arrange — Qonto does not support the Peppol UBL profile */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', 'UblPeppolV21');
        $this->mockResponses([]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
            'direction'          => 'out',
        ]);
    }

    #[Test]
    public function it_does_not_transmit_on_a_plain_get_request(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21');
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['id' => 'must-not-be-used']],
        ]);

        /* Act */
        $this->send($invoiceId, $merchantId, 'GET');

        /* Assert — isPostRequest() gate: the action returns without doing anything */
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
        ]);
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_send_endpoint(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21');
        $this->actingAsGuest();

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);
    }
}
