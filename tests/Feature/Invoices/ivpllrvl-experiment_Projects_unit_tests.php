<?php

namespace Modules\Projects\Tests\Unit;

use Modules\Crm\Models\Client;
use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
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
        $reflection = new ReflectionClass($this->service);
        $method     = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->service);

        /* Assert */
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
        $data   = [
            'client_id'    => $client->client_id,
            'project_name' => 'Test Project',
        ];

        /** Act */
        $project = $this->service->create($data);

        /* Assert */
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
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id'    => $client->client_id,
            'project_name' => 'Old Name',
        ]);

        $updateData = [
            'project_name' => 'Updated Name',
        ];

        /** Act */
        $result = $this->service->update($project->project_id, $updateData);

        /* Assert */
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
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $found = $this->service->find($project->project_id);

        /* Assert */
        $this->assertInstanceOf(Project::class, $found);
        $this->assertEquals($project->project_id, $found->project_id);
    }

    /**
     * Test that findOrFail throws exception for non-existent project.
     */
    #[Test]
    public function it_throws_exception_when_project_not_found(): void
    {
        /* Arrange */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /* Act */
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
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $result = $this->service->delete($project->project_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_projects', [
            'project_id' => $project->project_id,
        ]);
    }
}

/**
 * TaskService Deletion Validation Tests.
 *
 * Tests business rules for task deletion:
 * - Tasks assigned to invoices cannot be deleted
 */
#[CoversClass(TaskService::class)]
class TaskDeletionValidationTest extends AbstractServiceTestCase
{
    private TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskService();
    }

    /**
     * Test that a task not assigned to an invoice can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_task_not_assigned_to_invoice(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_name'   => 'Unassigned Task',
            'invoice_id'  => null, // Not assigned to any invoice
            'task_status' => 1,
        ]);

        /** Act */
        $canDelete  = $this->service->canDelete($task->task_id);
        $isAssigned = $this->service->isAssignedToInvoice($task->task_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Task without invoice assignment should be deletable');
        $this->assertFalse($isAssigned, 'Task should not be marked as assigned');
    }

    /**
     * Test that a task assigned to an invoice cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_task_assigned_to_invoice(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $task = Task::factory()->create([
            'task_name'   => 'Invoiced Task',
            'invoice_id'  => $invoice->invoice_id, // Assigned to invoice
            'task_status' => 3, // Complete
        ]);

        /** Act */
        $canDelete  = $this->service->canDelete($task->task_id);
        $isAssigned = $this->service->isAssignedToInvoice($task->task_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Task assigned to invoice should NOT be deletable');
        $this->assertTrue($isAssigned, 'Task should be marked as assigned to invoice');
    }

    /**
     * Test that task assignment check works correctly.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_correctly_identifies_task_invoice_assignment(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $assignedTask = Task::factory()->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        $unassignedTask = Task::factory()->create([
            'invoice_id' => null,
        ]);

        /** Act */
        $assignedIsAssigned   = $this->service->isAssignedToInvoice($assignedTask->task_id);
        $unassignedIsAssigned = $this->service->isAssignedToInvoice($unassignedTask->task_id);

        /* Assert */
        $this->assertTrue($assignedIsAssigned, 'Assigned task should return true');
        $this->assertFalse($unassignedIsAssigned, 'Unassigned task should return false');
    }

    /**
     * Test that non-existent task can be "deleted" (returns true).
     */
    #[Group('edge-cases')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_true_for_nonexistent_task(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $canDelete  = $this->service->canDelete($nonexistentId);
        $isAssigned = $this->service->isAssignedToInvoice($nonexistentId);

        /* Assert */
        $this->assertTrue($canDelete, 'Non-existent task should return true for canDelete');
        $this->assertFalse($isAssigned, 'Non-existent task should return false for isAssigned');
    }

    /**
     * Test task status doesn't affect deletion rule - only invoice assignment matters.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_regardless_of_task_status(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        // Create tasks with different statuses but all assigned to invoice
        $statuses = [1, 2, 3, 4]; // Not Started, In Progress, Complete, On Hold

        foreach ($statuses as $status) {
            $task = Task::factory()->create([
                'task_status' => $status,
                'invoice_id'  => $invoice->invoice_id,
            ]);

            /** Act */
            $canDelete = $this->service->canDelete($task->task_id);

            /* Assert */
            $this->assertFalse(
                $canDelete,
                "Task with status {$status} assigned to invoice should not be deletable"
            );
        }
    }

    /**
     * Test that completed task without invoice can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_completed_task_without_invoice(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_status' => 3, // Complete
            'invoice_id'  => null, // Not assigned
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($task->task_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Completed task without invoice should be deletable');
    }

    /**
     * Test multiple tasks with same invoice.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_all_tasks_assigned_to_same_invoice(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        // Create multiple tasks assigned to same invoice
        $tasks = Task::factory()->count(3)->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        /* Act & Assert */
        foreach ($tasks as $task) {
            $canDelete = $this->service->canDelete($task->task_id);
            $this->assertFalse(
                $canDelete,
                'Each task assigned to invoice should not be deletable'
            );
        }
    }

    /**
     * Test that task can be deleted after invoice reference is removed.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_invoice_reference_removed(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $task = Task::factory()->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($task->task_id));

        // Remove invoice reference
        $task->invoice_id = null;
        $task->save();

        /** Act */
        $canDelete = $this->service->canDelete($task->task_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Task should be deletable after invoice reference removed');
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
        /** Arrange */
        $project = Project::factory()->create();
        $data    = [
            'project_id'  => $project->project_id,
            'task_name'   => 'Test Task',
            'task_status' => 1,
        ];

        /** Act */
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
        /** Arrange */
        $data = [
            'task_name'   => 'Standalone Task',
            'task_status' => 1,
        ];

        /** Act */
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
        /** Arrange */
        $task = Task::factory()->create([
            'task_name' => 'Old Name',
        ]);

        $updateData = [
            'task_name' => 'Updated Name',
        ];

        /** Act */
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
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
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
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
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
        /** Arrange */
        $project = Project::factory()->create();
        $taxRate = \Modules\Products\Models\TaxRate::factory()->create();

        Task::factory()->count(3)->create([
            'project_id'       => $project->project_id,
            'task_tax_rate_id' => $taxRate->tax_rate_id,
        ]);

        /** Act */
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
        Task::factory()->create(['task_name' => 'Zebra Task']);
        Task::factory()->create(['task_name' => 'Alpha Task']);
        Task::factory()->create(['task_name' => 'Beta Task']);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /** Assert */
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
        Task::factory()->count(10)->create();

        /** Act */
        $result = $this->service->getAllWithRelations(['project'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }
}

