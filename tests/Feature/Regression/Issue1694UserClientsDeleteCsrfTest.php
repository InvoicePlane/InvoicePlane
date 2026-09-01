<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: User_clients::delete() (application/modules/user_clients).
 *
 * Mdl_user_clients::get_by_id() INNER-joins ip_users and ip_clients, so the
 * mapping is seeded against a real secondary user and a real client.
 */
#[Group('security')]
class Issue1694UserClientsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    private function seedUserClient(): int
    {
        $userId   = (int) $this->seedModel('User')->user_id;
        $clientId = $this->seedClient();

        return (int) $this->seedModel('UserClient', [
            'user_id'   => $userId,
            'client_id' => $clientId,
        ])->user_client_id;
    }

    #[Test]
    public function it_deletes_a_user_client_link_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $userClientId = $this->seedUserClient();

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/delete/' . $userClientId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('user_clients/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $userClientId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $userClientId = $this->seedUserClient();

        /* Act */
        $response = $this->postWithoutCsrfToken('/user_clients/delete/' . $userClientId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $userClientId]);
    }
}
