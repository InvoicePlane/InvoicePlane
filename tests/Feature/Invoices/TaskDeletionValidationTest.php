<?php

namespace Tests\Feature\Invoices;

use Tests\Concerns\InteractsWithDatabase;

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

class TaskDeletionValidationTest extends AbstractServiceTestCase
{
    use InteractsWithDatabase;

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
        $task = $this->seedModel('Task', [
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
        $invoice = $this->seedModel('Invoice');

        $task = $this->seedModel('Task', [
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
        $invoice = $this->seedModel('Invoice');

        $assignedTask = $this->seedModel('Task', [
            'invoice_id' => $invoice->invoice_id,
        ]);

        $unassignedTask = $this->seedModel('Task', [
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
        $invoice = $this->seedModel('Invoice');

        // Create tasks with different statuses but all assigned to invoice
        $statuses = [1, 2, 3, 4]; // Not Started, In Progress, Complete, On Hold

        foreach ($statuses as $status) {
            $task = $this->seedModel('Task', [
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
        $task = $this->seedModel('Task', [
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
        $invoice = $this->seedModel('Invoice');

        // Create multiple tasks assigned to same invoice
        $tasks = $this->seedModelMany('Task', 3, [
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
        $invoice = $this->seedModel('Invoice');

        $task = $this->seedModel('Task', [
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
