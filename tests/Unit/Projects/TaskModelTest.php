<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

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

    #[Test]
    public function it_has_update_status_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'update_status'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_task_and_retrieves_it(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name    = 'TaskCreate_' . uniqid();
        $task_id = $this->seedModel('Task', ['task_name' => $name])->task_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_tasks', ['task_id' => $task_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['task_name']);
        $this->assertEquals(1, (int) $row['task_status']);

        /* Cleanup */
        $this->databaseDelete('ip_tasks', ['task_id' => $task_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_task_status(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $task_id = $this->seedModel('Task', ['task_status' => 1])->task_id;

        /* Act */
        $this->model->update_status(3, $task_id);

        /* Assert */
        $row = $this->databaseFetchOne('ip_tasks', ['task_id' => $task_id]);
        $this->assertEquals(3, (int) $row['task_status']);

        /* Cleanup */
        $this->databaseDelete('ip_tasks', ['task_id' => $task_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_does_not_update_status_for_invalid_status_code(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $task_id = $this->seedModel('Task', ['task_status' => 1])->task_id;

        /* Act: 99 is not a valid status */
        $this->model->update_status(99, $task_id);

        /* Assert: status remains unchanged */
        $row = $this->databaseFetchOne('ip_tasks', ['task_id' => $task_id]);
        $this->assertEquals(1, (int) $row['task_status']);

        /* Cleanup */
        $this->databaseDelete('ip_tasks', ['task_id' => $task_id]);
    }
}
