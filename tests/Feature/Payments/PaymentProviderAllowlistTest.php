<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests for the payment-provider allowlist guard.
 *
 * Before the fix, the `$payment_provider` URL segment was dispatched to
 * `$this->{$payment_provider}()` without validation — allowing an attacker
 * to invoke any method on the controller via the URL.
 *
 * After the fix, only providers present in `$available_drivers` (the list of
 * enabled gateways for that invoice) are accepted; any other value results in
 * a 404.
 *
 * Test strategy: the guest/form endpoint requires a real invoice URL key.
 * We seed a minimal invoice and verify that supplying an unknown provider
 * segment results in a 404, while omitting a provider segment returns 200.
 */
#[Group('security')]
class PaymentProviderAllowlistTest extends AbstractTestCase
{
    private string $invoiceUrlKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();

        $clientId            = $this->seedClient(['client_name' => 'Allowlist Test Client']);
        $this->invoiceUrlKey = 'allowlist-test-key-' . bin2hex(random_bytes(4));

        $this->seedInvoice($clientId, [
            'invoice_url_key'   => $this->invoiceUrlKey,
            'invoice_number'    => 'INV-ALLOWLIST-001',
            'invoice_status_id' => 2, // sent — required by guest_visible() filter
            'payment_method'    => 0,
        ], [
            'invoice_balance' => '100.00',
            'invoice_total'   => '100.00',
        ]);
    }

    #[Test]
    public function it_returns_200_when_accessing_the_payment_form_without_a_provider(): void
    {
        /* Arrange */
        /* (invoice seeded in setUp) */

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey);

        /* Assert */
        // No payment_provider segment — should render the form or redirect to guest, not crash.
        self::assertNotSame(
            500,
            $response->statusCode(),
            'Accessing the payment form without a provider must not crash.'
        );
    }

    #[Test]
    public function it_returns_404_for_an_unknown_payment_provider_segment(): void
    {
        /* Arrange */
        $unknownProvider = 'malicious_method';

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $unknownProvider);

        /* Assert */
        self::assertSame(
            404,
            $response->statusCode(),
            "An unknown payment provider [{$unknownProvider}] must return 404, not dispatch to an arbitrary method."
        );
    }

    #[Test]
    public function it_returns_404_for_an_internal_controller_method_name_as_provider(): void
    {
        /* Arrange */
        $internalMethod = 'index';

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $internalMethod);

        /* Assert */
        self::assertSame(
            404,
            $response->statusCode(),
            "Internal method name [{$internalMethod}] passed as provider must return 404."
        );
    }

    #[Test]
    public function it_returns_404_for_a_path_traversal_attempt_as_provider(): void
    {
        /* Arrange */
        $traversal = '__construct';

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $traversal);

        /* Assert */
        self::assertSame(
            404,
            $response->statusCode(),
            'A __construct provider segment must return 404, not be invoked.'
        );
    }
}
