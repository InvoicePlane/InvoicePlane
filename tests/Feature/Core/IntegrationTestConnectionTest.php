<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

/**
 * Feature tests for the "Test connection" button on the integration provider
 * settings form.
 *
 * POST /integrations/settings/test_connection/{id} loads the merchant client,
 * decrypts its stored settings, resolves the provider client from the registry
 * and calls IntegrationClientInterface::ping(). The outbound HTTP call is
 * replaced by Tests\Fakes\Integration\QueueApiClient (armed through
 * IntegrationTransport by the INTEGRATION_MOCK_RESPONSES fixture); routing,
 * settings decryption and the registry all run for real. The endpoint answers
 * with JSON: {reachable: bool, http_code: int, message: string}.
 */
#[Group('integration')]
class IntegrationTestConnectionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_reports_a_reachable_provider_as_a_successful_connection(): void
    {
        /* Arrange */
        $id = $this->seedQonto();
        $this->armTransport(['responses' => [
            ['success' => true, 'http_code' => 200, 'response' => []],
        ]]);

        /* Act */
        $response = $this->request('POST', '/integrations/settings/test_connection/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $payload = $this->decode($response->body());
        self::assertTrue($payload['reachable'], 'A provider whose read call answered must report reachable.');
        self::assertSame(200, $payload['http_code']);
    }

    #[Test]
    public function it_reports_the_provider_as_unreachable_when_authentication_fails(): void
    {
        /* Arrange */
        $id = $this->seedSuperPdp();
        $this->armTransport(['responses' => [], 'token_error' => 'invalid_client']);

        /* Act */
        $response = $this->request('POST', '/integrations/settings/test_connection/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $payload = $this->decode($response->body());
        self::assertFalse($payload['reachable'], 'A failed OAuth handshake must report unreachable.');
    }

    #[Test]
    public function it_answers_with_a_json_body_carrying_the_three_probe_keys(): void
    {
        /* Arrange */
        $id = $this->seedQonto();
        $this->armTransport(['responses' => [['success' => true, 'http_code' => 200]]]);

        /* Act */
        $response = $this->request('POST', '/integrations/settings/test_connection/' . $id);

        /* Assert */
        $payload = $this->decode($response->body());
        self::assertArrayHasKey('reachable', $payload);
        self::assertArrayHasKey('http_code', $payload);
        self::assertArrayHasKey('message', $payload);
        $this->assertResponseBodyNotContains($response, '<html');
    }

    #[Test]
    public function it_rejects_a_non_post_request(): void
    {
        /* Arrange */
        $id = $this->seedQonto();

        /* Act */
        $response = $this->get('/integrations/settings/test_connection/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 405);
    }

    #[Test]
    public function it_errors_for_an_unknown_merchant_client(): void
    {
        /* Arrange */

        /* Act & Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/not found or is disabled/i');

        $this->request('POST', '/integrations/settings/test_connection/99999');
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_test_connection_endpoint(): void
    {
        /* Arrange */
        $id = $this->seedQonto();
        $this->actingAsGuest();

        /* Act */
        $response = $this->request('POST', '/integrations/settings/test_connection/' . $id);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated POST [/integrations/settings/test_connection/%d] must redirect. Got [%d].', $id, $response->statusCode())
        );
    }

    #[Test]
    public function it_shows_a_test_connection_control_on_the_provider_edit_form(): void
    {
        /* Arrange */
        $id = $this->seedQonto();

        /* Act */
        $response = $this->get('/integrations/settings/edit/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        // The button carries the endpoint URL and the three UI strings the
        // extracted JS module reads off data-msg-* attributes.
        $this->assertResponseBodyContains($response, 'test_connection/' . $id);
        $this->assertResponseBodyContains($response, 'js-test-connection');
        $this->assertResponseBodyContains($response, 'data-msg-running');
        $this->assertResponseBodyContains($response, 'data-msg-ok');
        $this->assertResponseBodyContains($response, 'data-msg-failed');
        // The behaviour itself lives in the tested module, not inline in the view.
        $this->assertResponseBodyContains($response, 'js/integration-settings.js');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * @param array<string, mixed> $config
     */
    private function armTransport(array $config): void
    {
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode($config)]);
    }

    /**
     * @return array{reachable: bool, http_code: int, message: string}
     */
    private function decode(string $body): array
    {
        $payload = json_decode($body, true);
        self::assertIsArray($payload, 'The endpoint must answer with a JSON object. Got: ' . $body);

        return $payload;
    }

    private function seedQonto(array $overrides = []): int
    {
        $id = array_key_exists('id', $overrides) ? $overrides['id'] : random_int(10000, 59999);

        $this->databaseInsert('ip_merchant_clients', array_merge([
            'id'            => $id,
            'merchant_type' => 'qonto',
            'label'         => 'Test Qonto',
            'enabled'       => 1,
            'auth_type'     => 'bearer',
            'settings_json' => json_encode([
                'access_token'               => 'qonto-access-token',
                'api_base_url'               => 'https://thirdparty.qonto.com',
                'import_endpoint'            => '/v2/client_invoices/bulk',
                'client_invoices_endpoint'   => '/v2/client_invoices',
                'send_invoice_endpoint'      => '/v2/client_invoices/{id}/send_by_einvoice',
                'invoice_status_endpoint'    => '/v2/client_invoices/{id}',
                'incoming_invoices_endpoint' => '/v2/supplier_invoices',
                'attachment_endpoint'        => '/v2/attachments/{id}',
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $overrides));

        return $id;
    }

    private function seedSuperPdp(array $overrides = []): int
    {
        $id = array_key_exists('id', $overrides) ? $overrides['id'] : random_int(10000, 59999);

        $this->databaseInsert('ip_merchant_clients', array_merge([
            'id'            => $id,
            'merchant_type' => 'superpdp',
            'label'         => 'Test SuperPDP',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => json_encode([
                'client_id'                  => 'sp-client-id',
                'client_secret'              => 'sp-client-secret',
                'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
                'api_base_url'               => 'https://api.superpdp.tech',
                'invoice_endpoint'           => '/v1.beta/invoices',
                'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
                'incoming_invoices_endpoint' => '/v1.beta/invoices',
                'incoming_document_endpoint' => '/v1.beta/invoices/{id}/document',
                'invoice_events_endpoint'    => '/v1.beta/invoice_events',
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $overrides));

        return $id;
    }
}
