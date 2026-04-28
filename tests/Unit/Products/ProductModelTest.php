<?php

namespace Tests\Unit\Products;

use Mdl_Products;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Products::class)]
class ProductModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('products/mdl_products');
        $this->model = $this->CI->mdl_products;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('product_name', $rules);
        $this->assertArrayHasKey('product_price', $rules);
        $this->assertArrayHasKey('family_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('unit_id', $rules);
    }
}
