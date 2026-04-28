<?php

namespace Tests\Unit\Products;

use Mdl_Families;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Families::class)]
class FamiliesModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('families/mdl_families');
        $this->model = $this->CI->mdl_families;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_families', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_families.family_id', $this->model->primary_key);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_order_by'));
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('family_name', $rules);
    }

    #[Test]
    public function it_requires_family_name_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertArrayHasKey('family_name', $rules);
        $this->assertEquals('required', $rules['family_name']['rules']);
    }

    #[Test]
    public function it_has_save_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'save'));
    }

    #[Test]
    public function it_has_delete_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'delete'));
    }
}
