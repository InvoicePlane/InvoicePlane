<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke tests for the integrations module via CI3 HTTP harness.
 */
class IntegrationsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_settings_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(303),
                self::equalTo(307),
                self::equalTo(308),
            ),
            sprintf('[GET /integrations/settings] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors_on_the_integrations_settings_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_a_successful_response_for_the_integrations_providers_endpoint(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/providers');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(307),
            ),
            sprintf('[GET /integrations/providers] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_settings_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/settings] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_incoming_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/incoming] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
