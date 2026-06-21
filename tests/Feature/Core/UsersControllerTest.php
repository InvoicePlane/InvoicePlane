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
}
