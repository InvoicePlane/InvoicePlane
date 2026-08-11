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

    #[Test]
    public function it_shows_the_user_client_assignment_page_for_a_real_user(): void
    {
        /* Arrange */
        $userId = $this->seedNonAdminUser();

        /* Act */
        $response = $this->get('/user_clients/user/' . $userId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_for_an_unknown_user_id(): void
    {
        /* Act */
        $response = $this->get('/user_clients/user/999999');

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_assigns_a_client_to_a_user(): void
    {
        /* Arrange */
        $userId   = $this->seedNonAdminUser();
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->post('/user_clients/create/' . $userId, ['user_id' => (string) $userId, 'client_id' => (string) $clientId]);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_fails_to_assign_a_client_without_client_id(): void
    {
        /* Arrange */
        $userId = $this->seedNonAdminUser();

        /* Act */
        $this->post('/user_clients/create/' . $userId, ['user_id' => (string) $userId]);

        /* Assert */
        $this->assertDatabaseCount('ip_user_clients', 0);
    }

    #[Test]
    public function it_deletes_a_user_client_assignment(): void
    {
        /* Arrange */
        $userId       = $this->seedNonAdminUser();
        $clientId     = $this->seedClient();
        $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        /* Act */
        $response = $this->post('/user_clients/delete/' . $userClientId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $userClientId]);
    }

    #[Test]
    public function it_does_not_delete_a_user_client_assignment_on_a_non_post_request(): void
    {
        /* Arrange */
        $userId       = $this->seedNonAdminUser();
        $clientId     = $this->seedClient();
        $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        /* Act */
        $this->get('/user_clients/delete/' . $userClientId);

        /* Assert */
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $userClientId]);
    }

    private function seedNonAdminUser(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'     => 'Assignable User', 'user_email' => 'assignable-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_type'     => 2, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
