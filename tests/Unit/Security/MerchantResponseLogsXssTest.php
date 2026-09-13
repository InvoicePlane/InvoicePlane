<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for stored XSS in the online payment logs table
 * (payments/partial_online_logs_table.php).
 *
 * merchant_response, merchant_response_driver, merchant_response_reference,
 * and merchant_response_id are populated from external payment-provider
 * responses (Qonto/LetsPeppol/SuperPdp/Stripe/PayPal) via
 * IntegrationPayloadSanitizer::text(), which redacts credentials/tokens/URLs
 * but does not HTML-escape. The view echoed them raw while the adjacent
 * invoice_number cell in the same row was already wrapped in htmlsc() — an
 * inconsistency that let a malicious provider response execute script in an
 * admin's browser.
 */
class MerchantResponseLogsXssTest extends TestCase
{
    private const PAYLOAD = '<script>alert(document.cookie)</script>';

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/date_helper.php';
        require_once dirname(__DIR__, 3) . '/vendor/pocketarc/codeigniter/system/helpers/url_helper.php';

        $this->fakeCi();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['unitCiInstance']);
    }

    #[Test]
    public function it_escapes_the_merchant_response_message(): void
    {
        /* Arrange */
        $log = $this->makeLog(['merchant_response' => self::PAYLOAD]);

        /* Act */
        $output = $this->renderTable([$log]);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringContainsString('&lt;script&gt;alert(document.cookie)&lt;/script&gt;', $output);
    }

    #[Test]
    public function it_escapes_the_merchant_response_driver(): void
    {
        /* Arrange */
        $log = $this->makeLog(['merchant_response_driver' => '"><img src=x onerror=alert(1)>']);

        /* Act */
        $output = $this->renderTable([$log]);

        /* Assert */
        self::assertStringNotContainsString('<img', $output);
        self::assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $output);
    }

    #[Test]
    public function it_escapes_the_merchant_response_reference(): void
    {
        /* Arrange */
        $log = $this->makeLog(['merchant_response_reference' => self::PAYLOAD]);

        /* Act */
        $output = $this->renderTable([$log]);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringContainsString('&lt;script&gt;alert(document.cookie)&lt;/script&gt;', $output);
    }

    #[Test]
    public function it_escapes_the_merchant_response_id(): void
    {
        /* Arrange */
        $log = $this->makeLog(['merchant_response_id' => self::PAYLOAD]);

        /* Act */
        $output = $this->renderTable([$log]);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringContainsString('&lt;script&gt;alert(document.cookie)&lt;/script&gt;', $output);
    }

    #[Test]
    public function it_preserves_benign_log_values(): void
    {
        /* Arrange */
        $log = $this->makeLog([
            'merchant_response'           => 'Payment accepted',
            'merchant_response_driver'    => 'stripe',
            'merchant_response_reference' => 'txn_123',
        ]);

        /* Act */
        $output = $this->renderTable([$log]);

        /* Assert */
        self::assertStringContainsString('Payment accepted', $output);
        self::assertStringContainsString('stripe', $output);
        self::assertStringContainsString('txn_123', $output);
    }

    /**
     * @param array<object> $payment_logs
     */
    private function renderTable(array $payment_logs): string
    {
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/payments/views/partial_online_logs_table.php';

        return (string) ob_get_clean();
    }

    private function makeLog(array $overrides): object
    {
        $defaults = [
            'merchant_response_id'         => 1,
            'invoice_id'                   => 1,
            'invoice_number'               => 'INV-0001',
            'merchant_response_successful' => true,
            'merchant_response_date'       => '2026-09-13',
            'merchant_response_driver'     => 'stripe',
            'merchant_response'            => 'Payment accepted',
            'merchant_response_reference'  => 'txn_123',
        ];

        return (object) array_merge($defaults, $overrides);
    }

    private function fakeCi(): void
    {
        $GLOBALS['unitCiInstance'] = new class () {
            public object $config;

            public object $lang;

            public object $mdl_settings;

            public function __construct()
            {
                $this->config = new class () {
                    public function site_url(string $uri = '', ?string $protocol = null): string
                    {
                        return '/index.php/' . ltrim($uri, '/');
                    }
                };

                $this->lang = new class () {
                    public function line(string $line): string
                    {
                        return $line;
                    }
                };

                $this->mdl_settings = new class () {
                    public function setting(string $key): string
                    {
                        return $key === 'date_format' ? 'Y-m-d' : '';
                    }
                };
            }
        };

        $_POST = [];
    }
}
