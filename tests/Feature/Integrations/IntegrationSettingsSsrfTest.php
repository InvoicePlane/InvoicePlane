<?php

namespace Tests\Feature\Integrations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests proving that SSRF is blocked at the integration settings save endpoint.
 *
 * Before the fix, any admin could store a private/internal URL in api_base_url
 * or token_url. The application would then fetch that URL server-side when
 * sending invoices — redirecting outbound requests to cloud metadata endpoints,
 * internal services, or RFC-1918 addresses.
 *
 * Each test posts a malicious URL to /integrations/settings/save/{id} and
 * asserts that the malicious URL was NOT written to the database, proving the
 * save was aborted by the SSRF guard.
 *
 * Note on test infrastructure: in PHP CLI (the subprocess environment), the
 * `headers_list()` function always returns an empty array, so we cannot inspect
 * Location headers. Instead, we assert on DB state: blocked URLs must not appear
 * in settings_json, while valid URLs must produce a redirect response.
 */
#[Group('security')]
class IntegrationSettingsSsrfTest extends AbstractTestCase
{
    /**
     * Rowid of the seeded merchant client row (SQLite rowid, not the id column
     * which is NULL due to MySQL-only AUTO_INCREMENT being ignored by SQLite).
     */
    private int $rowid;

    protected function setUp(): void
    {
        $this->setUpDatabase();
        parent::setUp();
        $this->actingAsAdmin();

        $this->rowid = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'SSRF Test Provider',
            'enabled'       => 0,
            'auth_type'     => 'oauth2',
            'settings_json' => json_encode([
                'api_base_url' => 'https://api.superpdp.tech',
                'token_url'    => 'https://api.superpdp.tech/oauth2/token',
            ]),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Proof tests — what was broken before the fix
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_the_aws_metadata_endpoint_as_api_base_url(): void
    {
        /* Arrange */
        $maliciousUrl = 'http://169.254.169.254';
        $payload = $this->validPayload(['api_base_url' => $maliciousUrl]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'SSRF-rejected save must redirect, not crash. Got status: ' . $response->statusCode()
        );
        $this->assertUrlNotPersistedInRow($maliciousUrl);
    }

    #[Test]
    public function it_rejects_an_internal_token_url(): void
    {
        /* Arrange */
        $maliciousUrl = 'https://10.0.0.1/oauth/token';
        $payload = $this->validPayload(['token_url' => $maliciousUrl]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'SSRF-rejected save must redirect. Got: ' . $response->statusCode());
        $this->assertUrlNotPersistedInRow($maliciousUrl);
    }

    // -------------------------------------------------------------------------
    // Blocked api_base_url addresses
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('blockedApiBaseUrls')]
    public function it_rejects_a_blocked_api_base_url(string $url, string $reason): void
    {
        /* Arrange */
        $payload = $this->validPayload(['api_base_url' => $url]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            "Blocked api_base_url [{$url}] ({$reason}) must redirect. Got: " . $response->statusCode()
        );
        $this->assertUrlNotPersistedInRow($url);
    }

    public static function blockedApiBaseUrls(): array
    {
        return [
            'loopback'          => ['https://127.0.0.1/', 'loopback'],
            'RFC-1918 class A'  => ['https://10.1.2.3/', 'RFC-1918 A'],
            'RFC-1918 class B'  => ['https://172.16.0.1/', 'RFC-1918 B'],
            'RFC-1918 class C'  => ['https://192.168.0.1/', 'RFC-1918 C'],
            'link-local'        => ['https://169.254.0.1/', 'link-local'],
            'IPv6 loopback'     => ['https://[::1]/', 'IPv6 loopback'],
            'IPv6 unique-local' => ['https://[fd00::1]/', 'IPv6 unique-local'],
            'plain http'        => ['http://api.example.com/', 'non-HTTPS'],
        ];
    }

    // -------------------------------------------------------------------------
    // Blocked token_url addresses
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('blockedTokenUrls')]
    public function it_rejects_a_blocked_token_url(string $url, string $reason): void
    {
        /* Arrange */
        $payload = $this->validPayload(['token_url' => $url]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            "Blocked token_url [{$url}] ({$reason}) must redirect. Got: " . $response->statusCode()
        );
        $this->assertUrlNotPersistedInRow($url);
    }

    public static function blockedTokenUrls(): array
    {
        return [
            'loopback'          => ['https://127.0.0.1/token', 'loopback'],
            'RFC-1918 class A'  => ['https://10.0.0.1/oauth/token', 'RFC-1918 A'],
            'link-local'        => ['https://169.254.169.254/latest/api-token', 'AWS metadata (link-local)'],
            'CGNAT'             => ['https://100.64.0.1/token', 'CGNAT shared address space'],
            'http scheme'       => ['http://auth.example.com/token', 'non-HTTPS'],
            'file scheme'       => ['file:///etc/passwd', 'file:// scheme'],
        ];
    }

    // -------------------------------------------------------------------------
    // Absolute URL in relative endpoint path fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_an_absolute_url_in_the_invoice_endpoint_path(): void
    {
        /* Arrange */
        $maliciousUrl = 'https://attacker.example.com/steal';
        $payload = $this->validPayload(['invoice_endpoint' => $maliciousUrl]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Absolute URL in invoice_endpoint must redirect. Got: ' . $response->statusCode());
        $this->assertUrlNotPersistedInRow($maliciousUrl);
    }

    #[Test]
    public function it_rejects_an_absolute_url_in_the_upload_endpoint_path(): void
    {
        /* Arrange */
        $maliciousUrl = 'https://attacker2.example.com/exfil';
        $payload = $this->validPayload(['upload_endpoint' => $maliciousUrl]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Absolute URL in upload_endpoint must redirect. Got: ' . $response->statusCode());
        $this->assertUrlNotPersistedInRow($maliciousUrl);
    }

    // -------------------------------------------------------------------------
    // Happy path — valid public HTTPS URLs must be accepted and saved
    // -------------------------------------------------------------------------

    #[Test]
    public function it_accepts_a_valid_public_https_api_base_url(): void
    {
        /* Arrange */
        $validUrl = 'https://api.superpdp.tech';
        $payload = $this->validPayload([
            'api_base_url' => $validUrl,
            'token_url'    => 'https://api.superpdp.tech/oauth2/token',
        ]);

        /* Act */
        $response = $this->post('/integrations/settings/save/' . $this->rowid, $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'Valid public HTTPS URL must result in a redirect. Got status: ' . $response->statusCode()
        );
        // Valid URL must be persisted — confirms the guard doesn't over-block.
        $this->assertUrlPersistedInRow($validUrl);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns a complete valid save payload with overrides applied.
     * All endpoint paths are relative paths (no scheme), which is the correct format.
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'label'                      => 'SSRF Test Provider',
            'enabled'                    => '0',
            'auth_type'                  => 'oauth2',
            'client_id'                  => 'test-client-id',
            'client_secret'              => 'test-client-secret',
            'api_base_url'               => 'https://api.superpdp.tech',
            'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
            'invoice_endpoint'           => '/v1.beta/invoices',
            'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
            'incoming_invoices_endpoint' => '/v1.beta/invoices',
            'invoice_events_endpoint'    => '/v1.beta/invoice_events',
            'upload_endpoint'            => '',
            'send_invoice_endpoint'      => '',
        ], $overrides);
    }

    /**
     * Asserts that a URL does NOT appear in the settings_json of the seeded row.
     * Uses SQLite rowid since the id column is NULL (AUTO_INCREMENT is MySQL-only).
     */
    private function assertUrlNotPersistedInRow(string $url): void
    {
        $row = $this->databaseFetchByRowid('ip_merchant_clients', $this->rowid);

        self::assertStringNotContainsString(
            $url,
            $row['settings_json'] ?? '',
            "The blocked URL [{$url}] must not appear in settings_json after a rejected save."
        );
    }

    /**
     * Asserts that a URL DOES appear as a value in the decoded settings_json.
     * Decodes JSON before comparing to avoid false failures from slash-escaping
     * differences between json_encode implementations (e.g. \/ vs /).
     */
    private function assertUrlPersistedInRow(string $url): void
    {
        $row      = $this->databaseFetchByRowid('ip_merchant_clients', $this->rowid);
        $settings = json_decode($row['settings_json'] ?? '{}', true) ?: [];

        self::assertContains(
            $url,
            $settings,
            "The valid URL [{$url}] should appear in settings_json values after a successful save. " .
            "Got: " . ($row['settings_json'] ?? '(empty)')
        );
    }

}
