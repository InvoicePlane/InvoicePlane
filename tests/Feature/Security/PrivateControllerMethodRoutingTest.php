<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-x2m4-962x-cphw: Sessions::authenticate() and Upload::create_dir() were public, so CodeIgniter
 * routed a bare GET straight to them (/sessions/sessions/authenticate/<email>/<password> let credentials
 * be tried outside the login form; /upload/upload/create_dir/<path> created directories). The fix is that
 * both are private; CodeIgniter answers 404 for a method that is not public. The existing login tests
 * call the login form only, so turning either back into `public` kept every test green.
 */
final class PrivateControllerMethodRoutingTest extends AbstractTestCase
{
    #[Test]
    public function it_does_not_route_a_get_request_to_sessions_authenticate(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $this->databaseInsert('ip_users', [
            'user_name'         => 'Routing Tester', 'user_password' => password_hash('routedpw', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => 'routeduser', 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/sessions/sessions/authenticate/routeduser/routedpw');

        /* Assert: not routable, and no login attempt was counted or accepted */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseCount('ip_login_log', 0);
        self::assertArrayNotHasKey('user_id', $response->session(), 'A bare GET to authenticate() must not log anyone in.');
    }

    #[Test]
    public function it_does_not_route_a_get_request_to_upload_create_dir(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $target = 'routing-probe-' . bin2hex(random_bytes(4));

        /* Act */
        $response = $this->get('/upload/upload/create_dir/' . $target);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        self::assertDirectoryDoesNotExist(ROOT_PATH . '/' . $target);
    }

    #[Test]
    public function it_does_not_route_any_url_to_the_layout_controller(): void
    {
        /*
         * Layout is a helper that other controllers call directly (buffer / set / render / load_view). It extends
         * MX_Controller, not Admin_Controller, so a URL that reaches it renders views without any login check.
         */

        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $render   = $this->get('/layout/layout/render');
        $loadView = $this->get('/layout/layout/load_view/layout/layout');
        $buffer   = $this->get('/layout/layout/buffer');

        /* Assert */
        $this->assertResponseStatusCode($render, 404);
        $this->assertResponseStatusCode($loadView, 404);
        $this->assertResponseStatusCode($buffer, 404);
    }
}
