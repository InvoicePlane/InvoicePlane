<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;

/**
 * GHSA-5rqm-w9p8-gp7w (completion) and GHSA-ppgp-rvmx-2wp5: the rule "the primary administrator may read or change
 * anyone's client assignments, everybody else only their own" (Mdl_user_clients::can_manage_user_clients) was applied
 * to the endpoints that change assignments but not to the ones that list them. A secondary administrator could read
 * the assignments of the primary administrator or a peer through these endpoints (a fourth, users/ajax/load_user_client_table,
 * was dead code and has been removed):
 *
 *  - GET  users/ajax/modal_add_user_client/<user_id>  (the complement: clients not yet assigned)
 *  - GET  user_clients/user/<user_id>  (the assignment page itself)
 *
 * Each endpoint is checked for the same four outcomes: another user is refused with nothing leaked, the primary
 * administrator is served, an administrator is served their own, and a non-integer id is refused.
 */
#[Group('security')]
#[Group('users')]
final class UserClientAssignmentReadAuthorizationTest extends AbstractTestCase
{
    private const SECRET_CLIENT = 'Confidential Client Ltd';

    /**
     * @return array<string, array{0: string}>
     */
    public static function endpoints(): array
    {
        return [
            'modal_add_user_client'  => ['modal'],
            'user_clients/user page' => ['page'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function sessionBackedEndpoints(): array
    {
        return [
            'modal_add_user_client'  => ['modal'],
        ];
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_refuses_a_secondary_admin_reading_another_users_assignments(string $endpoint): void
    {
        /* Arrange */
        [$victimId] = $this->victimWithOneClient();
        $attackerId = $this->seedAdmin();
        $this->actingAsAdmin($attackerId);

        /* Act */
        $response = $this->read($endpoint, (string) $victimId);

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        $this->assertResponseBodyContains($response, 'Access denied');
        $this->assertResponseBodyNotContains($response, self::SECRET_CLIENT);
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_refuses_a_secondary_admin_reading_the_primary_administrators_assignments(string $endpoint): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => self::SECRET_CLIENT]);
        $this->databaseInsert('ip_user_clients', ['user_id' => 1, 'client_id' => $clientId]);
        $this->actingAsAdmin($this->seedAdmin());

        /* Act */
        $response = $this->read($endpoint, '1');

        /* Assert: refused by the authorisation (not a login redirect), with the reason shown and nothing of the
         * primary administrator's assignments in the body or changed in the database */
        self::assertSame(403, $response->statusCode());
        self::assertFalse($response->isRedirect(), 'A refusal is a 403, not a redirect to the login page.');
        $this->assertResponseBodyContains($response, 'Access denied');
        $this->assertResponseBodyNotContains($response, self::SECRET_CLIENT);
        $this->assertDatabaseCount('ip_user_clients', 1, ['user_id' => 1, 'client_id' => $clientId]);
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_serves_the_primary_administrator_any_users_assignments(string $endpoint): void
    {
        /* Arrange */
        [$victimId] = $this->victimWithOneClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->read($endpoint, (string) $victimId);

        /* Assert */
        self::assertSame(200, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        if ($endpoint !== 'modal') {
            $this->assertResponseBodyContains($response, self::SECRET_CLIENT);
        }
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_serves_an_admin_their_own_assignments(string $endpoint): void
    {
        /* Arrange */
        [$ownId] = $this->victimWithOneClient();
        $this->actingAsAdmin($ownId);

        /* Act */
        $response = $this->read($endpoint, (string) $ownId);

        /* Assert */
        self::assertSame(200, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        if ($endpoint !== 'modal') {
            $this->assertResponseBodyContains($response, self::SECRET_CLIENT);
        }
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_refuses_a_non_integer_user_id(string $endpoint): void
    {
        /* Arrange */
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->read($endpoint, '1.5');

        /* Assert: "1.5" is authorised as user 1 by an (int) cast but would be stored and queried as 2. The assignment
         * page redirects an id that matches no user before the authorisation, the others answer 403; nothing is served. */
        self::assertContains($response->statusCode(), [403, 307]);
        self::assertFalse($response->statusCode() === 200, 'A non-integer user id must never be served.');
        $this->assertResponseBodyNotContains($response, self::SECRET_CLIENT);
    }

    #[Test]
    public function it_opens_the_new_user_client_modal_before_any_assignment_is_staged(): void
    {
        /* Arrange: Users::form requests the modal without a user id while a user is being created */
        $this->actingAsAdmin($this->seedAdmin());
        $this->seedClient(['client_name' => self::SECRET_CLIENT]);

        /* Act */
        $modal = $this->ajax('GET', '/users/ajax/modal_add_user_client');

        /* Assert: nothing stored is read, the modal offers the clients to choose from */
        self::assertSame(200, $modal->statusCode(), 'Body: ' . substr($modal->body(), 0, 300));
        $this->assertResponseBodyContains($modal, self::SECRET_CLIENT);
    }

    #[Test]
    #[DataProvider('sessionBackedEndpoints')]
    public function it_checks_a_non_empty_user_id_even_when_assignments_are_staged_in_the_session(string $endpoint): void
    {
        /* Arrange: a user is being created (clients staged in the session) while the request names another user's id */
        [$victimId] = $this->victimWithOneClient();
        $this->actingAsAdmin($this->seedAdmin());
        $this->sessionData['user_clients'] = [1 => 1];

        /* Act */
        $response = $this->read($endpoint, (string) $victimId);

        /* Assert: the id is authorised before the session-backed branch is chosen */
        self::assertSame(403, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
    }

    /**
     * @return array{0: int, 1: int}
     */
    #[Test]
    public function it_no_longer_routes_the_removed_load_user_client_table_endpoint(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/load_user_client_table', ['user_id' => '1']);

        /* Assert: the endpoint listed assignments and rendered a view that no longer exists */
        self::assertSame(404, $response->statusCode());
    }

    #[Test]
    public function it_does_not_reference_the_removed_endpoint_from_the_modal(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->ajax('GET', '/users/ajax/modal_add_user_client/1');

        /* Assert: the modal only posts the assignment; the reload target #div_user_client_table does not exist */
        self::assertSame(200, $response->statusCode());
        $this->assertResponseBodyNotContains($response, 'load_user_client_table');
        $this->assertResponseBodyNotContains($response, 'div_user_client_table');
    }

    private function victimWithOneClient(): array
    {
        $userId   = $this->seedAdmin();
        $clientId = $this->seedClient(['client_name' => self::SECRET_CLIENT]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        return [$userId, $clientId];
    }

    private function read(string $endpoint, string $userId): HttpResponse
    {
        return match ($endpoint) {
            'modal' => $this->ajax('GET', '/users/ajax/modal_add_user_client/' . $userId),
            'page'  => $this->get('/user_clients/user/' . $userId),
        };
    }

    private function seedAdmin(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'          => 1,
            'user_name'          => 'Admin ' . bin2hex(random_bytes(3)),
            'user_email'         => 'admin+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('secret123', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
