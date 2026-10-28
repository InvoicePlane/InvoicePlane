<?php

namespace Tests\Unit\Libraries\Gateways;

use InvalidArgumentException;
use PaypalLib;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-rf9w-hm5h-4gr2: the PayPal order id was concatenated into the REST path ('v2/checkout/orders/' . $id . '/capture')
 * without validation, so an id such as ../../v1/payments/... redirected the authenticated call to another PayPal API
 * endpoint. captureOrder() and showOrderDetails() now reject anything outside [A-Za-z0-9-_]. The only test that
 * touched this (PaypalRequestExecutorTest) throws its own exception and catches it, so it proved nothing. These send
 * hostile ids through the real library and read what, if anything, reached the HTTP transport.
 */
final class PaypalOrderIdTraversalTest extends TestCase
{
    private string $captureFile;

    protected function setUp(): void
    {
        parent::setUp();
        $file = tempnam(sys_get_temp_dir(), 'paypal-capture-');
        self::assertNotFalse($file);
        $this->captureFile = $file;
        putenv('PAYPAL_MOCK_REQUEST_CAPTURE=' . $this->captureFile);
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 200, 'body' => json_encode(['access_token' => 'fake-token'])],
            ['status' => 200, 'body' => json_encode(['id' => 'ORDER1', 'purchase_units' => []])],
        ]));
    }

    protected function tearDown(): void
    {
        putenv('PAYPAL_MOCK_REQUEST_CAPTURE');
        putenv('PAYPAL_MOCK_RESPONSES');
        @unlink($this->captureFile);
        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function hostileOrderIds(): array
    {
        return [
            'path traversal'      => ['../../v1/payments/payouts'],
            'embedded traversal'  => ['ORDER/../../x'],
            'query string'        => ['ORDER1?fields=all'],
            'fragment'            => ['ORDER1#x'],
            'crlf injection'      => ["ORDER1\r\nX-Evil: 1"],
            'space'               => ['ORDER 1'],
            'percent encoded dot' => ['%2e%2e%2fx'],
            'extra path segment'  => ['ORDER1/capture'],
            'backslash'           => ['ORDER1\\x'],
            'hyphen'              => ['ORDER-1'],
            'underscore'          => ['ORDER_1'],
            'empty'               => [''],
        ];
    }

    #[Test]
    #[DataProvider('hostileOrderIds')]
    public function it_refuses_to_capture_a_hostile_order_id_without_any_http_request(string $orderId): void
    {
        /* Act */
        $result = $this->lib()->captureOrder($orderId);

        /* Assert */
        self::assertFalse($result['status']);
        self::assertInstanceOf(InvalidArgumentException::class, $result['error']);
        self::assertSame('', (string) file_get_contents($this->captureFile), 'No request may reach PayPal for a malformed order id.');
    }

    #[Test]
    #[DataProvider('hostileOrderIds')]
    public function it_refuses_to_show_a_hostile_order_id_without_any_http_request(string $orderId): void
    {
        /* Act */
        $result = $this->lib()->showOrderDetails($orderId);

        /* Assert */
        self::assertFalse($result['status']);
        self::assertInstanceOf(InvalidArgumentException::class, $result['error']);
        self::assertSame('', (string) file_get_contents($this->captureFile));
    }

    #[Test]
    public function it_sends_a_well_formed_order_id_to_exactly_the_capture_path(): void
    {
        /* Act: positive control, so the refusals above are not simply "everything fails" */
        $result = $this->lib()->showOrderDetails('ORDER1a');

        /* Assert */
        self::assertTrue($result['status']);
        $sent = json_decode((string) file_get_contents($this->captureFile), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('GET', $sent['method']);
        self::assertStringEndsWith('/v2/checkout/orders/ORDER1a', $sent['url']);
    }

    private function lib(): PaypalLib
    {
        return new PaypalLib(['client_id' => 'id', 'client_secret' => 'secret', 'demo' => true]);
    }
}
