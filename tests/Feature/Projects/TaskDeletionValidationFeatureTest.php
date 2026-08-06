<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ProjectsController deletion side effect on tasks.
 *
 * Projects::delete() calls Mdl_tasks::update_on_project_delete(), which does
 * not delete the project's tasks — it sets each task's project_id to null
 * and leaves the task row in place. Only then is the project itself deleted.
 */
class TaskDeletionValidationFeatureTest extends AbstractTestCase
{
    private int $projectId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $clientId        = $this->seedClient(['client_name' => 'Task Deletion Client']);
        $this->projectId = $this->databaseInsert('ip_projects', [
            'client_id'    => $clientId,
            'project_name' => 'Deletion Test Project',
        ]);
    }

    #[Test]
    public function it_orphans_the_projects_tasks_instead_of_deleting_them(): void
    {
        /* Arrange */
        $taskId = $this->databaseInsert('ip_tasks', [
            'project_id'       => $this->projectId,
            'task_name'        => 'Task Surviving Project Delete',
            'task_description' => '',
            'task_price'       => '0.00',
            'task_finish_date' => date('Y-m-d'),
            'task_status'      => 1,
            'tax_rate_id'      => 0,
        ]);

        /* Act */
        $response = $this->post('/projects/delete/' . $this->projectId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $this->projectId]);
        // ip_tasks.project_id is a non-nullable column, so the null the
        // model assigns is coerced to 0 on write, not a real SQL NULL.
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $taskId, 'project_id' => 0]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/projects');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/projects] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
