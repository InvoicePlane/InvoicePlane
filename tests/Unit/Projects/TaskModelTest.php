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
        $name = 'TaskCreate_' . uniqid();
        $this->CI->db->insert('ip_tasks', [
            'task_name'        => $name,
            'task_date_added'  => date('Y-m-d H:i:s'),
            'task_status'      => 1,
            'task_price'       => 0,
            'task_finish_date' => date('Y-m-d'),
        ]);
        $task_id = $this->CI->db->insert_id();

        /* Act */
        $task = $this->CI->db->get_where('ip_tasks', ['task_id' => $task_id])->row();

        /* Assert */
        $this->assertNotNull($task);
        $this->assertEquals($name, $task->task_name);
        $this->assertEquals(1, (int) $task->task_status);

        /* Cleanup */
        $this->CI->db->delete('ip_tasks', ['task_id' => $task_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_task_status(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_tasks', [
            'task_name'        => 'Status Task ' . uniqid(),
            'task_date_added'  => date('Y-m-d H:i:s'),
            'task_status'      => 1,
            'task_price'       => 0,
            'task_finish_date' => date('Y-m-d'),
        ]);
        $task_id = $this->CI->db->insert_id();

        /* Act */
        $this->model->update_status(3, $task_id);

        /* Assert */
        $task = $this->CI->db->get_where('ip_tasks', ['task_id' => $task_id])->row();
        $this->assertEquals(3, (int) $task->task_status);

        /* Cleanup */
        $this->CI->db->delete('ip_tasks', ['task_id' => $task_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_does_not_update_status_for_invalid_status_code(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_tasks', [
            'task_name'        => 'Invalid Status Task ' . uniqid(),
            'task_date_added'  => date('Y-m-d H:i:s'),
            'task_status'      => 1,
            'task_price'       => 0,
            'task_finish_date' => date('Y-m-d'),
        ]);
        $task_id = $this->CI->db->insert_id();

        /* Act: 99 is not a valid status */
        $this->model->update_status(99, $task_id);

        /* Assert: status remains unchanged */
        $task = $this->CI->db->get_where('ip_tasks', ['task_id' => $task_id])->row();
        $this->assertEquals(1, (int) $task->task_status);

        /* Cleanup */
        $this->CI->db->delete('ip_tasks', ['task_id' => $task_id]);
    }
}
