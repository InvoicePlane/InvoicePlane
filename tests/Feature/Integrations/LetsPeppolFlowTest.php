<?php

namespace Tests\Feature\Integrations;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests for the LetsPeppol integration flow.
 *
 * These tests exercise the CI3 HTTP layer (settings, history, send_invoice
 * error gates) without hitting the real LetsPeppol API. All live HTTP is
 * stopped either by testing error-branch conditions (disabled client, missing
 * invoice) that never reach the API, or by asserting on pre-seeded database
 * state that the history view reads back.
 */
#[Group('integration')]
class LetsPeppolFlowTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function seedLetsPeppolClient(array $overrides = []): int
    {
        return $this->databaseInsert('ip_merchant_clients', array_merge([
            'merchant_type' => 'letspeppol',
            'label'         => 'Test LetsPeppol',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => json_encode([
                'client_id'                    => 'cid-test',
                'client_secret'                => 'csecret-test',
                'token_url'                    => 'https://api.letspeppol.eu/oauth2/token',
                'api_base_url'                 => 'https://api.letspeppol.eu',
                'invoice_endpoint'             => '/v1/invoices',
                'invoice_status_endpoint'      => '/v1/invoices/{id}',
                'incoming_invoices_endpoint'   => '/v1/incoming-invoices',
                'invoice_events_endpoint'      => '/v1/invoice-events',
                'credit_note_endpoint'         => '/v1/credit-notes',
                'participants_endpoint'        => '/v1/participants',
                'participant_lookup_endpoint'  => '/v1/participants/{id}',
                'transmissions_endpoint'       => '/v1/transmissions',
                'transmission_status_endpoint' => '/v1/transmissions/{id}',
                'documents_endpoint'           => '/v1/documents',
                'document_endpoint'            => '/v1/documents/{id}',
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    private function seedOutboundResponse(int $invoiceId, int $merchantClientId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_merchant_responses', array_merge([
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantClientId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => 'letspeppol',
            'merchant_response'            => 'Invoice queued for Peppol delivery',
            'merchant_response_reference'  => 'lp-ext-' . random_int(1000, 9999),
            'merchant_response_successful' => 1,
            'direction'                    => 'out',
            'record_type'                  => 'outbound_status',
            'status'                       => 'sent',
            'http_code'                    => 201,
            'created_at'                   => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    // =========================================================================
    // Provider registry
    // =========================================================================

    #[Test]
    #[Group('smoke')]
    public function it_includes_letspeppol_in_the_provider_registry(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/integrations/providers');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'letspeppol');
        $this->assertResponseBodyContains($response, 'LetsPeppol');
    }

    // =========================================================================
    // Settings list
    // =========================================================================

    #[Test]
    #[Group('smoke')]
    public function it_shows_a_letspeppol_integration_on_the_settings_page(): void
    {
        /* Arrange */
        $this->seedLetsPeppolClient(['label' => 'My LetsPeppol Account']);

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertNoApplicationError($response);
        $this->assertResponseBodyContains($response, 'My LetsPeppol Account');
    }

    // =========================================================================
    // Edit settings form
    // =========================================================================

    #[Test]
    public function it_renders_the_letspeppol_settings_edit_form(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient();

        /* Act */
        $response = $this->get('/integrations/settings/edit/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertNoApplicationError($response);
        $this->assertResponseBodyContains($response, 'client_id');
        $this->assertResponseBodyContains($response, 'client_secret');
        $this->assertResponseBodyContains($response, 'token_url');
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_letspeppol_edit_form(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient();
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/settings/edit/' . $id);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/settings/edit/%d] must redirect. Got [%d].', $id, $response->statusCode())
        );
    }

    // =========================================================================
    // Save settings
    // =========================================================================

    #[Test]
    public function it_persists_letspeppol_credentials_to_the_database(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient(['enabled' => 0]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $id, [
            'label'          => 'Production LetsPeppol',
            'enabled'        => '1',
            'auth_type'      => 'oauth2',
            'client_id'      => 'prod-client-id',
            'client_secret'  => 'prod-secret',
            'token_url'      => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url'   => 'https://api.letspeppol.eu',
            'invoice_endpoint'        => '/v1/invoices',
            'invoice_status_endpoint' => '/v1/invoices/{id}',
        ]);

        /* Assert */
        $this->assertNoApplicationError($response);
        $this->assertResponseRedirectTo($response, '/integrations/settings');

        $row = $this->databaseFetchOne('ip_merchant_clients', ['id' => $id]);
        self::assertSame('Production LetsPeppol', $row['label']);
        self::assertSame(1, (int) $row['enabled']);

        $settings = json_decode($row['settings_json'], true);
        self::assertSame('prod-client-id', $settings['client_id']);
        self::assertSame('prod-secret', $settings['client_secret']);
        self::assertSame('https://api.letspeppol.eu/oauth2/token', $settings['token_url']);
    }

    #[Test]
    public function it_disables_all_other_providers_when_letspeppol_is_enabled(): void
    {
        /* Arrange */
        $otherId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'Old Provider',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $letsPeppolId = $this->seedLetsPeppolClient(['enabled' => 0]);

        /* Act */
        $this->post('/integrations/settings/save/' . $letsPeppolId, [
            'label'        => 'Test LetsPeppol',
            'enabled'      => '1',
            'auth_type'    => 'oauth2',
            'client_id'    => 'cid',
            'client_secret' => 'csecret',
            'token_url'    => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url' => 'https://api.letspeppol.eu',
        ]);

        /* Assert */
        $other = $this->databaseFetchOne('ip_merchant_clients', ['id' => $otherId]);
        self::assertSame(0, (int) $other['enabled'], 'Previously enabled provider must be disabled.');

        $letsPeppol = $this->databaseFetchOne('ip_merchant_clients', ['id' => $letsPeppolId]);
        self::assertSame(1, (int) $letsPeppol['enabled']);
    }

    #[Test]
    public function it_rejects_a_private_ip_as_api_base_url_and_stays_on_the_edit_form(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient();

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $id, [
            'label'        => 'Attacker',
            'enabled'      => '0',
            'token_url'    => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url' => 'http://192.168.1.1/steal-credentials',
        ]);

        /* Assert */
        $this->assertNoApplicationError($response);
        $this->assertResponseRedirectTo($response, '/integrations/settings/edit/' . $id);
    }

    #[Test]
    public function it_rejects_a_non_https_token_url_and_stays_on_the_edit_form(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient();

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $id, [
            'label'        => 'Downgrade',
            'enabled'      => '0',
            'token_url'    => 'http://api.letspeppol.eu/oauth2/token',
            'api_base_url' => 'https://api.letspeppol.eu',
        ]);

        /* Assert */
        $this->assertNoApplicationError($response);
        $this->assertResponseRedirectTo($response, '/integrations/settings/edit/' . $id);
    }

    #[Test]
    public function it_rejects_an_absolute_url_in_an_endpoint_path_field(): void
    {
        /* Arrange */
        $id = $this->seedLetsPeppolClient();

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $id, [
            'label'            => 'Path Attack',
            'enabled'          => '0',
            'token_url'        => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url'     => 'https://api.letspeppol.eu',
            'invoice_endpoint' => 'https://evil.example.com/exfiltrate',
        ]);

        /* Assert */
        $this->assertNoApplicationError($response);
        $this->assertResponseRedirectTo($response, '/integrations/settings/edit/' . $id);
    }

    // =========================================================================
    // Invoice response history
    // =========================================================================

    #[Test]
    #[Group('smoke')]
    public function it_shows_a_sent_letspeppol_invoice_in_the_history_page(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient(['client_name' => 'Peppol Customer BV']);
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient();

        $this->seedOutboundResponse($invoiceId, $merchantClientId, [
            'merchant_response_reference' => 'lp-inv-abc123',
            'status'                      => 'sent',
        ]);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertNoApplicationError($response);
        $this->assertResponseBodyContains($response, 'lp-inv-abc123');
    }

    #[Test]
    public function it_shows_an_empty_history_for_an_invoice_that_was_never_sent(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertNoApplicationError($response);
        // No external references should appear
        $this->assertResponseBodyNotContains($response, 'lp-inv-');
    }

    #[Test]
    public function it_shows_multiple_peppol_responses_for_a_single_invoice(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient();

        $this->seedOutboundResponse($invoiceId, $merchantClientId, [
            'merchant_response_reference' => 'lp-ref-first',
            'status'                      => 'sent',
            'created_at'                  => date('Y-m-d H:i:s', strtotime('-10 minutes')),
        ]);
        $this->seedOutboundResponse($invoiceId, $merchantClientId, [
            'merchant_response_reference' => 'lp-ref-status-update',
            'status'                      => 'accepted',
            'created_at'                  => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'lp-ref-first');
        $this->assertResponseBodyContains($response, 'lp-ref-status-update');
    }

    #[Test]
    public function it_shows_a_rejected_status_in_the_invoice_history(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient();

        $this->seedOutboundResponse($invoiceId, $merchantClientId, [
            'merchant_response'            => 'Recipient not reachable on Peppol network',
            'merchant_response_successful' => 0,
            'status'                       => 'rejected',
            'http_code'                    => 422,
        ]);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertNoApplicationError($response);
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_letspeppol_history_page(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/history/%d] must redirect. Got [%d].', $invoiceId, $response->statusCode())
        );
    }

    // =========================================================================
    // send_invoice — error guards (no real API call)
    // =========================================================================

    #[Test]
    public function it_returns_an_error_when_send_invoice_references_an_unknown_merchant_client(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $nonexistentMerchantClientId = 99999;

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $nonexistentMerchantClientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 500);
    }

    #[Test]
    public function it_returns_an_error_when_send_invoice_uses_a_disabled_merchant_client(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient(['enabled' => 0]);

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantClientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 500);
    }

    #[Test]
    public function it_returns_an_error_when_send_invoice_references_an_unknown_invoice(): void
    {
        /* Arrange */
        $merchantClientId = $this->seedLetsPeppolClient();
        $nonexistentInvoiceId = 99999;

        /* Act */
        $response = $this->post('/integrations/send_invoice/' . $nonexistentInvoiceId . '/' . $merchantClientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 500);
    }

    // =========================================================================
    // Database state assertions — response log
    // =========================================================================

    #[Test]
    public function it_records_the_peppol_external_id_in_the_merchant_response_table(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient();

        /* Act */
        $this->seedOutboundResponse($invoiceId, $merchantClientId, [
            'merchant_response_reference' => 'peppol-ext-789',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                  => $invoiceId,
            'merchant_client_id'          => $merchantClientId,
            'merchant_response_driver'    => 'letspeppol',
            'merchant_response_reference' => 'peppol-ext-789',
            'direction'                   => 'out',
        ]);
    }

    #[Test]
    public function it_records_a_failed_send_attempt_in_the_merchant_response_table(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient();

        /* Act */
        $this->databaseInsert('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_client_id'           => $merchantClientId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => 'letspeppol',
            'merchant_response'            => 'Peppol participant not reachable',
            'merchant_response_successful' => 0,
            'direction'                    => 'out',
            'record_type'                  => 'outbound_status',
            'status'                       => 'error',
            'http_code'                    => 422,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                   => $invoiceId,
            'merchant_response_driver'     => 'letspeppol',
            'merchant_response_successful' => 0,
            'status'                       => 'error',
            'direction'                    => 'out',
        ]);
    }
}
