<?php

namespace tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use src\Models\Family;
use src\Services\FamiliesService;
use Tests\TestCase;

class FamiliesServiceTest extends TestCase
{
    use RefreshDatabase;

    private FamiliesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FamiliesService::class);
    }

    #[Test]
    public function it_returns_a_builder_from_default_select(): void
    {
        $builder = $this->service->defaultSelect();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_a_builder_from_default_order_by(): void
    {
        $builder = $this->service->defaultOrderBy();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_requires_family_name_in_validation_rules(): void
    {
        $rules = $this->service->validationRules();
        $this->assertArrayHasKey('family_name', $rules);
        $this->assertEquals('required', $rules['family_name']['rules']);
    }

    #[Test]
    public function it_returns_all_families_from_get_all(): void
    {
        Family::factory()->count(5)->create();

        $results = $this->service->getAll();
        $this->assertCount(5, $results);
    }

    #[Test]
    public function it_retrieves_all_families(): void
    {
        // Arrange
        Family::create(['family_name' => 'Family 1']);
        Family::create(['family_name' => 'Family 2']);
        Family::create(['family_name' => 'Family 3']);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(3, $result);
    }
}
