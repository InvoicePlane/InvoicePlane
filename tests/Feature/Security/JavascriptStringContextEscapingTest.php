<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-2849-wpq6-pq9g: stripe.php put the invoice key into a single-quoted JavaScript string with a plain echo
 * (paypal.php already used json_encode), so a quote in the key broke out of the string. The user-client modal did the
 * same with the user id taken from the URL. Values fed by the request or by settings are encoded with json_encode and
 * the hex flags; a quote must reach the page encoded, never raw.
 */
#[Group('security')]
final class JavascriptStringContextEscapingTest extends AbstractTestCase
{
    #[Test]
    public function it_encodes_the_public_key_in_the_stripe_checkout_script(): void
    {
        /* Arrange: the key is a setting, an administrator-controlled value that lands inside a JavaScript string */
        $this->actingAsGuest();
        $this->setSetting('enable_online_payments', '1');
        $this->setSetting('gateway_stripe_enabled', '1');
        $this->setSetting('gateway_stripe_payment_method', '1');
        $this->setSetting('gateway_stripe_apiKeyPublic', "pk_test_ab'cd");
        $urlKey = 'urlkey' . bin2hex(random_bytes(4));
        $this->seedInvoice($this->seedClient(), ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey . '/stripe');

        /* Assert: the raw quote never ends a JavaScript string, the encoded one is there */
        self::assertSame(200, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        $this->assertResponseBodyNotContains($response, "pk_test_ab'cd");
        $this->assertResponseBodyContains($response, 'pk_test_ab\u0027cd');
    }

    #[Test]
    public function it_encodes_the_user_id_in_the_user_client_modal(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act: the administrator's own id, a digit string, as the real page requests it */
        $response = $this->ajax('GET', '/users/ajax/modal_add_user_client/1');

        /* Assert: emitted as an encoded JSON string, not inside single quotes */
        self::assertSame(200, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        $this->assertResponseBodyNotContains($response, "user_id: '1'");
        $this->assertResponseBodyContains($response, 'user_id: "1"');
    }

    #[Test]
    public function it_refuses_a_hostile_user_id_for_the_user_client_modal_and_does_not_echo_it(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->ajax('GET', '/users/ajax/modal_add_user_client/ab%27cd');

        /* Assert: the id guard refuses before any view is rendered */
        self::assertSame(403, $response->statusCode());
        $this->assertResponseBodyNotContains($response, "ab'cd");
        $this->assertResponseBodyNotContains($response, 'user_id:');
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
