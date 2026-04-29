<?php

namespace Tests\Unit\Projects;

use Mdl_Tasks;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
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
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_tasks', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('task_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('task_name', $rules);
        $this->assertArrayHasKey('task_price', $rules);
        $this->assertArrayHasKey('task_finish_date', $rules);
    }

    #[Test]
    public function it_returns_status_array(): void
    {
        $statuses = $this->model->statuses();

        $this->assertIsArray($statuses);
        $this->assertCount(4, $statuses);
        $this->assertArrayHasKey('1', $statuses);
        $this->assertArrayHasKey('2', $statuses);
        $this->assertArrayHasKey('3', $statuses);
        $this->assertArrayHasKey('4', $statuses);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_getting_invoice_for_null_task_id(): void
    {
        $result = $this->model->get_invoice_for_task(null);

        $this->assertNull($result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_getting_tasks_to_invoice_with_null_id(): void
    {
        $result = $this->model->get_tasks_to_invoice(null);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Group('crud')]
    #[Test]
    public function it_filters_tasks_by_name(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $unique = uniqid();
        $this->CI->db->insert('ip_tasks', [
            'task_name'        => 'Design_' . $unique,
            'task_date_added'  => date('Y-m-d H:i:s'),
            'task_status'      => 1,
            'task_price'       => 0,
            'task_finish_date' => date('Y-m-d'),
        ]);
        $task_id = $this->CI->db->insert_id();

        /* Act */
        $this->model->by_task('Design_' . $unique);
        $results = $this->model->get(false)->result();

        /* Assert */
        $this->assertNotEmpty($results);
        $found = array_filter($results, fn ($r) => $r->task_id === $task_id);
        $this->assertNotEmpty($found);

        /* Cleanup */
        $this->CI->db->delete('ip_tasks', ['task_id' => $task_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_does_nothing_when_updating_on_invoice_delete_with_null_id(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_tasks', [
            'task_name'        => 'Keep Me ' . uniqid(),
            'task_date_added'  => date('Y-m-d H:i:s'),
            'task_status'      => 1,
            'task_price'       => 0,
            'task_finish_date' => date('Y-m-d'),
        ]);
        $task_id = $this->CI->db->insert_id();

        /* Act */
        $this->model->update_on_invoice_delete(null);

        /* Assert */
        $task = $this->CI->db->get_where('ip_tasks', ['task_id' => $task_id])->row();
        $this->assertEquals(1, (int) $task->task_status);

        /* Cleanup */
        $this->CI->db->delete('ip_tasks', ['task_id' => $task_id]);
    }
}
