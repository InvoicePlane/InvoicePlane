<?php

namespace Tests\Feature\Integrations;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

/**
 * Feature tests for the LetsPeppol integration flow.
 *
 * These tests exercise the CI3 HTTP layer (settings, history, send_invoice
 * error gates) without hitting the real LetsPeppol API. All live HTTP is
 * stopped either by testing error-branch conditions (disabled client, missing
 * invoice) that never reach the API, or by asserting on pre-seeded database
 * state that the history view reads back.
 *
 * Notes on the test harness:
 *   - Seed helpers assign an explicit `id` and return it, so a later lookup by
 *     id works regardless of the DB's auto-increment behaviour.
 *   - PHP CLI SAPI does not populate headers_list(), so Location headers are
 *     invisible to the harness. Redirect assertions use isRedirect() (status
 *     code) instead of the specific URL.
 *   - CI3 show_error() surfaces as a RuntimeException in this harness (via
 *     MY_Exceptions). The send_invoice error-gate tests use expectException().
 *
 * Deleted from prep/v180 in c4802ab9 alongside the reverted e-invoicing
 * provider merge; restored and re-fitted to the current MariaDB harness and
 * the since-split Settings/Events/Incoming controllers.
 */
#[Group('integration')]
class LetsPeppolFlowTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
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
        // Release the cached PDO handle so the follow-up read reconnects and
        // sees what the HTTP subprocess committed.
        $this->resetDatabaseConnection();

        // IntegrationSettingsForm::collect() hard-requires every non-sensitive
        // field on save (client_secret is the only one it will reuse from the
        // stored blob when left blank), so the save form re-posts the full set.
        $response = $this->post('/integrations/settings/save/' . $id, $this->letsPeppolSettingsPayload([
            'label'   => 'Production LetsPeppol',
            'enabled' => '1',
        ]));

        /* Assert */
        $this->assertNoApplicationError($response);
        self::assertTrue($response->isRedirect(), 'Successful save must redirect.');

        $row = $this->databaseFetchOne('ip_merchant_clients', ['id' => $id]);
        self::assertSame('Production LetsPeppol', $row['label'], 'The new label must be persisted.');
        self::assertSame(1, (int) $row['enabled'], 'The provider must be enabled after the save.');
        // settings_json is written through IntegrationSettingsCipher (encrypted at
        // rest), so assert the round-trip via the edit form, which decrypts it.
        $editResponse = $this->get('/integrations/settings/edit/' . $id);
        $this->assertResponseBodyContains($editResponse, 'prod-client-id');
    }

    #[Test]
    public function it_disables_all_other_providers_when_letspeppol_is_enabled(): void
    {
        /* Arrange */
        $otherId      = random_int(60000, 69999);
        $letsPeppolId = random_int(70000, 79999);

        $this->seedOtherProvider($otherId);
        $this->seedLetsPeppolClient(['id' => $letsPeppolId, 'enabled' => 0]);

        /* Act */
        $this->resetDatabaseConnection();
        $response = $this->post('/integrations/settings/save/' . $letsPeppolId, $this->letsPeppolSettingsPayload([
            'label'   => 'Test LetsPeppol',
            'enabled' => '1',
        ]));

        /* Assert */
        $this->assertNoApplicationError($response);
        self::assertSame(1, (int) $this->databaseFetchOne('ip_merchant_clients', ['id' => $letsPeppolId])['enabled'], 'LetsPeppol must be the enabled provider.');
        self::assertSame(0, (int) $this->databaseFetchOne('ip_merchant_clients', ['id' => $otherId])['enabled'], 'Enabling LetsPeppol must disable every other provider.');
    }

    // =========================================================================
    // SSRF protection
    // =========================================================================

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
        // PHP CLI SAPI does not populate headers_list(); we verify the redirect
        // happened (status code) and that the malicious URL was NOT persisted.
        self::assertTrue($response->isRedirect(), 'SSRF-rejected save must redirect back.');

        $row      = $this->databaseFetchOne('ip_merchant_clients', ['id' => $id]);
        $settings = json_decode($row['settings_json'] ?? '{}', true);
        self::assertNotSame('http://192.168.1.1/steal-credentials', $settings['api_base_url'] ?? null);
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
        self::assertTrue($response->isRedirect(), 'SSRF-rejected save must redirect back.');

        $row      = $this->databaseFetchOne('ip_merchant_clients', ['id' => $id]);
        $settings = json_decode($row['settings_json'] ?? '{}', true);
        self::assertNotSame('http://api.letspeppol.eu/oauth2/token', $settings['token_url'] ?? null);
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
        self::assertTrue($response->isRedirect(), 'SSRF-rejected save must redirect back.');

        $row      = $this->databaseFetchOne('ip_merchant_clients', ['id' => $id]);
        $settings = json_decode($row['settings_json'] ?? '{}', true);
        self::assertNotSame('https://evil.example.com/exfiltrate', $settings['invoice_endpoint'] ?? null);
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
        $clientId                    = $this->seedClient();
        $invoiceId                   = $this->seedInvoice($clientId);
        $nonexistentMerchantClientId = 99999;

        /* Act & Assert */
        // show_error() surfaces as a RuntimeException in the test harness;
        // trans('merchant_client_not_found') resolves to its English string.
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/not found or is disabled/i');

        $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $nonexistentMerchantClientId);
    }

    #[Test]
    public function it_returns_an_error_when_send_invoice_uses_a_disabled_merchant_client(): void
    {
        /* Arrange */
        $clientId         = $this->seedClient();
        $invoiceId        = $this->seedInvoice($clientId);
        $merchantClientId = $this->seedLetsPeppolClient(['enabled' => 0]);

        /* Act & Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/not found or is disabled/i');

        $this->post('/integrations/send_invoice/' . $invoiceId . '/' . $merchantClientId);
    }

    #[Test]
    public function it_returns_an_error_when_send_invoice_references_an_unknown_invoice(): void
    {
        /* Arrange */
        $merchantClientId     = $this->seedLetsPeppolClient();
        $nonexistentInvoiceId = 99999;

        /* Act & Assert */
        $this->expectException(RuntimeException::class);

        $this->post('/integrations/send_invoice/' . $nonexistentInvoiceId . '/' . $merchantClientId);
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
            'merchant_response_reference'  => '',
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
            'http_code'                    => 422,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * The full settings/save POST body the edit form submits for a LetsPeppol
     * provider. IntegrationSettingsForm::collect() requires every field marked
     * required in LetsPeppolClient::settingsSchema() except the sensitive
     * client_secret (reused from the stored blob when blank), so every save
     * carries the whole set.
     *
     * @param array<string, string> $overrides
     *
     * @return array<string, string>
     */
    private function letsPeppolSettingsPayload(array $overrides = []): array
    {
        return array_merge([
            'label'                        => 'LetsPeppol',
            'enabled'                      => '0',
            'auth_type'                    => 'oauth2',
            'client_id'                    => 'prod-client-id',
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
        ], $overrides);
    }

    /**
     * Insert a LetsPeppol merchant client with an explicit id.
     *
     * SQLite's INT AUTO_INCREMENT PRIMARY KEY does not auto-fill the id column
     * (unlike MySQL). We generate the id explicitly so that lookups by id work
     * in the HTTP subprocess.
     */
    private function seedLetsPeppolClient(array $overrides = []): int
    {
        $id = array_key_exists('id', $overrides) ? $overrides['id'] : random_int(10000, 59999);

        $this->databaseInsert('ip_merchant_clients', array_merge([
            'id'            => $id,
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

        return $id;
    }

    private function seedOtherProvider(int $id, array $overrides = []): void
    {
        $this->databaseInsert('ip_merchant_clients', array_merge([
            'id'            => $id,
            'merchant_type' => 'superpdp',
            'label'         => 'Old Provider',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
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
}
