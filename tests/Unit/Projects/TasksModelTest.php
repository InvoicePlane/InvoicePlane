<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Tasks::class)]
class TasksModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('tasks/mdl_tasks');
        $this->model = $this->CI->mdl_tasks;
    }

    #[Test]
    public function it_filters_tasks_by_name(): void
    {
        /* Arrange */
            'task_name'        => 'Design Homepage',
            'task_description' => 'Create wireframes',
            'task_status'      => 1,
        ]);
            'task_name'        => 'Build API',
            'task_description' => 'Design endpoints',
            'task_status'      => 1,
        ]);
            'task_name'        => 'Testing',
            'task_description' => 'Write tests',
            'task_status'      => 1,
        ]);

        /* Act */
        $result = $this->model->byTask('Design');

        /* Assert */
        $this->assertInstanceOf(TasksService::class, $result);
    }

    #[Test]
    public function it_returns_null_when_getting_invoice_for_null_task_id(): void
    {
        /* Act */
        $result = $this->model->getInvoiceForTask(null);

        /* Assert */
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_task_has_no_associated_invoice(): void
    {
        /* Arrange */
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
        ]);

        /* Act */
        $result = $this->model->getInvoiceForTask($task->task_id);

        /* Assert */
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_empty_array_when_getting_tasks_to_invoice_with_null_id(): void
    {
        /* Act */
        $result = $this->model->getTasksToInvoice(null);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_tasks_to_invoice_for_unassigned_projects(): void
    {
        /* Arrange */
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDays(2),
        ]);
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDay(),
        ]);
        // Task with different status should not be included
            'task_name'        => 'Task 3',
            'task_description' => 'Description 3',
            'task_status'      => 1,
            'project_id'       => 0,
        ]);

        // Create invoice
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        /* Act */
        $result = $this->model->getTasksToInvoice($invoice->invoice_id);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_does_nothing_when_updating_on_invoice_delete_with_null_id(): void
    {
        /* Arrange */
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
        ]);

        /* Act */
        $this->model->updateOnInvoiceDelete(null);

        /* Assert */
        $task->refresh();
        $this->assertEquals(1, $task->task_status);
    }

    #[Test]
    public function it_does_nothing_when_updating_on_project_delete_with_null_id(): void
    {
        /* Arrange */
            'project_name' => 'Test Project',
        ]);
            'task_name'        => 'Test Task',
            'task_description' => 'Description',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        /* Act */
        $this->model->updateOnProjectDelete(null);

        /* Assert */
        $task->refresh();
        $this->assertEquals($project->project_id, $task->project_id);
    }

    #[Test]
    public function it_clears_project_association_when_project_is_deleted(): void
    {
        /* Arrange */
            'project_name' => 'Test Project',
        ]);
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        /* Act */
        $this->model->updateOnProjectDelete($project->project_id);

        /* Assert */
        $task1->refresh();
        $task2->refresh();
        $this->assertNull($task1->project_id);
        $this->assertNull($task2->project_id);
    }

    #[Test]
    public function it_returns_status_array(): void
    {
        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertIsArray($statuses);
        $this->assertNotEmpty($statuses);
    }
}
