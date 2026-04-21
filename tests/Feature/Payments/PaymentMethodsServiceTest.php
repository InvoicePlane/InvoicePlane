<?php

namespace Modules\Payments\tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Modules\PaymentMethods\Tests\Unit\app;

use Modules\Payments\app\Models\PaymentMethod;
use Modules\Payments\app\Services\PaymentMethodsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentMethodsServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentMethodsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentMethodsService::class);
    }

    #[Test]
    public function it_retrieves_all_payment_methods(): void
    {
        // Arrange
        PaymentMethod::create([
            'payment_method_name' => 'Cash',
        ]);
        PaymentMethod::create([
            'payment_method_name' => 'Credit Card',
        ]);
        PaymentMethod::create([
            'payment_method_name' => 'Bank Transfer',
        ]);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('payment_method_name', $rules);
    }

    #[Test]
    public function it_orders_by_name_by_default(): void
    {
        // Arrange
        PaymentMethod::create(['payment_method_name' => 'Zebra Payment']);
        PaymentMethod::create(['payment_method_name' => 'Apple Pay']);
        PaymentMethod::create(['payment_method_name' => 'Bitcoin']);

        // Act
        $result = $this->service->defaultOrderBy()->get();

        // Assert
        $this->assertCount(3, $result);
        // First should be alphabetically first
        $this->assertEquals('Apple Pay', $result->first()->payment_method_name);
    }
}
