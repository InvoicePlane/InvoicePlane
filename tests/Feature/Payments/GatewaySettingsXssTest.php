<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-mp5h-3jcr-f7hm: gateway settings echoed into JavaScript string literals on the guest payment pages.
 */
final class GatewaySettingsXssTest extends AbstractTestCase
{
    private const BREAKOUT = "');alert(document.domain);//</script><script>alert(1)</script>";

    #[Test]
    public function it_json_encodes_paypal_settings_in_the_guest_payment_page_script(): void
    {
        /* Arrange */
        $urlKey = $this->seedPayableInvoiceUrlKey();
        $this->setSetting('gateway_paypal_enabled', '1');
        $this->setSetting('gateway_paypal_payment_method', '0');
        $this->setSetting('gateway_paypal_clientId', self::BREAKOUT);
        $this->setSetting('gateway_paypal_currency', self::BREAKOUT);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey . '/paypal');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'window.PayPalConfig');
        $this->assertResponseBodyNotContains($response, self::BREAKOUT);
        $this->assertResponseBodyContains($response, $this->escapedBreakout());
    }

    #[Test]
    public function it_json_encodes_the_stripe_public_key_in_the_guest_payment_page_script(): void
    {
        /* Arrange */
        $urlKey = $this->seedPayableInvoiceUrlKey();
        $this->setSetting('gateway_stripe_enabled', '1');
        $this->setSetting('gateway_stripe_payment_method', '0');
        $this->setSetting('gateway_stripe_apiKeyPublic', self::BREAKOUT);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey . '/stripe');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'stripe = Stripe(');
        $this->assertResponseBodyNotContains($response, self::BREAKOUT);
        $this->assertResponseBodyContains($response, $this->escapedBreakout());
    }

    private function escapedBreakout(): string
    {
        $bs = chr(92);

        return $bs . 'u0027);alert(document.domain);' . $bs . '/' . $bs . '/' . $bs . 'u003C' . $bs . '/script' . $bs . 'u003E';
    }

    private function seedPayableInvoiceUrlKey(): string
    {
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        return $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
