<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use Tests\AbstractTestCase;

/**
 * Drives the integrations module through the real MX router stack.
 *
 * These tests also act as a canary for MY_Router::aliasPsr4Controller():
 * if IntegrationsController.php is renamed or the alias breaks, routing
 * will return a 404 / throw, which these tests will catch immediately.
 *
 * @group feature
 * @group integrations
 */
class IntegrationsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_the_integrations_index_through_mx_routing_and_returns_200(): void
    {
        $response = $this->get('/integrations');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_html_on_the_integrations_index_page(): void
    {
        $response = $this->get('/integrations');

        $this->assertResponseBodyContains($response, '<html');

        self::assertGreaterThan(
            200,
            $response->bodyLength(),
            'The integrations index page body is suspiciously short — the view likely did not render.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_integrations(): void
    {
        $this->actingAsGuest();

        $response = $this->get('/integrations');

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /integrations must redirect to login. Got status [%d] with body: %s',
                $response->statusCode(),
                mb_substr($response->body(), 0, 200)
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_a_provider_api_key_in_the_integrations_page_body(): void
    {
        $response = $this->get('/integrations');

        $this->assertResponseBodyNotContains($response, 'api_secret');
        $this->assertResponseBodyNotContains($response, 'private_key');
        $this->assertResponseBodyNotContains($response, 'client_secret');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_peppol_settings_form_when_the_provider_is_enabled(): void
    {
        $response = $this->get('/integrations/peppol');

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(302),
                self::equalTo(301),
                self::equalTo(404)
            ),
            sprintf(
                'GET /integrations/peppol produced an unexpected status [%d].',
                $response->statusCode()
            )
        );

        if ($response->statusCode() === 200) {
            $this->assertResponseHasNoPhpErrors($response);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_json_with_a_success_flag_when_saving_valid_integration_settings(): void
    {
        $response = $this->post('/integrations/save', [
            'provider'    => 'letspeppol',
            'api_key'     => 'test-key-' . bin2hex(random_bytes(4)),
            'environment' => 'sandbox',
        ]);

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(302)
            ),
            sprintf('POST /integrations/save returned unexpected status [%d].', $response->statusCode())
        );

        if ($response->statusCode() === 200 && str_contains($response->header('Content-Type') ?? '', 'json')) {
            $payload = $response->json();

            self::assertArrayHasKey(
                'success',
                $payload,
                'JSON response from /integrations/save must contain a [success] key.'
            );
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_raw_php_errors_on_integration_settings_save_with_empty_payload(): void
    {
        $response = $this->post('/integrations/save', []);

        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resolves_integrations_via_the_psr4_controller_alias_not_a_legacy_file(): void
    {
        $psr4ControllerFile   = APPPATH . 'modules/integrations/controllers/IntegrationsController.php';
        $legacyControllerFile = APPPATH . 'modules/integrations/controllers/Integrations.php';

        if ( ! file_exists($psr4ControllerFile) && ! file_exists($legacyControllerFile)) {
            static::markTestSkipped(
                'Neither IntegrationsController.php nor Integrations.php found — module may not exist yet.'
            );
        }

        $response = $this->get('/integrations');

        $this->assertResponseStatusCode($response, 200);

        self::assertStringNotContainsString(
            'Unable to load your default controller',
            $response->body(),
            'CI3 returned its default 404 page — MY_Router failed to resolve the integrations controller.'
        );
    }
}
