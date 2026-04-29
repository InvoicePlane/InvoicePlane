<?php

namespace Tests\Unit\Projects;

use Mdl_Projects;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Projects::class)]
class ProjectModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('projects/mdl_projects');
        $this->model = $this->CI->mdl_projects;
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_join_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_join'));
    }

    #[Test]
    public function it_has_get_latest_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_latest'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_project_and_retrieves_it(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $name       = 'ProjectCreate_' . uniqid();
        $project_id = $this->seedModel('Project', ['client_id' => $client_id, 'project_name' => $name])->project_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_projects', ['project_id' => $project_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['project_name']);
        $this->assertEquals($client_id, (int) $row['client_id']);

        /* Cleanup */
        $this->databaseDelete('ip_projects', ['project_id' => $project_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_project(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $project_id = $this->seedModel('Project', ['client_id' => $client_id])->project_id;

        /* Act */
        $this->databaseDelete('ip_projects', ['project_id' => $project_id]);

        /* Assert */
        $this->assertNull($this->databaseFetchOne('ip_projects', ['project_id' => $project_id]));

        /* Cleanup */
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
