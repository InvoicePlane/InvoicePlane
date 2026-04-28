<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Products;

use Mdl_Families;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Families::class)]
class FamiliesModelTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = app(FamiliesService::class);
    }

    #[Test]
    public function it_returns_a_builder_from_default_select(): void
    {
        $builder = $this->model->defaultSelect();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_a_builder_from_default_order_by(): void
    {
        $builder = $this->model->defaultOrderBy();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_requires_family_name_in_validation_rules(): void
    {
        $rules = $this->model->validationRules();
        $this->assertArrayHasKey('family_name', $rules);
        $this->assertEquals('required', $rules['family_name']['rules']);
    }

    #[Test]
    public function it_returns_all_families_from_get_all(): void
    {
        $this->seedModelMany('Family', 5);

        $results = $this->model->getAll();
        $this->assertCount(5, $results);
    }

    #[Test]
    public function it_retrieves_all_families(): void
    {
        /* Arrange */
        Family::create(['family_name' => 'Family 1']);
        Family::create(['family_name' => 'Family 2']);
        Family::create(['family_name' => 'Family 3']);

        /* Act */
        $result = $this->model->defaultSelect()->get();

        /* Assert */
        $this->assertCount(3, $result);
    }
}
