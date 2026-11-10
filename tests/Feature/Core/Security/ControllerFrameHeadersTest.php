<?php

namespace Tests\Feature\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\CapturesControllerOutputHeaders;

/**
 * GHSA-858w-pp5p-wphm (PR #1723): Base_Controller::setSecurityHeaders() and
 * Guest_Controller::setSecurityHeaders() must themselves queue X-Frame-Options / CSP frame-ancestors.
 *
 * ClickjackingProtectionTest cannot see that: the request runner merges kernel.php's header list into
 * every response, so its assertions pass with both controller methods deleted. These tests read
 * CI_Output's own header queue instead (see CapturesControllerOutputHeaders).
 */
#[Group('security')]
final class ControllerFrameHeadersTest extends AbstractTestCase
{
    use CapturesControllerOutputHeaders;

    private const CSP_SELF = "frame-ancestors 'self'; object-src 'none'; base-uri 'self'";

    #[Test]
    public function it_queues_frame_headers_from_base_controller_on_the_login_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/sessions/login');

        /* Assert */
        self::assertSame(200, $result['status'], 'The login page must render so the headers belong to a real response.');
        self::assertSame('SAMEORIGIN', $result['headers']['X-Frame-Options']);
        self::assertSame(self::CSP_SELF, $result['headers']['Content-Security-Policy']);
        self::assertSame('strict-origin-when-cross-origin', $result['headers']['Referrer-Policy']);
        self::assertSame('nosniff', $result['headers']['X-Content-Type-Options']);
    }

    #[Test]
    public function it_queues_frame_headers_on_the_public_guest_invoice_view(): void
    {
        /* Arrange: guest/View extends Base_Controller and is reachable without any login */
        $clientId = $this->seedClient();
        $invoice  = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);
        $urlKey   = (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoice])['invoice_url_key'];
        $this->actingAsGuest();

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/guest/view/invoice/' . $urlKey);

        /* Assert */
        self::assertSame(200, $result['status']);
        self::assertSame('SAMEORIGIN', $result['headers']['X-Frame-Options']);
        self::assertSame(self::CSP_SELF, $result['headers']['Content-Security-Policy']);
    }

    #[Test]
    public function it_queues_frame_headers_on_a_logged_in_guest_portal_page(): void
    {
        /* Arrange */
        $this->seedLoggedInGuest();

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/guest/invoices/status/open');

        /* Assert: 200 proves Guest_Controller accepted the session, not a bounce to the login page */
        self::assertSame(200, $result['status']);
        self::assertSame('SAMEORIGIN', $result['headers']['X-Frame-Options']);
        self::assertSame(self::CSP_SELF, $result['headers']['Content-Security-Policy']);
        self::assertSame('strict-origin-when-cross-origin', $result['headers']['Referrer-Policy']);
        self::assertSame('nosniff', $result['headers']['X-Content-Type-Options']);
    }

    #[Test]
    public function it_queues_frame_headers_on_admin_pages_through_the_inherited_base_controller(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/dashboard');

        /* Assert */
        self::assertSame(200, $result['status']);
        self::assertSame('SAMEORIGIN', $result['headers']['X-Frame-Options']);
        self::assertSame(self::CSP_SELF, $result['headers']['Content-Security-Policy']);
    }

    #[Test]
    public function it_honours_a_configured_deny_value_in_the_controller_x_frame_options(): void
    {
        /* Arrange */
        $this->seedLoggedInGuest();
        $this->withEnvironment(['X_FRAME_OPTIONS' => 'DENY']);

        /* Act */
        $guest = $this->requestCapturingControllerHeaders('GET', '/guest/invoices/status/open');
        $this->actingAsGuest();
        $login = $this->requestCapturingControllerHeaders('GET', '/sessions/login');

        /* Assert: proves the header is read from configuration, not hard-coded */
        self::assertSame('DENY', $guest['headers']['X-Frame-Options']);
        self::assertSame('DENY', $login['headers']['X-Frame-Options']);
    }

    #[Test]
    public function it_omits_nosniff_on_the_logged_in_guest_portal_only_when_disabled(): void
    {
        /* Arrange */
        $this->seedLoggedInGuest();
        $this->withEnvironment(['ENABLE_X_CONTENT_TYPE_OPTIONS' => 'false']);

        /* Act */
        $result = $this->requestCapturingControllerHeaders('GET', '/guest/invoices/status/open');

        /* Assert: 200 proves the guest session was accepted; framing protection stays */
        self::assertSame(200, $result['status']);
        self::assertNull($result['headers']['X-Content-Type-Options']);
        self::assertSame('SAMEORIGIN', $result['headers']['X-Frame-Options']);
    }

    #[Test]
    public function it_omits_nosniff_from_controller_headers_only_when_disabled(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $this->withEnvironment(['ENABLE_X_CONTENT_TYPE_OPTIONS' => 'true']);
        $enabled = $this->requestCapturingControllerHeaders('GET', '/sessions/login');
        $this->withEnvironment(['ENABLE_X_CONTENT_TYPE_OPTIONS' => 'false']);

        /* Act */
        $disabled = $this->requestCapturingControllerHeaders('GET', '/sessions/login');

        /* Assert */
        self::assertSame('nosniff', $enabled['headers']['X-Content-Type-Options'], 'Positive control: explicitly enabled yields nosniff.');
        self::assertNull($disabled['headers']['X-Content-Type-Options']);
        self::assertSame('SAMEORIGIN', $disabled['headers']['X-Frame-Options'], 'Disabling nosniff must not drop framing protection.');
    }
}
