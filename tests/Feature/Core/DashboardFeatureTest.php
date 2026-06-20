<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use Tests\AbstractTestCase;

/**
 * Feature tests for the Dashboard module.
 *
 * @group feature
 * @group dashboard
 */
#[CoversClass(Tests\Feature\Core\DashboardFeature::class)]
class DashboardFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires live CI3 environment with database — not available in CI');
        $this->actingAsAdmin();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_dashboard_with_a_200_status_when_authenticated(): void
    {
        $response = $this->get('/dashboard');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_a_full_html_document_on_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $this->assertResponseBodyContains($response, '<html');
        $this->assertResponseBodyContains($response, '</html>');

        self::assertGreaterThan(
            500,
            $response->bodyLength(),
            'Dashboard body is suspiciously short — the layout likely did not render.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_navigation_elements_on_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('invoice') || $response->contains('client') || $response->contains('nav'),
            'The dashboard must contain at least one primary navigation element.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_a_guest_away_from_the_dashboard(): void
    {
        $this->actingAsGuest();

        $response = $this->get('/dashboard');

        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET /dashboard must redirect. Got status [%d].', $response->statusCode())
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_expose_php_errors_on_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $this->assertResponseHasNoPhpErrors($response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests(): void
    {
        $first  = $this->get('/dashboard');
        $second = $this->get('/dashboard');

        self::assertSame(
            $first->statusCode(),
            $second->statusCode(),
            'Two consecutive GET /dashboard requests must return the same HTTP status.'
        );
    }
}
