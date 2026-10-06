<?php

namespace Tests\Feature\Core;

use Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Dashboard controller feature tests via CI3 HTTP subprocess harness.
 */
#[Group('feature')]
#[Group('dashboard')]
#[CoversClass(Dashboard::class)]
class DashboardControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
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
        self::assertTrue(
            $response->contains('client') || $response->contains('invoice'),
            'Dashboard must reference clients or invoices in its content.'
        );
    }

    #[Test]
    public function it_lists_recent_invoices_with_their_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Dashboard Test Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'DASH-RECENT-001']);
        $this->seedInvoice($clientId, ['invoice_number' => 'DASH-RECENT-002']);

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'DASH-RECENT-001');
        $this->assertResponseBodyContains($response, 'DASH-RECENT-002');
        $this->assertResponseBodyContains($response, 'Dashboard Test Client');
    }

    #[Test]
    public function it_flags_overdue_invoices_and_otherwise_reports_none(): void
    {
        /* Arrange: nothing overdue yet */
        $none = $this->get('/dashboard');

        $clientId = $this->seedClient();
        $this->seedInvoice(
            $clientId,
            ['invoice_number' => 'DASH-LATE-001', 'invoice_status_id' => 2, 'invoice_date_due' => date('Y-m-d', strtotime('-10 days'))],
            ['invoice_total' => '75.00', 'invoice_balance' => '75.00'],
        );

        /* Act */
        $late = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseBodyContains($none, 'No overdue Invoice');
        $this->assertResponseBodyNotContains($late, 'No overdue Invoice');
        $this->assertResponseBodyContains($late, 'Overdue Invoices');
        self::assertMatchesRegularExpression('/text-danger">\s*\$75\s*</', $late->body(), 'The overdue panel must total the overdue balance.');
    }

}
