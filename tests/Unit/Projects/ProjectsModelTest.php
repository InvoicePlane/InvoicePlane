<?php

namespace Tests\Unit\Projects;

use Mdl_Projects;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
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
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_projects', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('project_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('project_name', $rules);
    }

    #[Test]
    public function it_has_get_tasks_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_tasks'));
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_empty_array_when_project_id_is_null(): void
    {
        $this->assertEmpty($this->model->get_tasks(null));
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_empty_array_when_project_id_is_zero(): void
    {
        $this->assertEmpty($this->model->get_tasks(0));
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_tasks_for_a_project(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedClient();
        $project_id = $this->seedProject($client_id);
        $this->seedTask(['project_id' => $project_id]);
        $this->seedTask(['project_id' => $project_id]);

        /* Act */
        $result = $this->model->get_tasks($project_id);

        /* Assert */
        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        /* Cleanup */
        $this->databaseDelete('ip_tasks', ['project_id' => $project_id]);
        $this->databaseDelete('ip_projects', ['project_id' => $project_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
