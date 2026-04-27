<?php

namespace Tests\Feature\Core;

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
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $response = $this->get(route('user_clients.index'));

        /* Assert */
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_displays_user_clients_for_a_user(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->get(route('user_clients.user', ['id' => $user->id]));

        /* Assert */
        $response->assertStatus(200);
        $response->assertViewIs('user_clients.new');
        $response->assertViewHas('user');
        $response->assertViewHas('user_clients');
    }

    #[Test]
    public function it_redirects_to_users_when_user_not_found(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $response = $this->get(route('user_clients.user', ['id' => 99999]));

        /* Assert */
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_redirects_to_custom_values_when_user_id_is_null(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $response = $this->get(route('user_clients.create'));

        /* Assert */
        $response->assertRedirect(route('custom_values'));
    }

    #[Test]
    public function it_deletes_user_client_and_redirects(): void
    {
        /* Arrange */
        $user       = $this->seedModel('User');
        $client     = $this->seedModel('\Modules\Clients\Models\tmpClient');
        $userClient = $this->seedModel('UserClient', [
            'user_id'   => $user->id,
            'client_id' => $client->id,
        ]);

        /* Act */
        $response = $this->get(route('user_clients.delete', ['user_client_id' => $userClient->id]));

        /* Assert */
        $response->assertRedirect(route('user_clients.user', ['id' => $user->id]));
        $this->assertDatabaseMissing('ip_user_clients', ['id' => $userClient->id]);
    }
}
