<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\UserClientsController::class)]
class UserClientsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_redirects_to_users_from_index(): void
    {
        /* Act */
        $response = $this->get('/user_clients/index');

        /* Assert */
        $response->assertRedirect('/users');
    }

    #[Test]
    public function it_displays_user_clients_for_a_user(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->get('/user_clients/user/' . ($user->id));

        /* Assert */
        $response->assertStatus(200);
        $response->assertViewIs('user_clients.new');
        $response->assertViewHas('user');
        $response->assertViewHas('user_clients');
    }

    #[Test]
    public function it_redirects_to_users_when_user_not_found(): void
    {
        /* Act */
        $response = $this->get('/user_clients/user/' . (99999));

        /* Assert */
        $response->assertRedirect('/users');
    }

    #[Test]
    public function it_redirects_to_custom_values_when_user_id_is_null(): void
    {
        /* Act */
        $response = $this->get('/user_clients/create');

        /* Assert */
        $response->assertRedirect('/custom_values');
    }

    #[Test]
    public function it_deletes_user_client_and_redirects(): void
    {
        /* Arrange */
        $user       = $this->seedModel('User');
        $client     = $this->seedModel('tmpClient');
        $userClient = $this->seedModel('UserClient', [
            'user_id'   => $user->id,
            'client_id' => $client->id,
        ]);

        /* Act */
        $response = $this->get('/user_clients/delete/' . ($userClient->id));

        /* Assert */
        $response->assertRedirect('/user_clients/user/' . ($user->id));
        $this->assertDatabaseMissing('ip_user_clients', ['id' => $userClient->id]);
    }
}
