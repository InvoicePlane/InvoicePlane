<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * GHSA-h4xh-4jwc-485r: Mdl_clients::can_user_access() and Mdl_projects::can_user_access() are the object-level gate for
 * client and project pages: an administrator (type 1) may see everything, a guest user (type 2) only the clients
 * assigned to them in ip_user_clients (and the projects of those clients), anyone else nothing. The existing IDOR tests
 * cover the user-client assignment endpoints; nothing exercised these two methods' deny branches.
 */
final class ClientProjectAccessTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    #[Test]
    public function it_lets_an_administrator_access_any_client_and_project(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $projectId = $this->seedProject($clientId);
        $ci        = $this->sessionFor(1, 999);

        /* Act + Assert */
        self::assertTrue($ci->mdl_clients->can_user_access($clientId));
        self::assertTrue($ci->mdl_projects->can_user_access($projectId));
    }

    #[Test]
    public function it_lets_a_guest_user_access_only_their_assigned_client_and_its_projects(): void
    {
        /* Arrange */
        $assigned = $this->seedClient();
        $other    = $this->seedClient();
        $userId   = $this->seedUser(2);
        $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $assigned]);
        $ownProject = $this->seedProject($assigned);
        $foreign    = $this->seedProject($other);
        $ci         = $this->sessionFor(2, $userId);

        /* Act + Assert */
        self::assertTrue($ci->mdl_clients->can_user_access($assigned));
        self::assertFalse($ci->mdl_clients->can_user_access($other), 'A client that is not assigned must be refused.');
        self::assertTrue($ci->mdl_projects->can_user_access($ownProject));
        self::assertFalse($ci->mdl_projects->can_user_access($foreign), 'A project of an unassigned client must be refused.');
    }

    #[Test]
    public function it_refuses_a_guest_user_a_project_that_does_not_exist(): void
    {
        /* Arrange */
        $userId = $this->seedUser(2);
        $ci     = $this->sessionFor(2, $userId);

        /* Act + Assert */
        self::assertFalse($ci->mdl_projects->can_user_access(987654));
    }

    #[Test]
    public function it_refuses_a_regular_user_every_client_and_project_even_when_assigned(): void
    {
        /* Arrange: type 3 has no client access at all, even with a (stray) assignment row */
        $clientId = $this->seedClient();
        $userId   = $this->seedUser(3);
        $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
        $projectId = $this->seedProject($clientId);
        $ci        = $this->sessionFor(3, $userId);

        /* Act + Assert */
        self::assertFalse($ci->mdl_clients->can_user_access($clientId));
        self::assertFalse($ci->mdl_projects->can_user_access($projectId));
    }

    private function sessionFor(int $userType, int $userId): object
    {
        $ci          = $this->bootCodeIgniter();
        $ci->session = new class ($userType, $userId) {
            public function __construct(private int $type, private int $id) {}

            public function userdata(string $key): mixed
            {
                return ['user_type' => (string) $this->type, 'user_id' => (string) $this->id][$key] ?? null;
            }
        };
        $ci->load->model(['clients/mdl_clients', 'projects/mdl_projects']);

        return $ci;
    }

    private function seedProject(int $clientId): int
    {
        return $this->databaseInsert('ip_projects', ['client_id' => $clientId, 'project_name' => 'Project ' . bin2hex(random_bytes(3))]);
    }

    private function seedUser(int $type): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'         => $type, 'user_name' => 'Access ' . bin2hex(random_bytes(3)),
            'user_email'        => 'access+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'     => password_hash('secret123', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(8)),
            'user_language'     => 'system', 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
