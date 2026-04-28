<?php

namespace Tests\Unit\Projects;

use Mdl_Projects;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Projects::class)]
class ProjectsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('projects/mdl_projects');
        $this->model = $this->CI->mdl_projects;
    }

    #[Test]
    public function it_returns_tasks_for_a_project(): void
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
            'task_status'      => 2,
            'project_id'       => $project->project_id,
        ]);

        // Create a task for a different project
            'project_name' => 'Other Project',
        ]);
            'task_name'        => 'Other Task',
            'task_description' => 'Other Description',
            'task_status'      => 1,
            'project_id'       => $otherProject->project_id,
        ]);

        /* Act */
        $result = $this->model->getTasks($project->project_id);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Task 1', $result[0]->task_name);
        $this->assertEquals('Task 2', $result[1]->task_name);
    }

    #[Test]
    public function it_returns_empty_array_when_project_has_no_tasks(): void
    {
        /* Arrange */
            'project_name' => 'Empty Project',
        ]);

        /* Act */
        $result = $this->model->getTasks($project->project_id);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_null(): void
    {
        /* Act */
        $result = $this->model->getTasks(null);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_zero(): void
    {
        /* Act */
        $result = $this->model->getTasks(0);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_project_id_is_false(): void
    {
        /* Act */
        $result = $this->model->getTasks(false);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->model->validation_rules();

        /* Assert */
        $this->assertIsArray($rules);
    }
}
