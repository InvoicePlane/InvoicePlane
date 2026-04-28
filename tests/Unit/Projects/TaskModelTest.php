<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\CiTestCase;
/**
 * TaskModel Unit Tests.
 *
 * Test suite for Mdl_Tasks model methods.
 */
#[CoversClass(Mdl_Tasks::class)]
class TaskModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('tasks/mdl_tasks');
        $this->model = $this->CI->mdl_tasks;
    }

    /**
     * Test that service returns correct model class.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        /* Arrange & Act */
        $reflection = new ReflectionClass($this->model);
        $method     = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->model);

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
        $data    = [
            'project_id'  => $project->project_id,
            'task_name'   => 'Test Task',
            'task_status' => 1,
        ];

        /* Act */
        $task = $this->model->create($data);

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
        $task = $this->model->create($data);

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
            'task_name' => 'Old Name',
        ]);

        $updateData = [
            'task_name' => 'Updated Name',
        ];

        /* Act */
        $result = $this->model->update($task->task_id, $updateData);

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

        /* Act */
        $found = $this->model->find($task->task_id);

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
        $this->model->findOrFail(999999);
    }

    /**
     * Test that delete method deletes task.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_task(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->model->delete($task->task_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_tasks', [
            'task_id' => $task->task_id,
        ]);
    }


    // Migrated from BckpTaskServiceTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_gets_all_tasks_with_relations_paginated(): void
    {
        /* Arrange */

            'project_id'       => $project->project_id,
            'task_tax_rate_id' => $taxRate->tax_rate_id,
        ]);

        /* Act */
        $result = $this->model->getAllWithRelations();

        /* Assert */
        $this->assertGreaterThanOrEqual(3, $result->total());
        $this->assertTrue($result->first()->relationLoaded('project'));
        $this->assertTrue($result->first()->relationLoaded('taxRate'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_orders_tasks_by_name(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->model->getAllWithRelations();

        /* Assert */
        $tasks = $result->items();
        $this->assertGreaterThanOrEqual(3, count($tasks));
        // First task should be Alpha (alphabetically first)
        $this->assertEquals('Alpha Task', $tasks[0]->task_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->model->getAllWithRelations(['project'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }

}
