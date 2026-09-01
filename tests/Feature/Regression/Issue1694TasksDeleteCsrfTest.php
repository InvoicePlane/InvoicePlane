<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Tasks::delete() (application/modules/tasks).
 */
#[Group('security')]
class Issue1694TasksDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_task_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $taskId = $this->seedTask();

        /* Act */
        $response = $this->postWithValidCsrfToken('/tasks/delete/' . $taskId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('tasks/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $taskId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $taskId = $this->seedTask();

        /* Act */
        $response = $this->postWithoutCsrfToken('/tasks/delete/' . $taskId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $taskId]);
    }

    private function seedTask(): int
    {
        $projectId = (int) $this->seedModel('Project', ['client_id' => $this->seedClient()])->project_id;

        return (int) $this->seedModel('Task', ['project_id' => $projectId])->task_id;
    }
}
