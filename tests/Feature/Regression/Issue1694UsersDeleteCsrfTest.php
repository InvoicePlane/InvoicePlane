<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Users::delete() (application/modules/users).
 *
 * Users::delete() only removes the row when $id != 1, so a secondary user is
 * seeded and the acting admin stays user 1.
 */
#[Group('security')]
class Issue1694UsersDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_user_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $userId = $this->seedSecondaryUser();

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/' . $userId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('users/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_users', ['user_id' => $userId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $userId = $this->seedSecondaryUser();

        /* Act */
        $response = $this->postWithoutCsrfToken('/users/delete/' . $userId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_users', ['user_id' => $userId]);
    }

    private function seedSecondaryUser(): int
    {
        $userId = (int) $this->seedModel('User')->user_id;
        self::assertGreaterThan(1, $userId, 'Seeded user must not be the primary admin (id 1).');

        return $userId;
    }
}
