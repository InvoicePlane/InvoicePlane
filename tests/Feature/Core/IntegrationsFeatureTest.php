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

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_events_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/events] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_events_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(307),
            ),
            sprintf('[GET /integrations/events] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors_on_the_integrations_events_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_incoming_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(307),
            ),
            sprintf('[GET /integrations/incoming] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors_on_the_integrations_incoming_page(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    #[Group('smoke')]
    public function it_handles_the_history_endpoint_gracefully_for_a_missing_invoice(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/integrations/history/999999');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(302),
                self::equalTo(404),
            ),
            sprintf('[GET /integrations/history/999999] returned unexpected status [%d].', $response->statusCode())
        );
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_history_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/history/1');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/history/1] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
