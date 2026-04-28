<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Feature\Payments;

use Modules\Crm\Models\Client;
use Payment_Information;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

use function Tests\Feature\Clients\route;

/**
 * Payment_Information.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 */
#[CoversClass(Payment_Information::class)]
#[CoversClass(Feature\Payments\PaymentInformationController::class)]

class PaymentInformationControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays payment information page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_payment_information_page(): void
    {
        /* Arrange */
        // Payment info may be accessible to guests

        /* Act */
        $response = $this->get('/guest/payment_information');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payment_info');
    }

    /**
     * Test payment information is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /* Arrange */
        // No authentication required

        /* Act */
        $response = $this->get('/guest/payment_information');

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test payment information is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/guest/payment_information');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payment_info');
    }
}
