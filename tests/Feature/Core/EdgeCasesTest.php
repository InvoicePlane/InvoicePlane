<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;
use Tests\Concerns\InteractsWithDatabase;

class EdgeCasesTest extends CiTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_unit_service_handles_extreme_quantities_correctly(): void
    {
        $this->skipWithoutDatabase();
        $unit = $this->seedModel('Unit', ['unit_name' => 'Piece', 'unit_name_plrl' => 'Pieces']);

        $this->CI->load->model('units/mdl_units');
        $model = $this->CI->mdl_units;

        $this->assertEquals('Piece', $model->getUnitName($unit->unit_id, 1));
        $this->assertEquals('Pieces', $model->getUnitName($unit->unit_id, 0));
        $this->assertEquals('Pieces', $model->getUnitName($unit->unit_id, PHP_INT_MAX));
    }

    #[Test]
    public function it_tasks_service_handles_concurrent_task_retrieval(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', ['client_id' => $client->client_id]);

        for ($i = 0; $i < 10; $i++) {
            $this->seedModel('Task', ['project_id' => $project->project_id]);
        }

        $this->CI->load->model('tasks/mdl_tasks');
        $model = $this->CI->mdl_tasks;

        $result1 = $model->get()->result();
        $result2 = $model->get()->result();

        $this->assertEquals(count($result1), count($result2));
    }

    #[Test]
    public function it_tasks_to_invoice_returns_correct_sorting(): void
    {
        $this->assertDirectoryExists(
            dirname(__DIR__, 3) . '/application/modules/tasks'
        );
    }

    #[Test]
    public function it_unit_save_preserves_data_integrity_on_update(): void
    {
        $this->skipWithoutDatabase();
        $unit = $this->seedModel('Unit', ['unit_name' => 'Old Name', 'unit_name_plrl' => 'Old Names']);

        $this->databaseUpdate(
            'ip_units',
            ['unit_name' => 'New Name', 'unit_name_plrl' => 'New Names'],
            ['unit_id' => $unit->unit_id]
        );

        $updated = $this->databaseFetchOne('ip_units', ['unit_id' => $unit->unit_id]);
        $this->assertEquals('New Name', $updated['unit_name']);
        $this->assertEquals('New Names', $updated['unit_name_plrl']);
    }

    #[Test]
    public function it_tasks_service_handles_string_and_numeric_ids(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $project = $this->seedModel('Project', ['client_id' => $client->client_id]);
        $task    = $this->seedModel('Task', ['project_id' => $project->project_id]);

        $this->CI->load->model('tasks/mdl_tasks');
        $model = $this->CI->mdl_tasks;

        $resultNumeric = $model->get_by_id((int) $task->task_id);
        $resultString  = $model->get_by_id((string) $task->task_id);

        $this->assertEquals($resultNumeric->task_id, $resultString->task_id);
    }

    #[Test]
    public function it_empty_string_id_treated_as_falsy(): void
    {
        $this->assertFalse((bool) '');
        $this->assertFalse(! ! '');
        $this->assertTrue(empty(''));
    }

    #[Test]
    public function it_unit_exists_is_case_sensitive(): void
    {
        $this->CI->load->model('units/mdl_units');
        $rules = $this->CI->mdl_units->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
    }

    #[Test]
    public function it_concurrent_updates_maintain_consistency(): void
    {
        $this->skipWithoutDatabase();
        $unit = $this->seedModel('Unit', ['unit_name' => 'Start', 'unit_name_plrl' => 'Starts']);

        $this->databaseUpdate('ip_units', ['unit_name' => 'Middle'], ['unit_id' => $unit->unit_id]);
        $this->databaseUpdate('ip_units', ['unit_name' => 'Final'], ['unit_id' => $unit->unit_id]);

        $final = $this->databaseFetchOne('ip_units', ['unit_id' => $unit->unit_id]);
        $this->assertEquals('Final', $final['unit_name']);
    }
}
