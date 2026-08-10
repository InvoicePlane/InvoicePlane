<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/Payment_information.php::form() — invoice lookup and
 * payability checks. The provider-allowlist dispatch itself is covered by
 * PaymentProviderAllowlistTest.php.
 */
class PaymentInformationFormTest extends AbstractTestCase
{
    #[Test]
    public function it_redirects_for_an_unknown_invoice_key(): void
    {
        /* Act */
        $response = $this->get('/guest/payment_information/form/does-not-exist');

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_redirects_for_a_draft_invoice_key(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $clientId = $this->seedClient();
        $urlKey   = 'draft-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 1]);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert */
        self::assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_returns_404_for_an_already_paid_invoice_when_unauthenticated(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $clientId = $this->seedClient();
        $urlKey   = 'paid-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_renders_the_form_for_a_payable_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'payable-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, [
            'invoice_url_key'   => $urlKey,
            'invoice_status_id' => 2,
            'payment_method'    => 0,
        ], ['invoice_balance' => '100.00', 'invoice_total' => '100.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_does_not_expose_php_errors_for_an_already_paid_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'paid-noerr-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }
}
