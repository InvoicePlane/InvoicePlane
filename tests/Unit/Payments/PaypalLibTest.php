<?php

namespace Tests\Unit\Payments;

use PaypalLib;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class PaypalLibTest extends TestCase
{
    protected function setUp(): void
    {
        if ( ! defined('BASEPATH')) {
            define('BASEPATH', dirname(__DIR__, 3) . '/system/');
        }

        if ( ! defined('ENVIRONMENT')) {
            define('ENVIRONMENT', 'testing');
        }

        require_once dirname(__DIR__, 3) . '/application/libraries/gateways/PaypalLib.php';
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 200, 'body' => '{"access_token":"test-bearer-token"}'],
            ['status' => 201, 'body' => '{"id":"ORDER-123","status":"CREATED"}'],
        ]));
    }

    protected function tearDown(): void
    {
        putenv('PAYPAL_MOCK_RESPONSES');
    }

    #[Test]
    public function it_creates_an_order_from_the_payment_information(): void
    {
        /* Arrange */
        $paypal = new PaypalLib([
            'demo'          => true,
            'client_id'     => 'client-id',
            'client_secret' => 'client-secret',
        ]);

        $order = [
            'invoice_id'    => 42,
            'currency_code' => 'EUR',
            'value'         => '12.50',
            'custom_id'     => 'invoice-key',
        ];

        /* Act */
        $response = $paypal->createOrder($order);

        /* Assert */
        self::assertSame('{"id":"ORDER-123","status":"CREATED"}', $response);
    }

    #[Test]
    public function it_generates_paypal_request_ids_with_context_and_uuid_format(): void
    {
        /* Arrange */
        $paypal = new PaypalLib([
            'demo'          => true,
            'client_id'     => 'client-id',
            'client_secret' => 'client-secret',
        ]);

        $method = new ReflectionMethod($paypal, 'generateRequestId');
        $method->setAccessible(true);

        /* Act */
        $requestId = $method->invoke($paypal, 'capture');

        /* Assert */
        self::assertMatchesRegularExpression(
            '/\Aip-capture-[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/',
            $requestId
        );
    }
}
