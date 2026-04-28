<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Core\CustomFieldsService::class)]
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
        $this->markTestIncomplete('This test uses Laravel Model::create pattern which needs to be refactored to use CodeIgniter insert patterns');
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
