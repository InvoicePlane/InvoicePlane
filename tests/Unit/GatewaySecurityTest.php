<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for GHSA-mp5h-3jcr-f7hm: the PayPal and Stripe guest payment pages
 * echoed $paypal_client_id / $stripe_api_key directly into a JavaScript string literal
 * (`clientId: '<?php echo $paypal_client_id; ?>'`). Both are admin-configured gateway
 * settings, so an admin account (or anyone able to write to ip_settings) could inject a
 * payload that breaks out of the literal and runs arbitrary JS for every guest who loads
 * the invoice payment page. The fix wraps each value in json_encode() with
 * JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT, which both quotes the value and
 * neutralises every character that could end the literal or open a <script> context.
 *
 * These tests render the real view files via ob_start()/include — not PHP's own
 * json_encode() in isolation — so a regression that reverts either template back to a raw
 * echo would actually be caught here.
 */
class GatewaySecurityTest extends TestCase
{
    private const JS_BREAKOUT_PAYLOAD = "'-alert(document.domain)-'</script><script>alert(1)</script>";

    /**
     * Both views call $this->security->... directly rather than going through
     * get_instance(). Since the view is include()'d from inside a method of this class,
     * $this inside it IS this test instance — so that property has to live here, not on
     * $GLOBALS['unitCiInstance'].
     */
    private object $security;

    protected function setUp(): void
    {
        require_once ROOT_PATH . '/application/helpers/echo_helper.php';
        require_once ROOT_PATH . '/application/helpers/settings_helper.php';
        require_once ROOT_PATH . '/vendor/pocketarc/codeigniter/system/helpers/url_helper.php';

        $this->security = new class () {
            public function get_csrf_token_name(): string
            {
                return '_ip_csrf';
            }

            public function get_csrf_hash(): string
            {
                return 'unit-csrf-hash';
            }
        };

        $this->fakeCi();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['unitCiInstance']);
    }

    #[Test]
    public function it_escapes_a_malicious_paypal_client_id(): void
    {
        /* Arrange */
        $html = $this->renderPaypalView(['paypal_client_id' => self::JS_BREAKOUT_PAYLOAD]);

        /* Act */
        $clientIdLine = $this->extractJsAssignment($html, 'clientId');

        /* Assert: the raw payload must not survive, and json_encode's own quoting must wrap it */
        self::assertStringNotContainsString(self::JS_BREAKOUT_PAYLOAD, $html);
        self::assertStringNotContainsString('</script><script>', $html);
        self::assertStringStartsWith('"', $clientIdLine);
        self::assertStringEndsWith('"', $clientIdLine);
    }

    #[Test]
    public function it_escapes_a_malicious_paypal_currency(): void
    {
        /* Arrange */
        $html = $this->renderPaypalView(['currency' => self::JS_BREAKOUT_PAYLOAD]);

        /* Act */
        $currencyLine = $this->extractJsAssignment($html, 'currency');

        /* Assert */
        self::assertStringNotContainsString(self::JS_BREAKOUT_PAYLOAD, $html);
        self::assertStringStartsWith('"', $currencyLine);
        self::assertStringEndsWith('"', $currencyLine);
    }

    #[Test]
    public function it_preserves_a_benign_paypal_client_id(): void
    {
        /* Arrange */
        $html = $this->renderPaypalView(['paypal_client_id' => 'AeAbc123RealClientId']);

        /* Act */
        $clientIdLine = $this->extractJsAssignment($html, 'clientId');

        /* Assert */
        self::assertSame('"AeAbc123RealClientId"', $clientIdLine);
    }

    #[Test]
    public function it_escapes_a_malicious_stripe_api_key(): void
    {
        /* Arrange */
        $html = $this->renderStripeView(['stripe_api_key' => self::JS_BREAKOUT_PAYLOAD]);

        /* Act */
        $stripeCall = $this->extractBetween($html, 'stripe = Stripe(', ');');

        /* Assert */
        self::assertStringNotContainsString(self::JS_BREAKOUT_PAYLOAD, $html);
        self::assertStringNotContainsString('</script><script>', $html);
        self::assertStringStartsWith('"', $stripeCall);
        self::assertStringEndsWith('"', $stripeCall);
    }

    #[Test]
    public function it_preserves_a_benign_stripe_api_key(): void
    {
        /* Arrange */
        $html = $this->renderStripeView(['stripe_api_key' => 'pk_test_realkey123']);

        /* Act */
        $stripeCall = $this->extractBetween($html, 'stripe = Stripe(', ');');

        /* Assert */
        self::assertSame('"pk_test_realkey123"', $stripeCall);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function renderPaypalView(array $overrides): string
    {
        $vars = array_merge([
            'advanced_credit_cards' => false,
            'venmo'                 => false,
            'paypal_client_id'      => 'default-client-id',
            'currency'              => 'USD',
            'invoice_url_key'       => 'abc123',
        ], $overrides);

        extract($vars);

        ob_start();
        include ROOT_PATH . '/application/modules/guest/views/gateways/paypal.php';

        return (string) ob_get_clean();
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function renderStripeView(array $overrides): string
    {
        $vars = array_merge([
            'stripe_api_key'  => 'default-api-key',
            'invoice_url_key' => 'abc123',
        ], $overrides);

        extract($vars);

        ob_start();
        include ROOT_PATH . '/application/modules/guest/views/gateways/stripe.php';

        return (string) ob_get_clean();
    }

    /**
     * Pulls the right-hand side of `<jsKey>: <value>,` (or the last assignment on the
     * line before a closing brace) out of the rendered <script> block.
     */
    private function extractJsAssignment(string $html, string $jsKey): string
    {
        $pattern = '/' . preg_quote($jsKey, '/') . ':\s*(.+?),?\s*\r?\n/';
        self::assertMatchesRegularExpression($pattern, $html, "Could not find a {$jsKey}: assignment in the rendered view.");
        preg_match($pattern, $html, $matches);

        return rtrim($matches[1], ',');
    }

    private function extractBetween(string $html, string $start, string $end): string
    {
        $startPos = strpos($html, $start);
        self::assertNotFalse($startPos, "Could not find \"{$start}\" in the rendered view.");
        $startPos += strlen($start);
        $endPos = strpos($html, $end, $startPos);
        self::assertNotFalse($endPos, "Could not find \"{$end}\" after \"{$start}\" in the rendered view.");

        return substr($html, $startPos, $endPos - $startPos);
    }

    private function fakeCi(): void
    {
        $GLOBALS['unitCiInstance'] = new class () {
            public object $config;

            public object $mdl_settings;

            public function __construct()
            {
                $this->config = new class () {
                    public function site_url(string $uri = '', ?string $protocol = null): string
                    {
                        return '/index.php/' . ltrim($uri, '/');
                    }

                    public function base_url(string $uri = '', ?string $protocol = null): string
                    {
                        return 'https://invoiceplane.example/' . ltrim($uri, '/');
                    }
                };

                $this->mdl_settings = new class () {
                    public function setting(string $key): string
                    {
                        return $key === 'current_version' ? '1.0.0' : '';
                    }
                };
            }
        };
    }
}
