<?php

namespace Tests\Feature\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression tests for clickjacking protection via X-Frame-Options and CSP headers.
 *
 * Clickjacking exploits missing framing restrictions to trick users into
 * approving actions via invisible iframes. The fix requires:
 *
 * 1. X-Frame-Options header on every response (admin, guest, login pages)
 * 2. Content-Security-Policy: frame-ancestors (modern alternative)
 * 3. nginx SameSite cookie setting adjusted (Docker deployments)
 *
 * On Docker, if proxy_cookie_path sets SameSite=none, the browser will send
 * cookies in cross-origin iframes, making clickjacking possible even with
 * X-Frame-Options present (the header is only a defense-in-depth layer).
 * The proper fix changes SameSite=none to SameSite=Strict or Lax.
 *
 * GHSA-858w-pp5p-wphm: Clickjacking on Guest Quote Approval Pages
 *
 * @reporter @sharma19d
 */
#[Group('security')]
class ClickjackingProtectionTest extends AbstractTestCase
{
    // -------------------------------------------------------------------------
    // X-Frame-Options header — all endpoints
    // -------------------------------------------------------------------------

    #[Test]
    public function it_sends_x_frame_options_on_admin_dashboard(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->hasHeader('X-Frame-Options'),
            'X-Frame-Options header must be present on admin pages to prevent clickjacking.'
        );
        self::assertMatchesRegularExpression(
            '/^(SAMEORIGIN|DENY)$/i',
            $response->header('X-Frame-Options'),
            'X-Frame-Options must be SAMEORIGIN (allow same-domain frames) or DENY (block all frames).'
        );
    }

    #[Test]
    public function it_sends_x_frame_options_on_guest_invoice_page(): void
    {
        /* Arrange */
        $client = $this->seedClient(['client_name' => 'Test Client']);
        $invoice = $this->seedGuestInvoice($client);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $invoice->invoice_url_key);

        /* Assert */
        self::assertTrue(
            $response->hasHeader('X-Frame-Options'),
            'X-Frame-Options header must be present on guest pages to prevent clickjacking.'
        );
    }

    #[Test]
    public function it_sends_x_frame_options_on_guest_quote_page(): void
    {
        /* Arrange */
        $client = $this->seedClient(['client_name' => 'Test Client']);
        $quote = $this->seedQuote($client);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/guest/view/quote/' . $quote->quote_url_key);

        /* Assert */
        self::assertTrue(
            $response->hasHeader('X-Frame-Options'),
            'X-Frame-Options header must be present on guest quote pages to prevent clickjacking attacks on approval.'
        );
    }

    #[Test]
    public function it_sends_x_frame_options_on_login_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert */
        self::assertTrue(
            $response->hasHeader('X-Frame-Options'),
            'X-Frame-Options header must be present on login pages (unauthenticated users still need framing protection).'
        );
    }

    // -------------------------------------------------------------------------
    // Content-Security-Policy: frame-ancestors — modern defense
    // -------------------------------------------------------------------------

    #[Test]
    public function it_sends_csp_frame_ancestors_on_all_responses(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        self::assertTrue(
            $response->hasHeader('Content-Security-Policy'),
            'Content-Security-Policy header must be present for modern browser framing control.'
        );
        self::assertStringContainsString(
            'frame-ancestors',
            $response->header('Content-Security-Policy'),
            'CSP must include frame-ancestors directive to restrict embedding contexts.'
        );
    }

    #[Test]
    public function it_sets_csp_frame_ancestors_to_sameorigin_by_default(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/dashboard');

        /* Assert */
        $cspHeader = $response->header('Content-Security-Policy');
        self::assertMatchesRegularExpression(
            "/frame-ancestors\\s+'self'/i",
            $cspHeader,
            "CSP frame-ancestors should default to 'self' (SAMEORIGIN equivalent in CSP syntax)."
        );
    }

    // -------------------------------------------------------------------------
    // nginx SameSite configuration — Docker deployments
    // -------------------------------------------------------------------------

    #[Test]
    public function it_configures_nginx_cookie_samesite_safely(): void
    {
        /* Arrange */
        $nginxConfig = dirname(__DIR__, 4) . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertFileExists(
            $nginxConfig,
            'nginx config must exist.'
        );

        // SameSite=none allows cookies in cross-origin iframes, enabling clickjacking.
        // The fix changes this to SameSite=Strict or Lax, or documents why none is necessary.
        self::assertStringContainsString(
            'proxy_cookie_path',
            $content,
            'nginx config must explicitly configure proxy_cookie_path for session cookies.'
        );

        // Check that SameSite is set to a restrictive value OR there's a comment explaining why none is needed
        $hasSameSiteStrict = (bool) preg_match('/SameSite\s*=\s*(Strict|Lax)/i', $content);
        $hasNoneExplanation = (bool) preg_match(
            '/SameSite\s*=\s*none.*comment|SameSite\s*=\s*none.*reason/i',
            $content
        );

        self::assertTrue(
            $hasSameSiteStrict || $hasNoneExplanation,
            'nginx proxy_cookie_path should use SameSite=Strict or Lax (not none), '
            . 'or include a documented reason if SameSite=none is required for cross-origin compatibility.'
        );
    }

    // -------------------------------------------------------------------------
    // Integration: quote approval should not be clickjackable
    // -------------------------------------------------------------------------

    #[Test]
    public function it_protects_guest_quote_approval_from_clickjacking(): void
    {
        /* Arrange */
        $client = $this->seedClient(['client_name' => 'Test Client']);
        $quote = $this->seedQuote($client);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/guest/quotes/approve/' . $quote->quote_id);

        /* Assert */
        // Quote approval may redirect (if GET) or show a form (if rendered)
        // The key is that all responses must have framing protection headers
        self::assertTrue(
            $response->hasHeader('X-Frame-Options'),
            'Quote approval endpoint must send X-Frame-Options to prevent UI redressing attacks.'
        );
        self::assertMatchesRegularExpression(
            '/^(SAMEORIGIN|DENY)$/i',
            $response->header('X-Frame-Options'),
            'Quote approval must not be embeddable in cross-origin frames.'
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function seedGuestInvoice(int $clientId, array $overrides = []): object
    {
        $urlKey = bin2hex(random_bytes(16));
        $id     = $this->seedInvoice(
            $clientId,
            array_merge(['invoice_status_id' => 2, 'invoice_url_key' => $urlKey], $overrides),
            ['invoice_total' => '100.00', 'invoice_balance' => '100.00'],
        );

        return (object) ['invoice_id' => $id, 'invoice_url_key' => $overrides['invoice_url_key'] ?? $urlKey];
    }

    protected function seedQuote(int $clientId, array $overrides = []): object
    {
        $urlKey = bin2hex(random_bytes(16));
        $id     = $this->databaseInsert('ip_quotes', array_merge([
            'user_id'             => 1,
            'client_id'           => $clientId,
            'invoice_group_id'    => 1,
            'quote_number'        => 'QT-' . bin2hex(random_bytes(3)),
            'quote_status_id'     => 2,
            'quote_url_key'       => $urlKey,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),
            'quote_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));

        $this->databaseInsert('ip_quote_amounts', [
            'quote_id'             => $id,
            'quote_item_subtotal'  => '500.00',
            'quote_item_tax_total' => '0.00',
            'quote_tax_total'      => '0.00',
            'quote_total'          => '500.00',
        ]);

        return (object) ['quote_id' => $id, 'quote_url_key' => $overrides['quote_url_key'] ?? $urlKey];
    }
}
