<?php

namespace Tests\Feature\Payments;

use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-2849-wpq6-pq9g: Payment_information::form() validates the invoice, the balance and the enabled gateways and then
 * renders the gateway view through $this->{$payment_provider}(). CodeIgniter routes every public controller method, so
 * while stripe() and paypal() were public a guest could request /guest/payment_information/stripe/<anything> and
 * /paypal/<anything> directly and get the gateway view with the configured publishable key / client id even with online
 * payments and the gateway disabled. Same bug class as GHSA-x2m4-962x-cphw.
 */
#[Group('security')]
#[Group('payments')]
final class PaymentInformationDirectRouteTest extends AbstractTestCase
{
    private const STRIPE_KEY = 'pk_test_PUBLISHABLE_KEY_1234';

    private const PAYPAL_ID = 'PAYPAL_CLIENT_ID_5678';

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsGuest();
        $this->setSetting('gateway_stripe_apiKeyPublic', self::STRIPE_KEY);
        $this->setSetting('gateway_paypal_clientId', self::PAYPAL_ID);
        // form() offers a gateway only when the invoice's payment method (seedInvoice: 1) matches the gateway's
        $this->setSetting('gateway_stripe_payment_method', '1');
        $this->setSetting('gateway_paypal_payment_method', '1');
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function directRoutes(): array
    {
        return [
            'stripe'             => ['stripe', self::STRIPE_KEY],
            'paypal'             => ['paypal', self::PAYPAL_ID],
            'stripe, upper case' => ['STRIPE', self::STRIPE_KEY],
            'paypal, mixed case' => ['PayPal', self::PAYPAL_ID],
        ];
    }

    #[Test]
    #[DataProvider('directRoutes')]
    public function it_does_not_route_a_gateway_helper_directly_with_online_payments_and_the_gateway_disabled(string $segment, string $secret): void
    {
        /* Arrange */
        $this->setSetting('enable_online_payments', '0');
        $this->setSetting('gateway_stripe_enabled', '0');
        $this->setSetting('gateway_paypal_enabled', '0');

        /* Act: no valid invoice key at all */
        $response = $this->get('/guest/payment_information/' . $segment . '/not-a-real-key');

        /* Assert: a 404 that discloses nothing of the gateway view, the settings or a CSRF token */
        self::assertSame(404, $response->statusCode());
        $this->assertResponseBodyNotContains($response, $secret);
        $this->assertResponseBodyNotContains($response, '_ip_csrf');
        $this->assertResponseBodyNotContains($response, 'create_checkout_session');
        $this->assertResponseBodyNotContains($response, 'paypal.com/sdk');
    }

    #[Test]
    #[DataProvider('directRoutes')]
    public function it_does_not_route_a_gateway_helper_directly_even_with_a_valid_invoice_key_and_the_gateway_disabled(string $segment, string $secret): void
    {
        /* Arrange: form() would refuse this combination, so the direct route must not be a way around it */
        $this->setSetting('enable_online_payments', '1');
        $this->setSetting('gateway_stripe_enabled', '0');
        $this->setSetting('gateway_paypal_enabled', '0');
        $urlKey = bin2hex(random_bytes(16));
        $this->seedInvoice($this->seedClient(), ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/' . $segment . '/' . $urlKey);

        /* Assert */
        self::assertSame(404, $response->statusCode());
        $this->assertResponseBodyNotContains($response, $secret);
    }

    #[Test]
    #[DataProvider('directRoutes')]
    public function it_does_not_route_a_gateway_helper_directly_whatever_the_request_method(string $segment, string $secret): void
    {
        /* Arrange */
        $this->setSetting('enable_online_payments', '1');
        $this->setSetting('gateway_stripe_enabled', '1');
        $this->setSetting('gateway_paypal_enabled', '1');

        /* Act */
        $post = $this->post('/guest/payment_information/' . $segment . '/not-a-real-key');

        /* Assert: a POST is no different, and the gateways being enabled does not make the helpers public */
        self::assertSame(404, $post->statusCode());
        $this->assertResponseBodyNotContains($post, $secret);
    }

    #[Test]
    public function it_exposes_nothing_but_the_constructor_and_form_as_public_methods(): void
    {
        /* Arrange */
        $code = (string) file_get_contents(dirname(__DIR__, 3) . '/application/modules/guest/controllers/Payment_information.php');
        $ast  = (new ParserFactory())->createForHostVersion()->parse($code) ?? [];

        /* Act */
        $class  = (new NodeFinder())->findFirstInstanceOf($ast, Class_::class);
        $public = [];
        foreach ($class->getMethods() as $method) {
            if ($method->isPublic()) {
                $public[] = $method->name->toString();
            }
        }

        /* Assert: every other public method of this controller is a URL anyone can request */
        sort($public);
        self::assertSame(['__construct', 'form'], $public);
    }

    #[Test]
    public function it_still_renders_each_enabled_gateway_through_form_for_its_own_invoice_only(): void
    {
        /* Arrange: positive control, the helpers stay reachable from form() */
        $this->setSetting('enable_online_payments', '1');
        $this->setSetting('gateway_stripe_enabled', '1');
        $this->setSetting('gateway_paypal_enabled', '1');
        $urlKey = bin2hex(random_bytes(16));
        $this->seedInvoice($this->seedClient(), ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        /* Act */
        // the segment is the gateway name as listed on the payment page (config case); with two gateways enabled form()
        // compares it case-sensitively against the available drivers
        $stripe = $this->get('/guest/payment_information/form/' . $urlKey . '/Stripe');
        $paypal = $this->get('/guest/payment_information/form/' . $urlKey . '/PayPal');

        /* Assert: each page carries its own gateway's key and this invoice's key, and not the other gateway's */
        self::assertSame(200, $stripe->statusCode(), 'Body: ' . substr($stripe->body(), 0, 300));
        $this->assertResponseBodyContains($stripe, self::STRIPE_KEY);
        $this->assertResponseBodyContains($stripe, $urlKey);
        $this->assertResponseBodyNotContains($stripe, self::PAYPAL_ID);

        self::assertSame(200, $paypal->statusCode());
        $this->assertResponseBodyContains($paypal, self::PAYPAL_ID);
        $this->assertResponseBodyContains($paypal, $urlKey);
        $this->assertResponseBodyNotContains($paypal, self::STRIPE_KEY);
    }

    #[Test]
    public function it_still_refuses_a_disabled_gateway_through_form(): void
    {
        /* Arrange: the check the direct route used to bypass is still enforced where it belongs */
        $this->setSetting('enable_online_payments', '1');
        $this->setSetting('gateway_stripe_enabled', '0');
        $this->setSetting('gateway_paypal_enabled', '1');
        $urlKey = bin2hex(random_bytes(16));
        $this->seedInvoice($this->seedClient(), ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey . '/stripe');

        /* Assert: the disabled gateway's key is not served. form() serves the only enabled gateway (PayPal), so the page
         * itself is fine; what matters is that it carries PayPal and not Stripe. */
        $this->assertResponseBodyNotContains($response, self::STRIPE_KEY);
        $this->assertResponseBodyContains($response, self::PAYPAL_ID);
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
