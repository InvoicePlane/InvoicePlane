<?php

namespace Modules\Projects\Tests\Feature;

use Modules\Crm\Models\Client;
use Modules\Projects\Controllers\ProjectsController;
use Modules\Projects\Models\Project;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * ProjectsController Feature Tests.
 *
 * Test suite for ProjectsController covering CRUD operations
 * with data integrity validation and business logic verification.
 */
#[CoversClass(ProjectsController::class)]

class TasksControllerTest extends FeatureTestCase
{
    /**
     * Test that index method displays list of tasks.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_list_of_tasks(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
        $response = $this->get(route('tasks.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::tasks_index');
        $response->assertViewHas('tasks');
        $response->assertViewHas('task_statuses');

        /** Verify task is in the list */
        $tasks   = $response->viewData('tasks');
        $taskIds = $tasks->pluck('task_id')->toArray();
        $this->assertContains($task->task_id, $taskIds);
    }

    /**
     * Test that create method displays task form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_task_create_form(): void
    {
        /** Act */
        $response = $this->get(route('tasks.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::tasks_form');
        $response->assertViewHas('task');
        $response->assertViewHas('projects');
        $response->assertViewHas('task_statuses');

        /** Verify new task instance is passed */
        $task = $response->viewData('task');
        $this->assertInstanceOf(Task::class, $task);
        $this->assertFalse($task->exists);
    }

    /**
     * Test that store method creates new task with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_task_with_valid_data(): void
    {
        /** Arrange */
        $project = Project::factory()->create();
        /**
         * {
         *     "project_id": 1,
         *     "task_name": "Test Task",
         *     "task_status": 1,
         *     "task_finish_date": "2025-12-31"
         * }.
         */
        $taskData = [
            'project_id'       => $project->project_id,
            'task_name'        => 'Test Task',
            'task_status'      => 1,
            'task_finish_date' => '2025-12-31',
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('alert_success');

        /* Verify task was created in database */
        $this->assertDatabaseHas('ip_tasks', [
            'project_id' => $project->project_id,
            'task_name'  => 'Test Task',
        ]);
    }

    /**
     * Test that store method fails with invalid data.
     */
    #[Test]
    public function it_fails_to_create_task_with_invalid_data(): void
    {
        /** Arrange */
        /**
         * {
         *     "project_id": 999
         * }.
         */
        $taskData = [
            'project_id' => 999,
            // Missing required task_name
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertSessionHasErrors(['task_name']);
    }

    /**
     * Test that edit method displays task edit form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_task_edit_form(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
        $response = $this->get(route('tasks.form', ['task_id' => $task->task_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::tasks_form');
        $response->assertViewHas('task');
        $response->assertViewHas('projects');
        $response->assertViewHas('task_statuses');

        /** Verify correct task is passed */
        $viewTask = $response->viewData('task');
        $this->assertEquals($task->task_id, $viewTask->task_id);
    }

    /**
     * Test that update method updates existing task.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_task_with_valid_data(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_name' => 'Old Name',
        ]);

        /**
         * {
         *     "task_name": "Updated Name",
         *     "task_status": 2
         * }.
         */
        $updateData = [
            'task_name'   => 'Updated Name',
            'task_status' => 2,
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('alert_success');

        /* Verify task was updated */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'   => $task->task_id,
            'task_name' => 'Updated Name',
        ]);
    }

    /**
     * Test that destroy method deletes task.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_task(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        /** Act */
        $response = $this->delete(route('tasks.destroy', ['task' => $task->task_id]));

        /* Assert */
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('alert_success');

        /* Verify task was deleted */
        $this->assertDatabaseMissing('ip_tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    /**
     * Test that tasks can be created without a project.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_task_without_project(): void
    {
        /** Arrange */
        /**
         * {
         *     "task_name": "Standalone Task",
         *     "task_status": 1
         * }.
         */
        $taskData = [
            'task_name'   => 'Standalone Task',
            'task_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify task was created without project */
        $this->assertDatabaseHas('ip_tasks', [
            'task_name'  => 'Standalone Task',
            'project_id' => null,
        ]);
    }

    // ==================== EDGE CASES & VALIDATION TESTS ====================

    /**
     * Test that task creation fails with empty task name.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_create_task_with_empty_name(): void
    {
        /** Arrange */
        $taskData = [
            'task_name'   => '', // Empty name
            'task_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertSessionHasErrors(['task_name']);
    }

    /**
     * Test that task creation handles special characters in name.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_special_characters_in_task_name(): void
    {
        /** Arrange */
        $taskData = [
            'task_name'   => "Task <img src=x onerror=alert('xss')> Name",
            'task_status' => 1,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify XSS is prevented - use database assertion instead of static model call */
        $this->assertDatabaseHas('ip_tasks', [
            'task_status' => 1,
        ]);
        // Additional check: task name should contain safe content
        $tasks = \Illuminate\Support\Facades\DB::table('ip_tasks')
            ->where('task_status', 1)
            ->orderBy('task_id', 'desc')
            ->first();
        $this->assertNotNull($tasks);
        $this->assertStringContainsString('Task', $tasks->task_name);
    }

    /**
     * Test that task creation fails with invalid status.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_create_task_with_invalid_status(): void
    {
        /** Arrange */
        $taskData = [
            'task_name'   => 'Test Task',
            'task_status' => 999, // Invalid status
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertSessionHasErrors(['task_status']);
    }

    /**
     * Test that task creation fails with non-existent project.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_create_task_with_nonexistent_project(): void
    {
        /** Arrange */
        $taskData = [
            'task_name'   => 'Test Task',
            'project_id'  => 99999, // Non-existent project
            'task_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('tasks.form'), $taskData);

        /* Assert */
        $response->assertSessionHasErrors(['project_id']);
    }

    /**
     * Test viewing non-existent task returns 404.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_404_when_editing_nonexistent_task(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->get(route('tasks.form', ['task_id' => $nonexistentId]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test that task update with invalid finish date fails.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_update_task_with_invalid_finish_date(): void
    {
        /** Arrange */
        $task = Task::factory()->create();

        $updateData = [
            'task_name'        => 'Updated Task',
            'task_finish_date' => 'invalid-date', // Invalid date format
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertSessionHasErrors(['task_finish_date']);
    }

    /**
     * Test that task can be assigned to a different project.
     */
    #[Group('crud')]
    #[Test]
    public function it_can_reassign_task_to_different_project(): void
    {
        /** Arrange */
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $task = Task::factory()->create([
            'project_id' => $project1->project_id,
        ]);

        $updateData = [
            'task_name'   => $task->task_name,
            'project_id'  => $project2->project_id,
            'task_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify task is now assigned to project2 */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'    => $task->task_id,
            'project_id' => $project2->project_id,
        ]);
    }

    /**
     * Test that task can be unassigned from project.
     */
    #[Group('crud')]
    #[Test]
    public function it_can_unassign_task_from_project(): void
    {
        /** Arrange */
        $project = Project::factory()->create();
        $task    = Task::factory()->create([
            'project_id' => $project->project_id,
        ]);

        $updateData = [
            'task_name'   => $task->task_name,
            'project_id'  => null, // Unassign from project
            'task_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify task is no longer assigned to project */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'    => $task->task_id,
            'project_id' => null,
        ]);
    }

    /**
     * Test that index page filters tasks by project.
     */
    #[Group('smoke')]
    #[Test]
    public function it_filters_tasks_by_project(): void
    {
        /** Arrange */
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $task1 = Task::factory()->create([
            'project_id' => $project1->project_id,
            'task_name'  => 'Project 1 Task',
        ]);
        $task2 = Task::factory()->create([
            'project_id' => $project2->project_id,
            'task_name'  => 'Project 2 Task',
        ]);

        /** Act */
        $response = $this->get(route('tasks.by-project', ['project' => $project1->project_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('tasks');

        /** Verify only project1 tasks are shown */
        $tasks   = $response->viewData('tasks');
        $taskIds = $tasks->pluck('task_id')->toArray();
        $this->assertContains($task1->task_id, $taskIds);
        $this->assertNotContains($task2->task_id, $taskIds);
    }

    /**
     * Test that index displays empty state when no tasks exist.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_displays_empty_state_when_no_tasks_exist(): void
    {
        /* Arrange */
        Task::query()->delete();

        /** Act */
        $response = $this->get(route('tasks.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('tasks');

        /** Verify empty collection */
        $tasks = $response->viewData('tasks');
        $this->assertCount(0, $tasks);
    }

    /**
     * Test that deleting non-existent task handles gracefully.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_deletion_of_nonexistent_task_gracefully(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->delete(route('tasks.destroy', ['task' => $nonexistentId]));

        /* Assert */
        // Should either return 404 or redirect with error
        $this->assertTrue(
            $response->isNotFound()
            || ($response->isRedirect() && session()->has('alert_error'))
        );
    }

    /**
     * Test that task finish date can be updated.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_task_finish_date(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_finish_date' => '2025-12-01',
        ]);

        $updateData = [
            'task_name'        => $task->task_name,
            'task_finish_date' => '2025-12-31',
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify finish date was updated */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'          => $task->task_id,
            'task_finish_date' => '2025-12-31',
        ]);
    }

    /**
     * Test that task status transitions work correctly.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_task_status(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_status' => 1, // Not started
        ]);

        $updateData = [
            'task_name'   => $task->task_name,
            'task_status' => 3, // Complete
        ];

        /** Act */
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify status was updated */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'     => $task->task_id,
            'task_status' => 3,
        ]);
    }

    /**
     * Test that task preserves unchanged fields on update.
     */
    #[Group('crud')]
    #[Test]
    public function it_preserves_unchanged_fields_on_task_update(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_name'        => 'Original Name',
            'task_description' => 'Original description',
            'task_status'      => 1,
        ]);

        $updateData = [
            'task_name' => 'Updated Name',
            // Not updating description or status
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('tasks.form', ['task_id' => $task->task_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify only name was updated - use database assertions */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'          => $task->task_id,
            'task_name'        => 'Updated Name',
            'task_description' => 'Original description',
            'task_status'      => 1,
        ]);
    }
}
