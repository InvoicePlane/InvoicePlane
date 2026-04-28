<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * TaskModel Unit Tests.
 *
 * Test suite for Mdl_Tasks model methods.
 */
#[CoversClass(Mdl_Tasks::class)]
class TaskModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('tasks/mdl_tasks');
        $this->model = $this->CI->mdl_tasks;
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_correct_model_class(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_task(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_task_without_project(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_task(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_finds_task_by_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_throws_exception_when_task_not_found(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_task(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_gets_all_tasks_with_relations_paginated(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_orders_tasks_by_name(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
