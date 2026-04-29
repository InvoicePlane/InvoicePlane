<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class CustomFieldsModelTest extends AbstractTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        get_instance()->load->model('custom_fields/mdl_custom_fields');
        $this->model = get_instance()->mdl_custom_fields;
    }

    #[Test]
    public function it_retrieves_custom_fields_by_table(): void
    {
        /* Arrange */
        $this->model->save(null, [
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Client Custom Field',
            'custom_field_type'  => 'TEXT',
        ]);
        $this->model->save(null, [
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Another Client Field',
            'custom_field_type'  => 'TEXT',
        ]);
        $this->model->save(null, [
            'custom_field_table' => 'ip_invoices',
            'custom_field_label' => 'Invoice Custom Field',
            'custom_field_type'  => 'TEXT',
        ]);

        /* Act */
        $this->model->get_by_table('ip_clients');
        $result = $this->model->get()->result();

        /* Assert */
        $this->assertCount(2, $result);
        foreach ($result as $field) {
            $this->assertEquals('ip_clients', $field->custom_field_table);
        }
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->model->validation_rules();

        /* Assert */
        $this->assertIsArray($rules);
    }
}
