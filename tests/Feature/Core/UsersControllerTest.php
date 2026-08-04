<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * UsersController Feature Tests.
 *
 * Tests user management list view.
 */
class UsersControllerTest extends AbstractTestCase
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
        $this->databaseInsert('ip_users', [
            'user_name'          => 'Alice Tester',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'alice@test.local',
            'user_type'          => 0,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/users');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Alice Tester');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/users');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/users] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    #[Group('security')]
    public function it_prevents_a_non_primary_admin_from_changing_another_users_password(): void
    {
        /* Arrange */
        $attackerId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Attacker Admin',
            'user_password'      => password_hash('attacker-secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'attacker-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $victimHash = password_hash('victim-secret', PASSWORD_DEFAULT);
        $victimId   = $this->databaseInsert('ip_users', [
            'user_name'          => 'Victim Admin',
            'user_password'      => $victimHash,
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'victim-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $this->actingAsAdmin($attackerId);

        /* Act */
        $response = $this->post('/users/change_password/' . $victimId, [
            'user_password'         => 'attacker-chosen-password',
            'user_password_confirm' => 'attacker-chosen-password',
            'btn_submit'            => '1',
        ]);

        /* Assert */
        self::assertSame(403, $response->statusCode());

        $victim = $this->databaseFetchOne('ip_users', ['user_id' => $victimId]);
        self::assertNotNull($victim);
        self::assertSame(
            $victimHash,
            $victim['user_password'],
            'A non-primary admin must not be able to mutate another user password hash.'
        );
    }
}
