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
        $response = $this->get(route('projects.create'));

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
        $response = $this->post(route('projects.store'), $projectData);

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
        $response = $this->post(route('projects.store'), $projectData);

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
        $response = $this->get(route('projects.edit', ['project' => $project->project_id]));

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
        $response = $this->put(route('projects.update', ['project' => $project->project_id]), $updateData);

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
        $response = $this->get(route('tasks.create'));

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
        $response = $this->post(route('tasks.store'), $taskData);

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
        $response = $this->post(route('tasks.store'), $taskData);

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
        $response = $this->get(route('tasks.edit', ['task' => $task->task_id]));

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
        $response = $this->put(route('tasks.update', ['task' => $task->task_id]), $updateData);

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
        $response = $this->post(route('tasks.store'), $taskData);

        /* Assert */
        $response->assertRedirect(route('tasks.index'));

        /* Verify task was created without project */
        $this->assertDatabaseHas('ip_tasks', [
            'task_name'  => 'Standalone Task',
            'project_id' => null,
        ]);
    }
}
