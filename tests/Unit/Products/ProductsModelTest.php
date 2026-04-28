<?php

namespace Tests\Unit\Products;

use Mdl_Products;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Products::class)]
class ProductsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('products/mdl_products');
        $this->model = $this->CI->mdl_products;
    }

    #[Test]
    public function it_returns_products_by_ids(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_empty_collection_when_no_matching_ids(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_empty_collection_when_empty_array_provided(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_handles_duplicate_ids_in_array(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
