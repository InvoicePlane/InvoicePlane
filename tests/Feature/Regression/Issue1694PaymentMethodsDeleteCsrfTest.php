<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Payment_methods::delete() (application/modules/payment_methods).
 */
#[Group('security')]
class Issue1694PaymentMethodsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_payment_method_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $methodId = (int) $this->seedModel('PaymentMethod', ['payment_method_name' => 'Issue 1694 Method'])->payment_method_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/payment_methods/delete/' . $methodId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('payment_methods/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $methodId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $methodId = (int) $this->seedModel('PaymentMethod', ['payment_method_name' => 'Issue 1694 Method No Token'])->payment_method_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/payment_methods/delete/' . $methodId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_id' => $methodId]);
    }
}
