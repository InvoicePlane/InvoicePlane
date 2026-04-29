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
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'ProjClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $name = 'ProjectCreate_' . uniqid();
        $this->CI->db->insert('ip_projects', [
            'client_id'            => $client_id,
            'project_name'         => $name,
            'project_date_created' => date('Y-m-d'),
        ]);
        $project_id = $this->CI->db->insert_id();

        /* Act */
        $project = $this->CI->db->get_where('ip_projects', ['project_id' => $project_id])->row();

        /* Assert */
        $this->assertNotNull($project);
        $this->assertEquals($name, $project->project_name);
        $this->assertEquals($client_id, (int) $project->client_id);

        /* Cleanup */
        $this->CI->db->delete('ip_projects', ['project_id' => $project_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_project(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'ProjDelClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_projects', [
            'client_id'            => $client_id,
            'project_name'         => 'Delete Me ' . uniqid(),
            'project_date_created' => date('Y-m-d'),
        ]);
        $project_id = $this->CI->db->insert_id();

        /* Act */
        $this->CI->db->delete('ip_projects', ['project_id' => $project_id]);

        /* Assert */
        $project = $this->CI->db->get_where('ip_projects', ['project_id' => $project_id])->row();
        $this->assertNull($project);

        /* Cleanup */
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
