<?php

namespace Modules\Projects\Tests\Unit;

use Modules\Crm\Models\Client;
use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * ProjectService Unit Tests.
 *
 * Test suite for ProjectService business logic methods.
 */
#[CoversClass(ProjectService::class)]
class ProjectServiceTest extends TestCase
{
    private ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectService();
    }

    /**
     * Test that service returns correct model class.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        /** Arrange & Act */
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->service);

        /** Assert */
        $this->assertEquals(Project::class, $modelClass);
    }

    /**
     * Test that create method creates a new project.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_project(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $data = [
            'client_id'    => $client->client_id,
            'project_name' => 'Test Project',
        ];

        /** Act */
        $project = $this->service->create($data);

        /** Assert */
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Test Project', $project->project_name);
        $this->assertEquals($client->client_id, $project->client_id);
        $this->assertDatabaseHas('ip_projects', [
            'project_name' => 'Test Project',
        ]);
    }

    /**
     * Test that update method updates existing project.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_project(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id'    => $client->client_id,
            'project_name' => 'Old Name',
        ]);

        $updateData = [
            'project_name' => 'Updated Name',
        ];

        /** Act */
        $result = $this->service->update($project->project_id, $updateData);

        /** Assert */
        $this->assertTrue($result);
        $this->assertDatabaseHas('ip_projects', [
            'project_id'   => $project->project_id,
            'project_name' => 'Updated Name',
        ]);
    }

    /**
     * Test that find method returns project.
     */
    #[Test]
    public function it_finds_project_by_id(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $found = $this->service->find($project->project_id);

        /** Assert */
        $this->assertInstanceOf(Project::class, $found);
        $this->assertEquals($project->project_id, $found->project_id);
    }

    /**
     * Test that findOrFail throws exception for non-existent project.
     */
    #[Test]
    public function it_throws_exception_when_project_not_found(): void
    {
        /** Arrange */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /** Act */
        $this->service->findOrFail(999999);
    }

    /**
     * Test that delete method deletes project.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_project(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $result = $this->service->delete($project->project_id);

        /** Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_projects', [
            'project_id' => $project->project_id,
        ]);
    }
}

/**
 * TaskService Unit Tests.
 *
 * Test suite for TaskService business logic methods.
 */
#[CoversClass(TaskService::class)]
class TaskServiceTest extends TestCase
{
    private TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskService();
    }

    /**
     * Test that service returns correct model class.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        /** Arrange & Act */
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->service);

        /** Assert */
        $this->assertEquals(Task::class, $modelClass);
    }

    /**
     * Test that create method creates a new task.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_task(): void
    {
        /** Arrange */
        $project = Project::factory()->create();
        $data = [
            'project_id' => $project->project_id,
            'task_name'  => 'Test Task',
            'task_status' => 1,
        ];

        /** Act */
        $task = $this->service->create($data);

        /** Assert */
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->task_name);
        $this->assertEquals($project->project_id, $task->project_id);
        $this->assertDatabaseHas('ip_tasks', [
            'task_name' => 'Test Task',
        ]);
    }

    /**
     * Test that create method creates task without project.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_task_without_project(): void
    {
        /** Arrange */
        $data = [
            'task_name'   => 'Standalone Task',
            'task_status' => 1,
        ];

        /** Act */
        $task = $this->service->create($data);

        /** Assert */
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Standalone Task', $task->task_name);
        $this->assertNull($task->project_id);
    }

    /**
     * Test that update method updates existing task.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_task(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_name' => 'Old Name',
        ]);

        $updateData = [
            'task_name' => 'Updated Name',
        ];

        /** Act */
        $result = $this->service->update($task->task_id, $updateData);

        /** Assert */
        $this->assertTrue($result);
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'   => $task->task_id,
            'task_name' => 'Updated Name',
        ]);
    }

    /**
     * Test that find method returns task.
     */
    #[Test]
    public function it_finds_task_by_id(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
        $found = $this->service->find($task->task_id);

        /** Assert */
        $this->assertInstanceOf(Task::class, $found);
        $this->assertEquals($task->task_id, $found->task_id);
    }

    /**
     * Test that findOrFail throws exception for non-existent task.
     */
    #[Test]
    public function it_throws_exception_when_task_not_found(): void
    {
        /** Arrange */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /** Act */
        $this->service->findOrFail(999999);
    }

    /**
     * Test that delete method deletes task.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_task(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
        $result = $this->service->delete($task->task_id);

        /** Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_tasks', [
            'task_id' => $task->task_id,
        ]);
    }
}

