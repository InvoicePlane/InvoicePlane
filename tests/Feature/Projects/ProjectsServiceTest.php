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
