<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class UsersAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpDatabase();
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (setup done in setUp) */

        /* Act */
        $response = $this->get('/users');

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
            sprintf('[GET /users] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors(): void
    {
        /* Arrange */
        /* (setup done in setUp) */

        /* Act */
        $response = $this->get('/users');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    #[Group('security')]
    public function it_treats_name_query_input_as_a_literal_search_term(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_users', [
            'user_name'          => 'Needle User',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'needle@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $this->databaseInsert('ip_users', [
            'user_name'          => 'Hidden User',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'hidden@test.local',
            'user_type'          => 1,
            'user_active'        => 0,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->request('GET', '/users/ajax/name_query/1', [
            'query' => "Needle%' OR 1=1 --",
        ], [], true);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyNotContains($response, 'Hidden User');
        $this->assertDatabaseHas('ip_users', ['user_name' => 'Needle User']);
        $this->assertDatabaseHas('ip_users', ['user_name' => 'Hidden User']);
    }

    #[Test]
    public function it_returns_latest_users_with_escaped_display_text(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_users', [
            'user_name'          => '<script>alert(1)</script>',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'xss-user@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->request('GET', '/users/ajax/get_latest', [], [], true);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyNotContains($response, '<script>alert(1)</script>');
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        self::assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', array_column($payload, 'text'));
    }
}
