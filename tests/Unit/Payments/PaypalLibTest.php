<?php

namespace Tests\Unit\Payments;

use PaypalLib;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class PaypalLibTest extends TestCase
{
    private string $captureFile;

    protected function setUp(): void
    {
        if ( ! defined('BASEPATH')) {
            define('BASEPATH', dirname(__DIR__, 3) . '/system/');
        }

        if ( ! defined('ENVIRONMENT')) {
            define('ENVIRONMENT', 'testing');
        }

        require_once dirname(__DIR__, 2) . '/Fakes/Payments/FakePaypalHttpClient.php';
        require_once dirname(__DIR__, 3) . '/application/libraries/gateways/PaypalLib.php';
        $captureFile = tempnam(sys_get_temp_dir(), 'paypal-request-');
        self::assertNotFalse($captureFile);
        $this->captureFile = $captureFile;
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 200, 'body' => '{"access_token":"test-bearer-token"}'],
            ['status' => 201, 'body' => '{"id":"ORDER-123","status":"CREATED"}'],
        ]));
        putenv('PAYPAL_MOCK_REQUEST_CAPTURE=' . $this->captureFile);
    }

    protected function tearDown(): void
    {
        putenv('PAYPAL_MOCK_RESPONSES');
        putenv('PAYPAL_MOCK_REQUEST_CAPTURE');
        if (is_file($this->captureFile)) {
            unlink($this->captureFile);
        }
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
        $request = json_decode((string) file_get_contents($this->captureFile), true, 512, JSON_THROW_ON_ERROR);
        $body    = json_decode($request['body'], true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('POST', $request['method']);
        self::assertStringEndsWith('/v2/checkout/orders', $request['url']);
        self::assertSame('42', (string) $body['purchase_units'][0]['invoice_id']);
        self::assertSame('12.50', $body['purchase_units'][0]['amount']['value']);
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
