<?php

namespace Tests\Unit\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the TasksServiceTest module via CI3 HTTP harness.
 */
class TasksServiceTest extends AbstractTestCase
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
        $clientId = $this->seedClient(['client_name' => 'Tasks Service Client']);
        $projectId = $this->databaseInsert('ip_projects', [
            'client_id'            => $clientId,
            'project_name'         => 'Tasks Service Project',
        ]);
        $this->databaseInsert('ip_tasks', [
            'project_id'      => $projectId,
            'task_name'        => 'Service Task Delta',
            'task_description' => '',
            'task_price'       => '0.00',
            'task_finish_date' => date('Y-m-d'),
            'task_status'      => 1,
            'tax_rate_id'      => 0,
        ]);

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_tasks', ['task_name' => 'Service Task Delta']);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/tasks');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/tasks] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
