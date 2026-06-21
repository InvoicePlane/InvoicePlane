<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * PaymentMethodsController Feature Tests.
 *
 * Tests payment method management (Cash, Check, Credit Card, PayPal, etc.)
 */
class PaymentMethodsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Wire Transfer Test',
        ]);

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Wire Transfer Test');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
