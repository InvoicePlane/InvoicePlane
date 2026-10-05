<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

final class SessionInvalidationTest extends AbstractTestCase
{
    #[Test]
    public function it_accepts_a_session_bound_to_the_current_password(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_rejects_an_old_session_after_the_password_changes(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->databaseUpdate('ip_users', [
            'user_password'     => password_hash('NewPassword456!', PASSWORD_DEFAULT),
            'user_auth_version' => 1,
        ], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
    }

    #[Test]
    public function it_rejects_an_old_session_when_only_the_password_hash_changes(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->databaseUpdate('ip_users', [
            'user_password' => password_hash('AnotherPassword789!', PASSWORD_DEFAULT),
        ], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
    }

    private function seedAdmin(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'          => 1,
            'user_name'          => 'Admin ' . bin2hex(random_bytes(3)),
            'user_email'         => 'admin+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('OldPassword123!', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
