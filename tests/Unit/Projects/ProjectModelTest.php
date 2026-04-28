<?php

namespace Tests\Unit\Projects;

use Mdl_Projects;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * ProjectModel Unit Tests.
 *
 * Test suite for Mdl_Projects model methods.
 */
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

    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_project(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_project(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_finds_project_by_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_throws_exception_when_project_not_found(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_project(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
