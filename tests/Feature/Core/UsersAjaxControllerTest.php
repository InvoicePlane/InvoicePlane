<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\UsersAjaxController::class)]
class UsersAjaxControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected AuthUser $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('AuthUser', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_empty_json_when_user_query_is_empty(): void
    {
        $response = $this->get('/users/ajax/nameQuery/' . (1));

        $response->assertSuccessful();
        $response->assertJson([]);
    }

    #[Test]
    public function it_searches_users_by_name_with_trailing_wildcard(): void
    {
        $this->seedModel('User', ['user_name' => 'John Doe', 'user_active' => 1, 'user_type' => 1]);
        $this->seedModel('User', ['user_name' => 'Jane Doe', 'user_active' => 1, 'user_type' => 1]);
        $this->seedModel('User', ['user_name' => 'Bob Smith', 'user_active' => 1, 'user_type' => 1]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('J'));

        $data = $response->json();
        $this->assertCount(2, $data);
    }

    #[Test]
    public function it_searches_users_by_company_name(): void
    {
        $this->seedModel('User', [
            'user_name'    => 'John Doe',
            'user_company' => 'Acme Corp',
            'user_active'  => 1,
            'user_type'    => 1,
        ]);
        $this->seedModel('User', [
            'user_name'    => 'Jane Smith',
            'user_company' => 'Acme Industries',
            'user_active'  => 1,
            'user_type'    => 1,
        ]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('Acme'));

        $data = $response->json();
        $this->assertCount(2, $data);
    }

    #[Test]
    public function it_searches_users_with_leading_wildcard_when_permissive_search_enabled(): void
    {
        $this->seedModel('User', [
            'user_name'   => 'John Doe',
            'user_active' => 1,
            'user_type'   => 1,
        ]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('ohn') . '/' . (1));

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data));
    }

    #[Test]
    public function it_only_returns_active_users_in_name_query(): void
    {
        $this->seedModel('User', ['user_name' => 'Active User', 'user_active' => 1, 'user_type' => 1]);
        $this->seedModel('User', ['user_name' => 'Inactive User', 'user_active' => 0, 'user_type' => 1]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('User'));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Active', $data[0]['text']);
    }

    #[Test]
    public function it_filters_users_by_type(): void
    {
        $this->seedModel('User', ['user_name' => 'Admin User', 'user_active' => 1, 'user_type' => 1]);
        $this->seedModel('User', ['user_name' => 'Guest User', 'user_active' => 1, 'user_type' => 2]);

        $response = $this->get('/users/ajax/nameQuery/' . (2) . '/' . ('User'));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Guest', $data[0]['text']);
    }

    #[Test]
    public function it_escapes_percent_signs_in_user_search_query(): void
    {
        $this->seedModel('User', ['user_name' => '100% Solutions', 'user_active' => 1, 'user_type' => 1]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('100%'));

        $response->assertSuccessful();
    }

    #[Test]
    public function it_returns_five_most_recent_active_users(): void
    {
        $this->seedModelMany('User', 10, ['user_active' => 1]);

        $response = $this->get('/users/ajax/getLatest');

        $data = $response->json();
        $this->assertCount(5, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
    }

    #[Test]
    public function it_saves_permissive_search_users_preference(): void
    {
        $response = $this->get('/users/ajax/savePreference/' . ('1'));

        $response->assertSuccessful();
        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'enable_permissive_search_users',
            'setting_value' => '1',
        ]);
    }

    #[Test]
    public function it_rejects_invalid_permissive_search_users_preference(): void
    {
        $response = $this->get('/users/ajax/savePreference/' . ('2'));

        $this->assertDatabaseMissing('ip_settings', [
            'setting_key'   => 'enable_permissive_search_users',
            'setting_value' => '2',
        ]);
    }

    #[Test]
    public function it_saves_user_client_relationship_for_existing_user(): void
    {
        $user   = $this->seedModel('User');
        $client = $this->seedModel('tmpClient');

        $response = $this->post('/users/ajax/saveUserClient', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('ip_user_clients', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);
    }

    #[Test]
    public function it_does_not_duplicate_user_client_relationship(): void
    {
        $user   = $this->seedModel('User');
        $client = $this->seedModel('tmpClient');

        $this->seedModel('UserClient', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response = $this->post('/users/ajax/saveUserClient', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertEquals(1, UserClient::where('user_id', $user->user_id)
            ->where('client_id', $client->client_id)
            ->count());
    }

    #[Test]
    public function it_stores_user_client_in_session_for_new_user(): void
    {
        $client = $this->seedModel('tmpClient');

        $response = $this->post('/users/ajax/saveUserClient', [
            'user_id'   => null,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertArrayHasKey($client->client_id, session('user_clients'));
    }

    #[Test]
    public function it_loads_user_client_table_for_existing_user(): void
    {
        $user    = $this->seedModel('User');
        $clients = $this->seedModelMany('tmpClient', 3);

        foreach ($clients as $client) {
            $this->seedModel('UserClient', [
                'user_id'   => $user->user_id,
                'client_id' => $client->client_id,
            ]);
        }

        $response = $this->post('/users/ajax/loadUserClientTable', [
            'user_id' => $user->user_id,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('user_clients', function ($userClients): bool {
            return count($userClients) === 3;
        });
    }

    #[Test]
    public function it_loads_user_client_table_from_session_for_new_user(): void
    {
        $clients        = $this->seedModelMany('tmpClient', 2);
        $sessionClients = $clients->pluck('client_id')->toArray();

        session(['user_clients' => array_combine($sessionClients, $sessionClients)]);

        $response = $this->post('/users/ajax/loadUserClientTable');

        $response->assertSuccessful();
        $response->assertViewHas('user_clients', function ($userClients): bool {
            return count($userClients) === 2;
        });
    }

    #[Test]
    public function it_displays_modal_add_user_client_for_existing_user(): void
    {
        $user              = $this->seedModel('User');
        $assignedClients   = $this->seedModelMany('tmpClient', 2);
        $unassignedClients = $this->seedModelMany('tmpClient', 3);

        foreach ($assignedClients as $client) {
            $this->seedModel('UserClient', [
                'user_id'   => $user->user_id,
                'client_id' => $client->client_id,
            ]);
        }

        $response = $this->get('/users/ajax/modalAddUserClient/' . ($user->user_id));

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients): bool {
            return count($clients) === 3; // Only unassigned clients
        });
        $response->assertViewHas('user_id', $user->user_id);
    }

    #[Test]
    public function it_displays_all_clients_for_new_user_in_modal(): void
    {
        $this->seedModelMany('tmpClient', 5);

        $response = $this->get('/users/ajax/modalAddUserClient');

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients): bool {
            return count($clients) === 5;
        });
    }

    #[Test]
    public function it_excludes_session_clients_from_modal_for_new_user(): void
    {
        $clients        = $this->seedModelMany('tmpClient', 5);
        $sessionClients = [$clients->first()->client_id => $clients->first()->client_id];

        session(['user_clients' => $sessionClients]);

        $response = $this->get('/users/ajax/modalAddUserClient');

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients): bool {
            return count($clients) === 4;
        });
    }

    #[Test]
    public function it_html_escapes_user_names_in_search_results(): void
    {
        $this->seedModel('User', [
            'user_name'   => '<script>alert("xss")</script>',
            'user_active' => 1,
            'user_type'   => 1,
        ]);

        $response = $this->get('/users/ajax/nameQuery/' . (1) . '/' . ('script'));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringNotContainsString('<script>', $data[0]['text']);
    }
}
