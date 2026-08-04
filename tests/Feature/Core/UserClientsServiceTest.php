<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the user_clients module via CI3 HTTP harness.
 */
class UserClientsServiceTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/user_clients');

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
            sprintf('[GET /user_clients] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/user_clients');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/user_clients] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_to_a_real_route_when_create_is_cancelled(): void
    {
        /* Arrange */
        // User_clients::create() redirects to 'user_clients/field/' . $user_id on
        // cancel, but the controller has no field() method — only user($id),
        // which is what renders the user_clients/field.php view. The test
        // harness cannot capture the Location header under CLI SAPI (see
        // SessionsFeatureTest), so this is verified at the source level: the
        // redirect target string must be a route that actually resolves.
        $controllerFile = APPPATH . 'modules/user_clients/controllers/User_clients.php';
        $content        = file_get_contents($controllerFile);

        /* Act */
        $routeStillPointsAtMissingFieldMethod = str_contains($content, "redirect('user_clients/field/");

        /* Assert */
        self::assertFalse(
            $routeStillPointsAtMissingFieldMethod,
            "create()'s cancel path must not redirect to user_clients/field/ — "
            . 'that route does not exist (the controller method is user(), not field()).'
        );
    }
}
