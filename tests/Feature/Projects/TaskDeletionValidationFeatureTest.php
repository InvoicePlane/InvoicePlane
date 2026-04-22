<?php

namespace Tests\Feature\Projects;

use Modules\Projects\Controllers\ProjectsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ProjectsController Feature Tests.
 *
 * Test suite for ProjectsController covering CRUD operations
 * with data integrity validation and business logic verification.
 */
#[CoversClass(\Tasks::class)]
class TaskDeletionValidationFeatureTest extends AbstractTestCase
{
    use InteractsWithDatabase;

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
        $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
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
        $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
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
        $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
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
            $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

            /* Assert */
            $response->assertRedirect(route('tasks.index'));
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
        $response = $this->post(route('tasks.delete', ['task_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
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
        $response = $this->post(route('tasks.delete', ['task_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
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
        $response1 = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));
        $response1->assertSessionHas('alert_error');

        // Remove invoice reference
        $task->invoice_id = null;
        $task->save();

        /* Act */
        $response2 = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

        /* Assert */
        $response2->assertRedirect(route('tasks.index'));
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
            $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

            $response->assertRedirect(route('tasks.index'));
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
            $response = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));

            $response->assertRedirect(route('tasks.index'));
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
        $response1 = $this->post(route('tasks.delete', ['task_id' => $deletableTask->task_id]));
        $response2 = $this->post(route('tasks.delete', ['task_id' => $nonDeletableTask->task_id]));

        /* Assert */
        // Deletable task deleted
        $response1->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $deletableTask->task_id]);

        // Non-deletable task preserved
        $response2->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tasks', ['task_id' => $nonDeletableTask->task_id]);
    }
}
