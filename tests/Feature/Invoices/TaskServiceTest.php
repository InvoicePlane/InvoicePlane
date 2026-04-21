<?php

namespace Tests\Feature\Invoices;

use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

/**
 * ProjectService Unit Tests.
 *
 * Test suite for ProjectService business logic methods.
 */
#[CoversClass(ProjectService::class)]

class TaskServiceTest extends TestCase
{
    use InteractsWithDatabase;

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

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_tasks_with_relations_paginated(): void
    {
        /* Arrange */
        $project = $this->seedModel('Project');
        $taxRate = $this->seedModel('\Modules\Products\Models\TaxRate');

        $this->seedModelMany('Task', 3, [
            'project_id'       => $project->project_id,
            'task_tax_rate_id' => $taxRate->tax_rate_id,
        ]);

        /* Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $this->assertGreaterThanOrEqual(3, $result->total());
        $this->assertTrue($result->first()->relationLoaded('project'));
        $this->assertTrue($result->first()->relationLoaded('taxRate'));
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_tasks_by_name(): void
    {
        /* Arrange */
        $this->seedModel('Task', ['task_name' => 'Zebra Task']);
        $this->seedModel('Task', ['task_name' => 'Alpha Task']);
        $this->seedModel('Task', ['task_name' => 'Beta Task']);

        /* Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $tasks = $result->items();
        $this->assertGreaterThanOrEqual(3, count($tasks));
        // First task should be Alpha (alphabetically first)
        $this->assertEquals('Alpha Task', $tasks[0]->task_name);
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        /* Arrange */
        $this->seedModelMany('Task', 10);

        /* Act */
        $result = $this->service->getAllWithRelations(['project'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }
}
