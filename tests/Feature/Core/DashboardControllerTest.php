<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Dashboard controller feature tests via CI3 HTTP subprocess harness.
 *
 * @group feature
 * @group dashboard
 */
class DashboardControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('crud')]
    public function it_displays_dashboard_with_a_200_status(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_a_full_html_document_on_the_dashboard(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseBodyContains($response, '<html');
        $this->assertResponseBodyContains($response, '</html>');
        self::assertGreaterThan(
            500,
            $response->bodyLength(),
            'Dashboard body is suspiciously short — the layout likely did not render.'
        );
    }

    #[Test]
    public function it_includes_navigation_elements_on_the_dashboard(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->contains('invoice') || $response->contains('client') || $response->contains('nav'),
            'The dashboard must contain at least one primary navigation element.'
        );
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_dashboard(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET /dashboard must redirect. Got status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors_on_the_dashboard(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $first  = $this->get('/dashboard');
        $second = $this->get('/dashboard');

        /* Assert */
        self::assertSame(
            $first->statusCode(),
            $second->statusCode(),
            'Two consecutive GET /dashboard requests must return the same HTTP status.'
        );
    }

    #[Test]
    public function it_does_not_display_invoice_form_content_on_the_dashboard(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertFalse(
            $response->contains('<form') && $response->contains('invoice_number'),
            'The dashboard must not render an invoice creation form.'
        );
    }

    #[Test]
    public function it_includes_the_clients_section_link_on_the_dashboard(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertTrue(
            $response->contains('client') || $response->contains('invoice'),
            'Dashboard must reference clients or invoices in its content.'
        );
    }

    #[Test]
    public function it_returns_200_with_seeded_invoices_and_clients(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Dashboard Test Client']);
        $this->seedInvoice($clientId, ['invoice_date_created' => date('Y-m-d')]);

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_200_with_multiple_seeded_clients(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Alpha Corp']);
        $this->seedClient(['client_name' => 'Beta Ltd']);
        $this->seedClient(['client_name' => 'Gamma BV']);

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }
}
