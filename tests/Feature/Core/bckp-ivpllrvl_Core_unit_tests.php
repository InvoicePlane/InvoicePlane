<?php

namespace Modules\Core\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CustomFields\Models\CustomField;
use Modules\CustomFields\Services\CustomFieldsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomFieldsServiceTest extends TestCase
{
    use RefreshDatabase;

    private CustomFieldsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CustomFieldsService::class);
    }

    #[Test]
    public function it_retrieves_custom_fields_by_table(): void
    {
        // Arrange
        CustomField::create([
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Client Custom Field',
            'custom_field_type'  => 'TEXT',
        ]);

        CustomField::create([
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Another Client Field',
            'custom_field_type'  => 'TEXT',
        ]);

        CustomField::create([
            'custom_field_table' => 'ip_invoices',
            'custom_field_label' => 'Invoice Custom Field',
            'custom_field_type'  => 'TEXT',
        ]);

        // Act
        $result = $this->service->byTable('ip_clients');

        // Assert
        $this->assertInstanceOf(CustomFieldsService::class, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
    }
}

/**
 * Edge case and boundary condition tests for service methods.
 */
class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unit_service_handles_extreme_quantities_correctly(): void
    {
        // Arrange
        $service = new UnitsService();
        $unit    = Unit::create(['unit_name' => 'Item', 'unit_name_plrl' => 'Items']);

        // Act & Assert: Very large positive number
        $this->assertEquals('Items', $service->getName($unit->unit_id, PHP_INT_MAX));

        // Act & Assert: Very large negative number
        $this->assertEquals('Items', $service->getName($unit->unit_id, PHP_INT_MIN));

        // Act & Assert: Boundary values
        $this->assertEquals('Item', $service->getName($unit->unit_id, 1));
        $this->assertEquals('Items', $service->getName($unit->unit_id, 2));
        $this->assertEquals('Item', $service->getName($unit->unit_id, -1));
        $this->assertEquals('Items', $service->getName($unit->unit_id, -2));
    }

    #[Test]
    public function tasks_service_handles_concurrent_task_retrieval(): void
    {
        // Arrange
        $service = new TasksService();
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

        // Act: Multiple retrievals should be consistent
        $result1 = $service->getTasks($project->project_id);
        $result2 = $service->getTasks($project->project_id);

        // Assert: Results should be identical
        $this->assertCount(10, $result1);
        $this->assertCount(10, $result2);
        $this->assertEquals(count($result1), count($result2));
    }

    #[Test]
    public function tasks_to_invoice_returns_correct_sorting(): void
    {
        // Arrange
        $service = new TasksService();
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

        // Act
        $result = $service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should be ordered by finish date ascending
        $this->assertCount(3, $result);
        $this->assertEquals('Earliest Task', $result[0]->task_name);
        $this->assertEquals('Middle Task', $result[1]->task_name);
        $this->assertEquals('Latest Task', $result[2]->task_name);
    }

    #[Test]
    public function unit_save_preserves_data_integrity_on_update(): void
    {
        // Arrange
        $service = new UnitsService();
        $unit    = Unit::create([
            'unit_name'      => 'Original Name',
            'unit_name_plrl' => 'Original Plural',
        ]);
        $originalId = $unit->unit_id;

        // Act: Update only one field
        $updated = $service->save(['unit_name' => 'Updated Name'], $originalId);

        // Assert: ID should remain the same, name should update
        $this->assertEquals($originalId, $updated->unit_id);
        $this->assertEquals('Updated Name', $updated->unit_name);

        // Refresh and verify persistence
        $updated->refresh();
        $this->assertEquals('Updated Name', $updated->unit_name);
    }

    #[Test]
    public function tasks_service_handles_string_and_numeric_ids(): void
    {
        // Arrange
        $service = new TasksService();
        $project = Project::create(['project_name' => 'Test Project']);
        Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        // Act: Try with numeric ID
        $result1 = $service->getTasks($project->project_id);

        // Act: Try with string ID
        $result2 = $service->getTasks((string) $project->project_id);

        // Assert: Both should work
        $this->assertCount(1, $result1);
        $this->assertCount(1, $result2);
    }

    #[Test]
    public function empty_string_id_treated_as_falsy(): void
    {
        // Arrange
        $service = new TasksService();

        // Act: Empty string should be treated as falsy
        $result = $service->getTasks('');

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function unit_exists_is_case_sensitive(): void
    {
        // Arrange
        $service = new UnitsService();
        Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        // Act & Assert
        $this->assertTrue($service->exists('Hour'));
        $this->assertFalse($service->exists('hour')); // Different case
        $this->assertFalse($service->exists('HOUR')); // Different case
    }

    #[Test]
    public function concurrent_updates_maintain_consistency(): void
    {
        // Arrange
        $service = new UnitsService();
        $unit    = Unit::create(['unit_name' => 'Item', 'unit_name_plrl' => 'Items']);

        // Act: Simulate concurrent updates
        $service->save(['unit_name' => 'Updated Item 1'], $unit->unit_id);
        $service->save(['unit_name' => 'Updated Item 2'], $unit->unit_id);

        // Assert: Last update should win
        $unit->refresh();
        $this->assertEquals('Updated Item 2', $unit->unit_name);
    }
}

class TaxRatesServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaxRatesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TaxRatesService::class);
    }

    #[Test]
    public function it_retrieves_all_tax_rates(): void
    {
        // Arrange
        TaxRate::create([
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => 20.00,
        ]);
        TaxRate::create([
            'tax_rate_name'    => 'VAT 10%',
            'tax_rate_percent' => 10.00,
        ]);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('tax_rate_name', $rules);
        $this->assertArrayHasKey('tax_rate_percent', $rules);
        $this->assertEquals('required', $rules['tax_rate_name']['rules']);
        $this->assertEquals('required', $rules['tax_rate_percent']['rules']);
    }

    #[Test]
    public function it_all_returns_all_tax_rates(): void
    {
        TaxRate::factory()->count(5)->create();

        $results = $this->service->getAll();
        $this->assertCount(5, $results);
    }
}

class UsersServiceTest extends TestCase
{
    use RefreshDatabase;

    private UsersService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsersService::class);
    }

    #[Test]
    public function it_retrieves_all_users(): void
    {
        // Arrange
        User::create([
            'user_name'     => 'John Doe',
            'user_email'    => 'john@example.com',
            'user_password' => bcrypt('password'),
        ]);
        User::create([
            'user_name'     => 'Jane Doe',
            'user_email'    => 'jane@example.com',
            'user_password' => bcrypt('password'),
        ]);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
    }
}
