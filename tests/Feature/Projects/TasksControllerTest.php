<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TasksController Feature Tests.
 *
 * Test suite for TasksController covering task list display.
 */
class TasksControllerTest extends AbstractTestCase
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
        $clientId = $this->seedClient(['client_name' => 'Task Test Client']);
        $projectId = $this->databaseInsert('ip_projects', [
            'client_id'            => $clientId,
            'project_name'         => 'Task Test Project',
        ]);
        $this->databaseInsert('ip_tasks', [
            'project_id'    => $projectId,
            'task_name'        => 'Test Task Gamma',
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
        $this->assertResponseBodyContains($response, 'Test Task Gamma');
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
