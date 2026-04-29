<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tasks;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ProjectsController Feature Tests.
 *
 * Test suite for ProjectsController covering CRUD operations
 * with data integrity validation and business logic verification.
 */
#[CoversClass(Tasks::class)]
class TaskDeletionValidationFeatureTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        get_instance()->load->model('tasks/mdl_tasks');
        $this->model = get_instance()->mdl_tasks;
    }

    /**
     * Test that task without invoice assignment can be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_task_without_invoice_assignment(): void
    {
        /* Arrange */
        $task = $this->seedModel('Task', [
            'task_name'   => 'Deletable Task',
            'invoice_id'  => null, // Not assigned to invoice
            'task_status' => 1,
        ]);

        /* Act */
        $response = $this->post('/tasks/delete/' . ($task->task_id));

        /* Assert */
        $response->assertRedirect('/tasks/index');
        $response->assertSessionHas('alert_success');

        // Verify task was actually deleted
        $this->assertDatabaseMissing('ip_tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    /**
     * Test that task assigned to invoice cannot be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_task_assigned_to_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        $task = $this->seedModel('Task', [
            'task_name'   => 'Invoiced Task',
            'invoice_id'  => $invoice->invoice_id, // Assigned to invoice
            'task_status' => 3, // Complete
        ]);

        /* Act */
        $response = $this->post('/tasks/delete/' . ($task->task_id));

        /* Assert */
        $response->assertRedirect('/tasks/index');
        $response->assertSessionHas('alert_error');

        // Verify task still exists in database
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'    => $task->task_id,
            'task_name'  => 'Invoiced Task',
            'invoice_id' => $invoice->invoice_id,
        ]);
    }

    /**
     * Test that completed task without invoice can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_completed_task_without_invoice(): void
    {
        /* Arrange */
        $task = $this->seedModel('Task', [
            'task_status' => 3, // Complete
            'invoice_id'  => null,
        ]);

        /* Act */
        $response = $this->post('/tasks/delete/' . ($task->task_id));

        /* Assert */
        $response->assertRedirect('/tasks/index');
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $task->task_id]);
    }

    /**
     * Test that task status doesn't affect deletion - only invoice assignment.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_regardless_of_status_when_assigned(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        // Test with different statuses
        $statuses = [
            1 => 'Not Started',
            2 => 'In Progress',
            3 => 'Complete',
            4 => 'On Hold',
        ];

        foreach ($statuses as $statusId => $statusName) {
            $task = $this->seedModel('Task', [
                'task_name'   => "Task - {$statusName}",
                'task_status' => $statusId,
                'invoice_id'  => $invoice->invoice_id,
            ]);

            /* Act */
            $response = $this->post('/tasks/delete/' . ($task->task_id));

            /* Assert */
            $response->assertRedirect('/tasks/index');
            $response->assertSessionHas('alert_error');
            $this->assertDatabaseHas('ip_tasks', [
                'task_id'    => $task->task_id,
                'invoice_id' => $invoice->invoice_id,
            ]);
        }
    }

    /**
     * Test deletion with invalid task ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_task_id(): void
    {
        /* Arrange */
        $invalidId = -1;

        /* Act */
        $response = $this->post('/tasks/delete/' . ($invalidId));

        /* Assert */
        $response->assertRedirect('/tasks/index');
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test deletion with non-existent task ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_task_id(): void
    {
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->post('/tasks/delete/' . ($nonexistentId));

        /* Assert */
        $response->assertRedirect('/tasks/index');
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test that task can be deleted after invoice reference is removed.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_invoice_reference_removed(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        $task = $this->seedModel('Task', [
            'invoice_id' => $invoice->invoice_id,
        ]);

        // Initially cannot delete
        $response1 = $this->post('/tasks/delete/' . ($task->task_id));
        $response1->assertSessionHas('alert_error');

        // Remove invoice reference
        $task->invoice_id = null;
        $task->save();

        /* Act */
        $response2 = $this->post('/tasks/delete/' . ($task->task_id));

        /* Assert */
        $response2->assertRedirect('/tasks/index');
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $task->task_id]);
    }

    /**
     * Test multiple tasks with same invoice cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_all_tasks_with_same_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        $tasks = $this->seedModelMany('Task', 3, [
            'invoice_id' => $invoice->invoice_id,
        ]);

        /* Act & Assert */
        foreach ($tasks as $task) {
            $response = $this->post('/tasks/delete/' . ($task->task_id));

            $response->assertRedirect('/tasks/index');
            $response->assertSessionHas('alert_error');
            $this->assertDatabaseHas('ip_tasks', ['task_id' => $task->task_id]);
        }
    }

    /**
     * Test that unassigned tasks can all be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_of_all_unassigned_tasks(): void
    {
        /* Arrange */
        $tasks = $this->seedModelMany('Task', 3, [
            'invoice_id' => null,
        ]);

        /* Act & Assert */
        foreach ($tasks as $task) {
            $response = $this->post('/tasks/delete/' . ($task->task_id));

            $response->assertRedirect('/tasks/index');
            $response->assertSessionHas('alert_success');
            $this->assertDatabaseMissing('ip_tasks', ['task_id' => $task->task_id]);
        }
    }

    /**
     * Test mixed scenario: some tasks deletable, some not.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_mixed_deletable_and_non_deletable_tasks(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        $deletableTask    = $this->seedModel('Task', ['invoice_id' => null]);
        $nonDeletableTask = $this->seedModel('Task', ['invoice_id' => $invoice->invoice_id]);

        /* Act */
        $response1 = $this->post('/tasks/delete/' . ($deletableTask->task_id));
        $response2 = $this->post('/tasks/delete/' . ($nonDeletableTask->task_id));

        /* Assert */
        // Deletable task deleted
        $response1->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $deletableTask->task_id]);

        // Non-deletable task preserved
        $response2->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $nonDeletableTask->task_id]);
    }


    // Migrated from BckpTaskDeletionValidationTest.php
    #[Test]
    public function it_allows_deletion_of_task_not_assigned_to_invoice(): void
    {
        /* Arrange */
        $task = $this->seedModel('Task', [
            'task_name'   => 'Unassigned Task',
            'invoice_id'  => null, // Not assigned to any invoice
            'task_status' => 1,
        ]);

        /* Act */
        $canDelete  = $this->model->canDelete($task->task_id);
        $isAssigned = $this->model->isAssignedToInvoice($task->task_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Task without invoice assignment should be deletable');
        $this->assertFalse($isAssigned, 'Task should not be marked as assigned');
    }

    #[Test]
    public function it_correctly_identifies_task_invoice_assignment(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        $assignedTask = $this->seedModel('Task', [
            'invoice_id' => $invoice->invoice_id,
        ]);

        $unassignedTask = $this->seedModel('Task', [
            'invoice_id' => null,
        ]);

        /* Act */
        $assignedIsAssigned   = $this->model->isAssignedToInvoice($assignedTask->task_id);
        $unassignedIsAssigned = $this->model->isAssignedToInvoice($unassignedTask->task_id);

        /* Assert */
        $this->assertTrue($assignedIsAssigned, 'Assigned task should return true');
        $this->assertFalse($unassignedIsAssigned, 'Unassigned task should return false');
    }

    #[Test]
    public function it_returns_true_for_nonexistent_task(): void
    {
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $canDelete  = $this->model->canDelete($nonexistentId);
        $isAssigned = $this->model->isAssignedToInvoice($nonexistentId);

        /* Assert */
        $this->assertTrue($canDelete, 'Non-existent task should return true for canDelete');
        $this->assertFalse($isAssigned, 'Non-existent task should return false for isAssigned');
    }

    #[Test]
    public function it_prevents_deletion_regardless_of_task_status(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        // Create tasks with different statuses but all assigned to invoice
        $statuses = [1, 2, 3, 4]; // Not Started, In Progress, Complete, On Hold

        foreach ($statuses as $status) {
            $task = $this->seedModel('Task', [
                'task_status' => $status,
                'invoice_id'  => $invoice->invoice_id,
            ]);

            /* Act */
            $canDelete = $this->model->canDelete($task->task_id);

            /* Assert */
            $this->assertFalse(
                $canDelete,
                "Task with status {$status} assigned to invoice should not be deletable"
            );
        }
    }

    #[Test]
    public function it_allows_deletion_of_completed_task_without_invoice(): void
    {
        /* Arrange */
        $task = $this->seedModel('Task', [
            'task_status' => 3, // Complete
            'invoice_id'  => null, // Not assigned
        ]);

        /* Act */
        $canDelete = $this->model->canDelete($task->task_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Completed task without invoice should be deletable');
    }

    #[Test]
    public function it_prevents_deletion_of_all_tasks_assigned_to_same_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');

        // Create multiple tasks assigned to same invoice
        $tasks = $this->seedModelMany('Task', 3, [
            'invoice_id' => $invoice->invoice_id,
        ]);

        /* Act & Assert */
        foreach ($tasks as $task) {
            $canDelete = $this->model->canDelete($task->task_id);
            $this->assertFalse(
                $canDelete,
                'Each task assigned to invoice should not be deletable'
            );
        }
    }


}
