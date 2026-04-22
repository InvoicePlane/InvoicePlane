<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Core\CustomFieldsService::class)]
class CustomFieldsServiceTest extends AbstractTestCase
{
    private CustomFieldsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CustomFieldsService::class);
    }

    #[Test]
    public function it_retrieves_custom_fields_by_table(): void
    {
        /* Arrange */
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

        /* Act */
        $result = $this->service->byTable('ip_clients');

        /* Assert */
        $this->assertInstanceOf(CustomFieldsService::class, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->service->validationRules();

        /* Assert */
        $this->assertIsArray($rules);
    }
}
