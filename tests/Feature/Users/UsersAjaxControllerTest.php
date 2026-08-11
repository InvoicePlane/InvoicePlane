<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class UsersAjaxControllerTest extends AbstractTestCase
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

    #[Test]
    public function it_saves_a_valid_permissive_search_preference(): void
    {
        /* Arrange */
        /* Act */
        $this->request('GET', '/users/ajax/save_preference_permissive_search_users', ['permissive_search_users' => '1'], [], true);

        /* Assert */
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'enable_permissive_search_users', 'setting_value' => '1']);
    }

    #[Test]
    public function it_rejects_an_invalid_permissive_search_preference_value(): void
    {
        /* Arrange */
        /* Act */
        $this->request('GET', '/users/ajax/save_preference_permissive_search_users', ['permissive_search_users' => '2'], [], true);

        /* Assert */
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'enable_permissive_search_users']);
    }

    #[Test]
    public function it_assigns_a_client_to_an_existing_user(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $userId   = $this->databaseInsert('ip_users', [
            'user_name'     => 'Client Assign Target', 'user_email' => 'assign-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $this->ajax('POST', '/users/ajax/save_user_client', ['user_id' => (string) $userId, 'client_id' => (string) $clientId]);

        /* Assert */
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_does_not_assign_an_unknown_client(): void
    {
        /* Arrange */
        $userId = $this->databaseInsert('ip_users', [
            'user_name'     => 'No Client Target', 'user_email' => 'noclient-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $this->ajax('POST', '/users/ajax/save_user_client', ['user_id' => (string) $userId, 'client_id' => '999999']);

        /* Assert */
        $this->assertDatabaseCount('ip_user_clients', 0);
    }

    #[Test]
    public function it_loads_the_user_client_table_for_an_existing_user(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Loaded Client Marker']);
        $userId   = $this->databaseInsert('ip_users', [
            'user_name'     => 'Table Load Target', 'user_email' => 'tableload-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),
            'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/load_user_client_table', ['user_id' => (string) $userId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Loaded Client Marker');
    }

    #[Test]
    public function it_renders_the_add_user_client_modal(): void
    {
        /* Arrange */
        $this->seedClient();

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/modal_add_user_client', []);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }
}
