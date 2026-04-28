<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Core\EdgeCases::class)]
class EdgeCasesTest extends AbstractTestCase
{
    #[Test]
    public function it_unit_service_handles_extreme_quantities_correctly(): void
    {
        /* Arrange */
        $model = new UnitsService();
        $unit    = Unit::create(['unit_name' => 'Item', 'unit_name_plrl' => 'Items']);

        /* Act & Assert */
        $this->assertEquals('Items', $service->getName($unit->unit_id, PHP_INT_MAX));

        /* Act & Assert */
        $this->assertEquals('Items', $service->getName($unit->unit_id, PHP_INT_MIN));

        /* Act & Assert */
        $this->assertEquals('Item', $service->getName($unit->unit_id, 1));
        $this->assertEquals('Items', $service->getName($unit->unit_id, 2));
        $this->assertEquals('Item', $service->getName($unit->unit_id, -1));
        $this->assertEquals('Items', $service->getName($unit->unit_id, -2));
    }

    #[Test]
    public function it_tasks_service_handles_concurrent_task_retrieval(): void
    {
        /* Arrange */
        $model = new TasksService();
        $project = Project::create(['project_name' => 'Test Project']);

        // Create multiple tasks
        for ($i = 1; $i <= 10; $i++) {
            Task::create([
                'task_name'        => "Task {$i}",
                'task_description' => "Description {$i}",
                'task_status'      => 1,
                'project_id'       => $project->project_id,
            ]);
        }

        /* Act */
        $result1 = $service->getTasks($project->project_id);
        $result2 = $service->getTasks($project->project_id);

        /* Assert */
        $this->assertCount(10, $result1);
        $this->assertCount(10, $result2);
        $this->assertEquals(count($result1), count($result2));
    }

    #[Test]
    public function it_tasks_to_invoice_returns_correct_sorting(): void
    {
        /* Arrange */
        $model = new TasksService();
        $client  = tmpClient::create(['client_name' => 'Test Client', 'client_active' => 1]);
        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        // Create tasks with different finish dates
        Task::create([
            'task_name'        => 'Latest Task',
            'task_description' => 'Description',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now(),
        ]);
        Task::create([
            'task_name'        => 'Earliest Task',
            'task_description' => 'Description',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDays(5),
        ]);
        Task::create([
            'task_name'        => 'Middle Task',
            'task_description' => 'Description',
            'task_status'      => 3,
            'project_id'       => 0,
            'task_finish_date' => now()->subDays(2),
        ]);

        /* Act */
        $result = $service->getTasksToInvoice($invoice->invoice_id);

        /* Assert */
        $this->assertCount(3, $result);
        $this->assertEquals('Earliest Task', $result[0]->task_name);
        $this->assertEquals('Middle Task', $result[1]->task_name);
        $this->assertEquals('Latest Task', $result[2]->task_name);
    }

    #[Test]
    public function it_unit_save_preserves_data_integrity_on_update(): void
    {
        /* Arrange */
        $model = new UnitsService();
        $unit    = Unit::create([
            'unit_name'      => 'Original Name',
            'unit_name_plrl' => 'Original Plural',
        ]);
        $originalId = $unit->unit_id;

        /* Act */
        $updated = $service->save(['unit_name' => 'Updated Name'], $originalId);

        /* Assert */
        $this->assertEquals($originalId, $updated->unit_id);
        $this->assertEquals('Updated Name', $updated->unit_name);

        // Refresh and verify persistence
        $updated->refresh();
        $this->assertEquals('Updated Name', $updated->unit_name);
    }

    #[Test]
    public function it_tasks_service_handles_string_and_numeric_ids(): void
    {
        /* Arrange */
        $model = new TasksService();
        $project = Project::create(['project_name' => 'Test Project']);
        Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        /* Act */
        $result1 = $service->getTasks($project->project_id);

        /* Act */
        $result2 = $service->getTasks((string) $project->project_id);

        /* Assert */
        $this->assertCount(1, $result1);
        $this->assertCount(1, $result2);
    }

    #[Test]
    public function it_empty_string_id_treated_as_falsy(): void
    {
        /* Arrange */
        $model = new TasksService();

        /* Act */
        $result = $service->getTasks('');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_unit_exists_is_case_sensitive(): void
    {
        /* Arrange */
        $model = new UnitsService();
        Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act & Assert */
        $this->assertTrue($service->exists('Hour'));
        $this->assertFalse($service->exists('hour')); // Different case
        $this->assertFalse($service->exists('HOUR')); // Different case
    }

    #[Test]
    public function it_concurrent_updates_maintain_consistency(): void
    {
        /* Arrange */
        $model = new UnitsService();
        $unit    = Unit::create(['unit_name' => 'Item', 'unit_name_plrl' => 'Items']);

        /* Act */
        $service->save(['unit_name' => 'Updated Item 1'], $unit->unit_id);
        $service->save(['unit_name' => 'Updated Item 2'], $unit->unit_id);

        /* Assert */
        $unit->refresh();
        $this->assertEquals('Updated Item 2', $unit->unit_name);
    }
}
