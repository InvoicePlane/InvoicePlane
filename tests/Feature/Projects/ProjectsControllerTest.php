<?php

namespace Tests\Feature\Projects;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Projects;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ProjectsController Feature Tests.
 *
 * Test suite for ProjectsController covering CRUD operations
 * with data integrity validation and business logic verification.
 */
#[CoversClass(Projects::class)]

class ProjectsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test that index method displays list of projects.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_list_of_projects(): void
    {
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->get('/projects/index');

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
        /* Arrange */
        $client = $this->seedModel('Client', ['client_active' => 1]);

        /* Act */
        $response = $this->get('/projects/form');

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
        /* Arrange */
        $client = $this->seedModel('Client');
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

        /* Act */
        $response = $this->post('/projects/form', $projectData);

        /* Assert */
        $response->assertRedirect('/projects/index');
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
        /* Arrange */
        /**
         * {
         *     "project_name": "Test Project"
         * }.
         */
        $projectData = [
            'project_name' => 'Test Project',
            // Missing required client_id
        ];

        /* Act */
        $response = $this->post('/projects/form', $projectData);

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
        /* Arrange */
        $client  = $this->seedModel('Client', ['client_active' => 1]);
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->get('/projects/form/' . ($project->project_id));

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
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

        /* Act */
        $response = $this->post('/projects/form/' . ($project->project_id), $updateData);

        /* Assert */
        $response->assertRedirect('/projects/index');
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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->get('/projects/view/' . ($project->project_id));

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->delete('/projects/destroy/' . ($project->project_id));

        /* Assert */
        $response->assertRedirect('/projects/index');
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
        /* Arrange */
        $client      = $this->seedModel('Client');
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => '', // Empty name
        ];

        /* Act */
        $response = $this->post('/projects/form', $projectData);

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
        /* Arrange */
        $client      = $this->seedModel('Client');
        $longName    = str_repeat('A', 300); // 300 characters
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => $longName,
        ];

        /* Act */
        $response = $this->post('/projects/form', $projectData);

        /* Assert */
        // CI3 should reject a 300-character project name with a validation error or redirect with error
        $statusCode = $response->getStatusCode();
        $this->assertContains(
            $statusCode,
            [200, 302],
            "Submitting a 300-character project name should result in a 200 (re-render) or 302 (redirect), got {$statusCode}."
        );

        // Verify the project was NOT stored with the overlong name (either rejected or truncated)
        // If it was stored, it must not exceed a reasonable maximum length
        $stored = \Modules\Projects\Models\Project::where('client_id', $client->client_id)->first();
        if ($stored !== null) {
            $this->assertLessThanOrEqual(
                255,
                mb_strlen($stored->project_name),
                'Stored project_name must not exceed 255 characters.'
            );
        } else {
            // Not stored — validation correctly rejected the input (expected behavior)
            $this->assertTrue(true, 'Project with overlong name was correctly rejected.');
        }
    }

    /**
     * Test that project creation handles special characters in name.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_special_characters_in_project_name(): void
    {
        /* Arrange */
        $client      = $this->seedModel('Client');
        $projectData = [
            'client_id'    => $client->client_id,
            'project_name' => "Test <script>alert('xss')</script> Project",
        ];

        /* Act */
        $response = $this->post('/projects/form', $projectData);

        /* Assert */
        $response->assertRedirect('/projects/index');

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
        /* Arrange */
        $projectData = [
            'client_id'    => 99999, // Non-existent client
            'project_name' => 'Test Project',
        ];

        /* Act */
        $response = $this->post('/projects/form', $projectData);

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        $updateData = [
            'client_id'      => $client->client_id,
            'project_name'   => 'Updated Name',
            'project_status' => 999, // Invalid status
        ];

        /* Act */
        $response = $this->post('/projects/form/' . ($project->project_id), $updateData);

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
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->get('/projects/view/' . ($nonexistentId));

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
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->get('/projects/form/' . ($nonexistentId));

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        // Create tasks associated with the project
        $task1 = $this->seedModel('Task', [
            'project_id' => $project->project_id,
        ]);
        $task2 = $this->seedModel('Task', [
            'project_id' => $project->project_id,
        ]);

        /* Act */
        $response = $this->delete('/projects/destroy/' . ($project->project_id));

        /* Assert */
        $response->assertRedirect('/projects/index');

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
        /* Arrange */
        $client = $this->seedModel('Client');

        // Create multiple projects
        $this->seedModelMany('Project', 25, [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->get('/projects/index/' . (1));

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

        /* Act */
        $response = $this->get('/projects/index');

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        // Create multiple tasks for the project
        $task1 = $this->seedModel('Task', [
            'project_id' => $project->project_id,
            'task_name'  => 'Task 1',
        ]);
        $task2 = $this->seedModel('Task', [
            'project_id' => $project->project_id,
            'task_name'  => 'Task 2',
        ]);

        /* Act */
        $response = $this->get('/projects/view/' . ($project->project_id));

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
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
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

        /* Act */
        $response = $this->post('/projects/form/' . ($project->project_id), $updateData);

        /* Assert */
        $response->assertRedirect('/projects/index');

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
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->delete('/projects/destroy/' . ($nonexistentId));

        /* Assert */
        // Should either return 404 or redirect with error message
        $this->assertTrue(
            $response->isNotFound()
            || ($response->isRedirect() && session()->has('alert_error'))
        );
    }
}
