<?php

namespace Modules\Projects\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Projects\app\Models\Task;
use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProjectsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectsService();
    }

    #[Test]
    public function it_returns_tasks_for_a_project(): void
    {
        // Arrange
        $project = Project::create([
            'project_name' => 'Test Project',
        ]);

        Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);
        Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 2,
            'project_id'       => $project->project_id,
        ]);

        // Create a task for a different project
        $otherProject = Project::create([
            'project_name' => 'Other Project',
        ]);
        Task::create([
            'task_name'        => 'Other Task',
            'task_description' => 'Other Description',
            'task_status'      => 1,
            'project_id'       => $otherProject->project_id,
        ]);

        // Act
        $result = $this->service->getTasks($project->project_id);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Task 1', $result[0]->task_name);
        $this->assertEquals('Task 2', $result[1]->task_name);
    }

    #[Test]
    public function it_returns_empty_array_when_project_has_no_tasks(): void
    {
        // Arrange
        $project = Project::create([
            'project_name' => 'Empty Project',
        ]);

        // Act
        $result = $this->service->getTasks($project->project_id);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_null(): void
    {
        // Act
        $result = $this->service->getTasks(null);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_zero(): void
    {
        // Act
        $result = $this->service->getTasks(0);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_false(): void
    {
        // Act
        $result = $this->service->getTasks(false);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
    }
}

class TasksServiceTest extends TestCase
{
    use RefreshDatabase;

    private TasksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TasksService();
    }

    #[Test]
    public function it_filters_tasks_by_name(): void
    {
        // Arrange
        Task::create([
            'task_name'        => 'Design Homepage',
            'task_description' => 'Create wireframes',
            'task_status'      => 1,
        ]);
        Task::create([
            'task_name'        => 'Build API',
            'task_description' => 'Design endpoints',
            'task_status'      => 1,
        ]);
        Task::create([
            'task_name'        => 'Testing',
            'task_description' => 'Write tests',
            'task_status'      => 1,
        ]);

        // Act
        $result = $this->service->byTask('Design');

        // Assert: Should match both task_name and task_description
        $this->assertInstanceOf(TasksService::class, $result);
    }

    #[Test]
    public function it_returns_null_when_getting_invoice_for_null_task_id(): void
    {
        // Act
        $result = $this->service->getInvoiceForTask(null);

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_task_has_no_associated_invoice(): void
    {
        // Arrange
        $task = Task::create([
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
        ]);

        // Act
        $result = $this->service->getInvoiceForTask($task->task_id);

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_empty_array_when_getting_tasks_to_invoice_with_null_id(): void
    {
        // Act
        $result = $this->service->getTasksToInvoice(null);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_tasks_to_invoice_for_unassigned_projects(): void
    {
        // Arrange: Create tasks with no project (project_id = 0) and status 3 (completed)
        Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDays(2),
        ]);
        Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDay(),
        ]);
        // Task with different status should not be included
        Task::create([
            'task_name'        => 'Task 3',
            'task_description' => 'Description 3',
            'task_status'      => 1,
            'project_id'       => 0,
        ]);

        // Create invoice
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);
        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        // Act
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_does_nothing_when_updating_on_invoice_delete_with_null_id(): void
    {
        // Arrange
        $task = Task::create([
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
        ]);

        // Act
        $this->service->updateOnInvoiceDelete(null);

        // Assert: Task should remain unchanged
        $task->refresh();
        $this->assertEquals(1, $task->task_status);
    }

    #[Test]
    public function it_does_nothing_when_updating_on_project_delete_with_null_id(): void
    {
        // Arrange
        $project = Project::create([
            'project_name' => 'Test Project',
        ]);
        $task = Task::create([
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        // Act
        $this->service->updateOnProjectDelete(null);

        // Assert: Task should remain unchanged
        $task->refresh();
        $this->assertEquals($project->project_id, $task->project_id);
    }

    #[Test]
    public function it_clears_project_association_when_project_is_deleted(): void
    {
        // Arrange
        $project = Project::create([
            'project_name' => 'Test Project',
        ]);
        $task1 = Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);
        $task2 = Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        // Act
        $this->service->updateOnProjectDelete($project->project_id);

        // Assert
        $task1->refresh();
        $task2->refresh();
        $this->assertNull($task1->project_id);
        $this->assertNull($task2->project_id);
    }

    #[Test]
    public function it_returns_status_array(): void
    {
        // Act
        $statuses = $this->service->statuses();

        // Assert
        $this->assertIsArray($statuses);
        $this->assertNotEmpty($statuses);
    }
}
