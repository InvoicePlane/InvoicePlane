<?php

namespace Tests\Feature\Invoices;

use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

/**
 * ProjectService Unit Tests.
 *
 * Test suite for ProjectService business logic methods.
 */
#[CoversClass(ProjectService::class)]

class ProjectServiceTest extends TestCase
{
    use InteractsWithDatabase;

    private ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectService();
    }

    /**
     * Test that service returns correct model class.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        /* Arrange & Act */
        $reflection = new ReflectionClass($this->service);
        $method     = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);
        $modelClass = $method->invoke($this->service);

        /* Assert */
        $this->assertEquals(Project::class, $modelClass);
    }

    /**
     * Test that create method creates a new project.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_project(): void
    {
        /* Arrange */
        $client = $this->seedModel('Client');
        $data   = [
            'client_id'    => $client->client_id,
            'project_name' => 'Test Project',
        ];

        /* Act */
        $project = $this->service->create($data);

        /* Assert */
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Test Project', $project->project_name);
        $this->assertEquals($client->client_id, $project->client_id);
        $this->assertDatabaseHas('ip_projects', [
            'project_name' => 'Test Project',
        ]);
    }

    /**
     * Test that update method updates existing project.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_project(): void
    {
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id'    => $client->client_id,
            'project_name' => 'Old Name',
        ]);

        $updateData = [
            'project_name' => 'Updated Name',
        ];

        /* Act */
        $result = $this->service->update($project->project_id, $updateData);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseHas('ip_projects', [
            'project_id'   => $project->project_id,
            'project_name' => 'Updated Name',
        ]);
    }

    /**
     * Test that find method returns project.
     */
    #[Test]
    public function it_finds_project_by_id(): void
    {
        /* Arrange */
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $found = $this->service->find($project->project_id);

        /* Assert */
        $this->assertInstanceOf(Project::class, $found);
        $this->assertEquals($project->project_id, $found->project_id);
    }

    /**
     * Test that findOrFail throws exception for non-existent project.
     */
    #[Test]
    public function it_throws_exception_when_project_not_found(): void
    {
        /* Arrange */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /* Act */
        $this->service->findOrFail(999999);
    }

    /**
     * Test that delete method deletes project.
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
        $result = $this->service->delete($project->project_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_projects', [
            'project_id' => $project->project_id,
        ]);
    }
}
