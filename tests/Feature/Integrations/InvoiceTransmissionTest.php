<?php

namespace Tests\Feature\Integrations;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * End-to-end transmission flow — POST /integrations/send_invoice/{invoiceId}/{merchantClientId}.
 *
 * Exercises Integrations::send_invoice from the HTTP boundary through provider
 * selection, settings decryption, the provider client's own send orchestration,
 * response normalisation and the ip_merchant_responses write — for each of the
 * three shipped providers.
 *
 * The outbound HTTP call and the Factur-X / UBL build are the only things
 * replaced: IntegrationTransport (armed in the test environment by the
 * INTEGRATION_MOCK_RESPONSES fixture this test publishes via withEnvironment())
 * hands the provider clients a Tests\Fakes\Integration\QueueApiClient and
 * send_invoice a stub artifact. Profile lookup and the provider-support check
 * still run for real.
 */
#[Group('integration')]
class InvoiceTransmissionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // LetsPeppol
    // -------------------------------------------------------------------------

    #[Test]
    public function it_transmits_an_invoice_through_letspeppol_and_logs_the_external_reference(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [
                ['success' => true, 'http_code' => 201, 'response' => ['id' => 'lp-ext-777', 'status' => 'sent']],
            ],
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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
    public function it_records_a_failed_letspeppol_transmission_when_the_provider_rejects_it(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [
                ['success' => false, 'http_code' => 422, 'message' => 'Recipient not reachable on the Peppol network', 'response' => []],
            ],
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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
    public function it_records_a_failed_letspeppol_transmission_when_oauth_fails(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses'   => [],
            'token_error' => 'invalid_client',
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

        /* Assert — auth failure is caught, no outbound row is written */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
            'direction'          => 'out',
        ]);
    }

    // -------------------------------------------------------------------------
    // Qonto
    // -------------------------------------------------------------------------

    #[Test]
    public function it_transmits_an_invoice_through_qonto_via_the_import_then_send_pipeline(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', 'Facturxv10');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [
                ['success' => true, 'http_code' => 201, 'response' => ['client_invoices' => [['invoice_id' => 'ci-qonto-9']]]],
                ['success' => true, 'http_code' => 200, 'response' => []],
            ],
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

        /* Assert — Qonto acknowledges asynchronously: reference logged, status pending */
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
    public function it_records_a_failed_qonto_transmission_when_the_import_returns_no_id(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', 'Facturxv10');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [
                ['success' => true, 'http_code' => 200, 'response' => ['client_invoices' => [[]]]],
            ],
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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

    // -------------------------------------------------------------------------
    // SuperPDP
    // -------------------------------------------------------------------------

    #[Test]
    public function it_transmits_an_invoice_through_superpdp_and_logs_the_external_reference(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedSendable('superpdp', 'Facturxv10');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [
                ['success' => true, 'http_code' => 201, 'response' => ['id' => 'sp-ext-55', 'status' => 'sent']],
            ],
        ])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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

    // -------------------------------------------------------------------------
    // Cross-cutting guards
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_send_to_a_provider_that_does_not_support_the_invoice_profile(): void
    {
        /* Arrange — Qonto does not support the Peppol UBL profile */
        [$invoiceId, $merchantId] = $this->seedSendable('qonto', 'UblPeppolV21');
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode(['responses' => []])]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode([
            'responses' => [['success' => true, 'http_code' => 201, 'response' => ['id' => 'nope']]],
        ])]);

        /* Act */
        $response = $this->get('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

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
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Seed a client (with the given e-invoicing profile), an invoice for it,
     * and an enabled merchant client of the given type.
     *
     * @return array{0: int, 1: int} [invoiceId, merchantClientId]
     */
    private function seedSendable(string $merchantType, string $profileCode): array
    {
        $clientId = $this->seedClient([
            'client_name'               => ucfirst($merchantType) . ' Peppol Customer',
            'client_einvoicing_active'  => 1,
            'client_einvoicing_version' => $profileCode,
            'client_peppol_id'          => '0088:1234567890123',
        ]);
        $invoiceId  = $this->seedInvoice($clientId);
        $merchantId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => $merchantType,
            'label'         => ucfirst($merchantType) . ' Live',
            'enabled'       => 1,
            'auth_type'     => $merchantType === 'qonto' ? 'bearer' : 'oauth2',
            'settings_json' => json_encode($this->settingsFor($merchantType)),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return [$invoiceId, $merchantId];
    }

    /**
     * @return array<string, string>
     */
    private function settingsFor(string $merchantType): array
    {
        return match ($merchantType) {
            'qonto' => [
                'access_token'               => 'qonto-access-token',
                'api_base_url'               => 'https://thirdparty.qonto.com',
                'import_endpoint'            => '/v2/client_invoices/bulk',
                'client_invoices_endpoint'   => '/v2/client_invoices',
                'send_invoice_endpoint'      => '/v2/client_invoices/{id}/send_by_einvoice',
                'invoice_status_endpoint'    => '/v2/client_invoices/{id}',
                'incoming_invoices_endpoint' => '/v2/supplier_invoices',
                'attachment_endpoint'        => '/v2/attachments/{id}',
            ],
            'superpdp' => [
                'client_id'                  => 'sp-client-id',
                'client_secret'              => 'sp-client-secret',
                'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
                'api_base_url'               => 'https://api.superpdp.tech',
                'invoice_endpoint'           => '/v1.beta/invoices',
                'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
                'incoming_invoices_endpoint' => '/v1.beta/invoices',
                'invoice_events_endpoint'    => '/v1.beta/invoice_events',
                'disable_pre_check'          => '0',
            ],
            default => [
                'client_id'                    => 'lp-client-id',
                'client_secret'                => 'lp-client-secret',
                'token_url'                    => 'https://api.letspeppol.eu/oauth2/token',
                'api_base_url'                 => 'https://api.letspeppol.eu',
                'invoice_endpoint'             => '/v1/invoices',
                'invoice_status_endpoint'      => '/v1/invoices/{id}',
                'incoming_invoices_endpoint'   => '/v1/incoming-invoices',
                'invoice_events_endpoint'      => '/v1/invoice-events',
                'credit_note_endpoint'         => '/v1/credit-notes',
                'credit_note_status_endpoint'  => '/v1/credit-notes/{id}',
                'participants_endpoint'        => '/v1/participants',
                'participant_lookup_endpoint'  => '/v1/participants/{id}',
                'transmissions_endpoint'       => '/v1/transmissions',
                'transmission_status_endpoint' => '/v1/transmissions/{id}',
                'documents_endpoint'           => '/v1/documents',
                'document_endpoint'            => '/v1/documents/{id}',
            ],
        };
    }
}
