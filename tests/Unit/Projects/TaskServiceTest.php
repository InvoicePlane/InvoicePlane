<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ProjectService Unit Tests.
 *
 * Test suite for ProjectService business logic methods.
 */
#[CoversClass(Mdl_Tasks::class)]
class TaskServiceTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private \Unit\Projects\TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Service class does not exist — CI3 model layer, no Laravel service layer available');
        $this->service = new TaskService();
    }

    /**
     * Test that service returns correct model class.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        /* Arrange & Act */
        $reflection = new ReflectionClass($this->service);
        $method     = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->service);

        /* Assert */
        $this->assertEquals(Task::class, $modelClass);
    }

    /**
     * Test that create method creates a new task.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_task(): void
    {
        /* Arrange */
        $project = $this->seedModel('Project');
        $data    = [
            'project_id'  => $project->project_id,
            'task_name'   => 'Test Task',
            'task_status' => 1,
        ];

        /* Act */
        $task = $this->service->create($data);

        /* Assert */
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
        /* Arrange */
        $data = [
            'task_name'   => 'Standalone Task',
            'task_status' => 1,
        ];

        /* Act */
        $task = $this->service->create($data);

        /* Assert */
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
        /* Arrange */
        $task = $this->seedModel('Task', [
            'task_name' => 'Old Name',
        ]);

        $updateData = [
            'task_name' => 'Updated Name',
        ];

        /* Act */
        $result = $this->service->update($task->task_id, $updateData);

        /* Assert */
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
        /* Arrange */
        $task = $this->seedModel('Task');

        /* Act */
        $found = $this->service->find($task->task_id);

        /* Assert */
        $this->assertInstanceOf(Task::class, $found);
        $this->assertEquals($task->task_id, $found->task_id);
    }

    /**
     * Test that findOrFail throws exception for non-existent task.
     */
    #[Test]
    public function it_throws_exception_when_task_not_found(): void
    {
        /* Arrange */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /* Act */
        $this->service->findOrFail(999999);
    }

    /**
     * Test that delete method deletes task.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_task(): void
    {
        /* Arrange */
        $task = $this->seedModel('Task');

        /* Act */
        $result = $this->service->delete($task->task_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_tasks', [
            'task_id' => $task->task_id,
        ]);
    }
}
