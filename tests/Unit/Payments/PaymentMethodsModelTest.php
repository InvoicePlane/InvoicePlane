<?php

namespace Tests\Unit\Payments;

use Mdl_Payment_Methods;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payment_Methods::class)]
class PaymentMethodsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payment_methods/mdl_payment_methods');
        $this->model = $this->CI->mdl_payment_methods;
    }

    #[Test]
    public function it_retrieves_all_payment_methods(): void
    {
        /* Arrange */
            'payment_method_name' => 'Cash',
        ]);
            'payment_method_name' => 'Credit Card',
        ]);
            'payment_method_name' => 'Bank Transfer',
        ]);

        /* Act */
        $result = $this->model->default_select();

        /* Assert */
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->model->validation_rules();

        /* Assert */
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('payment_method_name', $rules);
    }

    #[Test]
    public function it_orders_by_name_by_default(): void
    {
        /* Arrange */

        /* Act */
        $result = $this->model->default_order_by();

        /* Assert */
        $this->assertCount(3, $result);
        // First should be alphabetically first
        $this->assertEquals('Apple Pay', $result->first()->payment_method_name);
    }
}
