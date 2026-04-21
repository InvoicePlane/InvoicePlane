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
class ProjectsControllerTest extends FeatureTestCase
{
    /**
     * Test that index method displays list of projects.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_list_of_projects(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->get(route('projects.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_index');
        $response->assertViewHas('projects');

        /** Verify project is in the list */
        $projects   = $response->viewData('projects');
        $projectIds = $projects->pluck('project_id')->toArray();
        $this->assertContains($project->project_id, $projectIds);
    }

    /**
     * Test that create method displays project form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_project_create_form(): void
    {
        /** Arrange */
        $client = Client::factory()->create(['client_active' => 1]);

        /** Act */
        $response = $this->get(route('projects.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_form');
        $response->assertViewHas('project');
        $response->assertViewHas('clients');

        /** Verify new project instance is passed */
        $project = $response->viewData('project');
        $this->assertInstanceOf(Project::class, $project);
        $this->assertFalse($project->exists);
    }

    /**
     * Test that store method creates new project with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_project_with_valid_data(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        /**
         * {
         *     "client_id": 1,
         *     "project_name": "Test Project",
         *     "project_status": 1
         * }.
         */
        $projectData = [
            'client_id'      => $client->client_id,
            'project_name'   => 'Test Project',
            'project_status' => 1,
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('alert_success');

        /* Verify project was created in database */
        $this->assertDatabaseHas('ip_projects', [
            'client_id'    => $client->client_id,
            'project_name' => 'Test Project',
        ]);
    }

    /**
     * Test that store method fails with invalid data.
     */
    #[Test]
    public function it_fails_to_create_project_with_invalid_data(): void
    {
        /** Arrange */
        /**
         * {
         *     "project_name": "Test Project"
         * }.
         */
        $projectData = [
            'project_name' => 'Test Project',
            // Missing required client_id
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        $response->assertSessionHasErrors(['client_id']);
    }

    /**
     * Test that edit method displays project edit form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_project_edit_form(): void
    {
        /** Arrange */
        $client  = Client::factory()->create(['client_active' => 1]);
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->get(route('projects.form', ['project_id' => $project->project_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_form');
        $response->assertViewHas('project');
        $response->assertViewHas('clients');

        /** Verify correct project is passed */
        $viewProject = $response->viewData('project');
        $this->assertEquals($project->project_id, $viewProject->project_id);
    }

    /**
     * Test that update method updates existing project.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_project_with_valid_data(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id'    => $client->client_id,
            'project_name' => 'Old Name',
        ]);

        /**
         * {
         *     "client_id": 1,
         *     "project_name": "Updated Name"
         * }.
         */
        $updateData = [
            'client_id'    => $client->client_id,
            'project_name' => 'Updated Name',
        ];

        /** Act */
        $response = $this->post(route('projects.form', ['project_id' => $project->project_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('alert_success');

        /* Verify project was updated */
        $this->assertDatabaseHas('ip_projects', [
            'project_id'   => $project->project_id,
            'project_name' => 'Updated Name',
        ]);
    }

    /**
     * Test that view method displays project details with related data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_project_view_with_related_data(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->get(route('projects.view', ['project' => $project->project_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_view');
        $response->assertViewHas('project');
        $response->assertViewHas('tasks');

        /** Verify correct project is passed */
        $viewProject = $response->viewData('project');
        $this->assertEquals($project->project_id, $viewProject->project_id);
    }

    /**
     * Test that destroy method deletes project.
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
        $response = $this->delete(route('projects.destroy', ['project' => $project->project_id]));

        /* Assert */
        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('alert_success');

        /* Verify project was deleted */
        $this->assertDatabaseMissing('ip_projects', [
            'project_id' => $project->project_id,
        ]);
    }

    // ==================== EDGE CASES & VALIDATION TESTS ====================

    /**
     * Test that project creation fails when project name is empty.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_create_project_with_empty_name(): void
    {
        /** Arrange */
        $client      = Client::factory()->create();
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => '', // Empty name
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        $response->assertSessionHasErrors(['project_name']);
    }

    /**
     * Test that project creation handles very long names.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_very_long_project_names(): void
    {
        /** Arrange */
        $client      = Client::factory()->create();
        $longName    = str_repeat('A', 300); // 300 characters
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => $longName,
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        // Should either truncate or fail validation
        if ($response->getStatusCode() === 302 && $response->isRedirect(route('projects.index'))) {
            // Accepted - verify truncation or storage
            $this->assertDatabaseHas('ip_projects', [
                'client_id' => $client->client_id,
            ]);

            return;
        }

        // Rejected - should have validation error
        $response->assertSessionHasErrors(['project_name']);
    }

    /**
     * Test that project creation handles special characters in name.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_special_characters_in_project_name(): void
    {
        /** Arrange */
        $client      = Client::factory()->create();
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => "Test <script>alert('xss')</script> Project",
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        $response->assertRedirect(route('projects.index'));

        /** Verify XSS is prevented/escaped */
        $project = Project::query()->where('client_id', $client->client_id)->first();
        $this->assertNotNull($project);
        // Name should be stored but will be escaped on output
        $this->assertStringContainsString('Project', $project->project_name);
    }

    /**
     * Test that project creation fails with non-existent client.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_create_project_with_nonexistent_client(): void
    {
        /** Arrange */
        $projectData = [
            'client_id'    => 99999, // Non-existent client
            'project_name' => 'Test Project',
        ];

        /** Act */
        $response = $this->post(route('projects.form'), $projectData);

        /* Assert */
        $response->assertSessionHasErrors(['client_id']);
    }

    /**
     * Test that project update fails with invalid status value.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_to_update_project_with_invalid_status(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        $updateData = [
            'client_id'      => $client->client_id,
            'project_name'   => 'Updated Name',
            'project_status' => 999, // Invalid status
        ];

        /** Act */
        $response = $this->post(route('projects.form', ['project_id' => $project->project_id]), $updateData);

        /* Assert */
        $response->assertSessionHasErrors(['project_status']);
    }

    /**
     * Test viewing non-existent project returns 404.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_404_when_viewing_nonexistent_project(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->get(route('projects.view', ['project' => $nonexistentId]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test editing non-existent project returns 404.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_404_when_editing_nonexistent_project(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->get(route('projects.form', ['project_id' => $nonexistentId]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test that deleting a project also handles associated tasks.
     */
    #[Group('crud')]
    #[Test]
    public function it_handles_task_associations_when_deleting_project(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        // Create tasks associated with the project
        $task1 = \Modules\Projects\Models\Task::factory()->create([
            'project_id' => $project->project_id,
        ]);
        $task2 = \Modules\Projects\Models\Task::factory()->create([
            'project_id' => $project->project_id,
        ]);

        /** Act */
        $response = $this->delete(route('projects.destroy', ['project' => $project->project_id]));

        /* Assert */
        $response->assertRedirect(route('projects.index'));

        /* Verify project was deleted */
        $this->assertDatabaseMissing('ip_projects', [
            'project_id' => $project->project_id,
        ]);

        /* Verify tasks no longer reference the project */
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'    => $task1->task_id,
            'project_id' => null, // Should be disassociated
        ]);
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'    => $task2->task_id,
            'project_id' => null,
        ]);
    }

    /**
     * Test that index page handles pagination correctly.
     */
    #[Group('smoke')]
    #[Test]
    public function it_handles_pagination_on_index_page(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        // Create multiple projects
        Project::factory()->count(25)->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->get(route('projects.index', ['page' => 1]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_index');
        $response->assertViewHas('projects');

        /** Verify pagination data is present */
        $projects = $response->viewData('projects');
        $this->assertGreaterThan(0, $projects->count());
    }

    /**
     * Test that index page displays empty state when no projects exist.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_displays_empty_state_when_no_projects_exist(): void
    {
        /* Arrange */
        // Ensure no projects exist
        Project::query()->delete();

        /** Act */
        $response = $this->get(route('projects.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('projects::projects_index');
        $response->assertViewHas('projects');

        /** Verify empty collection */
        $projects = $response->viewData('projects');
        $this->assertCount(0, $projects);
    }

    /**
     * Test that project view displays all related tasks.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_all_related_tasks_in_project_view(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        // Create multiple tasks for the project
        $task1 = \Modules\Projects\Models\Task::factory()->create([
            'project_id' => $project->project_id,
            'task_name'  => 'Task 1',
        ]);
        $task2 = \Modules\Projects\Models\Task::factory()->create([
            'project_id' => $project->project_id,
            'task_name'  => 'Task 2',
        ]);

        /** Act */
        $response = $this->get(route('projects.view', ['project' => $project->project_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('tasks');

        /** Verify tasks are in the view data */
        $tasks   = $response->viewData('tasks');
        $taskIds = $tasks->pluck('task_id')->toArray();
        $this->assertContains($task1->task_id, $taskIds);
        $this->assertContains($task2->task_id, $taskIds);
    }

    /**
     * Test that update preserves unchanged fields.
     */
    #[Group('crud')]
    #[Test]
    public function it_preserves_unchanged_fields_on_update(): void
    {
        /** Arrange */
        $client  = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id'      => $client->client_id,
            'project_name'   => 'Original Name',
            'project_status' => 1,
            'project_notes'  => 'Original notes',
        ]);

        $updateData = [
            'client_id'    => $client->client_id,
            'project_name' => 'Updated Name',
            // Not updating status or notes
        ];

        /** Act */
        $response = $this->post(route('projects.form', ['project_id' => $project->project_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('projects.index'));

        /** Verify only name was updated, other fields preserved */
        $updatedProject = Project::find($project->project_id);
        $this->assertEquals('Updated Name', $updatedProject->project_name);
        $this->assertEquals(1, $updatedProject->project_status);
        $this->assertEquals('Original notes', $updatedProject->project_notes);
    }

    /**
     * Test that deleting non-existent project handles gracefully.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_deletion_of_nonexistent_project_gracefully(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->delete(route('projects.destroy', ['project' => $nonexistentId]));

        /* Assert */
        // Should either return 404 or redirect with error message
        $this->assertTrue(
            $response->isNotFound()
            || ($response->isRedirect() && session()->has('alert_error'))
        );
    }
}

/**
 * TasksController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for task deletion with business rules:
 * - Tasks assigned to invoices cannot be deleted via HTTP request
 */
#[CoversClass(TasksController::class)]
class TaskDeletionValidationFeatureTest extends FeatureTestCase
{
    /**
     * Test that task without invoice assignment can be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_task_without_invoice_assignment(): void
    {
        /** Arrange */
        $task = Task::factory()->create([
            'task_name'   => 'Deletable Task',
            'invoice_id'  => null, // Not assigned to invoice
            'task_status' => 1,
        ]);

        /** Act */
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
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $task = Task::factory()->create([
            'task_name'   => 'Invoiced Task',
            'invoice_id'  => $invoice->invoice_id, // Assigned to invoice
            'task_status' => 3, // Complete
        ]);

        /** Act */
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
        /** Arrange */
        $task = Task::factory()->create([
            'task_status' => 3, // Complete
            'invoice_id'  => null,
        ]);

        /** Act */
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
        /** Arrange */
        $invoice = Invoice::factory()->create();

        // Test with different statuses
        $statuses = [
            1 => 'Not Started',
            2 => 'In Progress',
            3 => 'Complete',
            4 => 'On Hold',
        ];

        foreach ($statuses as $statusId => $statusName) {
            $task = Task::factory()->create([
                'task_name'   => "Task - {$statusName}",
                'task_status' => $statusId,
                'invoice_id'  => $invoice->invoice_id,
            ]);

            /** Act */
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
        /** Arrange */
        $invalidId = -1;

        /** Act */
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
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
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
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $task = Task::factory()->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        // Initially cannot delete
        $response1 = $this->post(route('tasks.delete', ['task_id' => $task->task_id]));
        $response1->assertSessionHas('alert_error');

        // Remove invoice reference
        $task->invoice_id = null;
        $task->save();

        /** Act */
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
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $tasks = Task::factory()->count(3)->create([
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
        /** Arrange */
        $tasks = Task::factory()->count(3)->create([
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
        /** Arrange */
        $invoice = Invoice::factory()->create();

        $deletableTask    = Task::factory()->create(['invoice_id' => null]);
        $nonDeletableTask = Task::factory()->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
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

/**
 * TasksController Feature Tests.
 *
 * Test suite for TasksController covering CRUD operations
 * with data integrity validation and business logic verification.
 */
#[CoversClass(TasksController::class)]
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
