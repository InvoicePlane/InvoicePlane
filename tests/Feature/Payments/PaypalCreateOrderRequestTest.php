<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/gateways/Paypal/paypal_create_order builds the order PayPal will charge. The request that reaches the transport
 * is the only place the amount and the invoice it is bound to can be read, so it is read there: the balance of the
 * invoice (never a constant), its id, its currency and the url key that comes back on capture.
 */
#[Group('security')]
#[Group('payments')]
final class PaypalCreateOrderRequestTest extends AbstractTestCase
{
    private string $captureFile = '';

    protected function setUp(): void
    {
        parent::setUp();
        $file = tempnam(sys_get_temp_dir(), 'paypal-create-');
        self::assertNotFalse($file);
        $this->captureFile = $file;
    }

    protected function tearDown(): void
    {
        @unlink($this->captureFile);
        parent::tearDown();
    }

    #[Test]
    public function it_asks_paypal_to_charge_the_invoice_balance_bound_to_that_invoice(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseUpdate('ip_settings', ['setting_value' => 'EUR'], ['setting_key' => 'gateway_paypal_currency']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '73.25']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->withEnvironment([
            'PAYPAL_MOCK_REQUEST_CAPTURE' => $this->captureFile,
            'PAYPAL_MOCK_RESPONSES'       => json_encode([
                ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])],
                ['status' => 201, 'body' => json_encode(['id' => 'ORDER-NEW-1', 'status' => 'CREATED'])],
            ]),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: the order id is handed back, and the request PayPal received names this invoice and its balance */
        self::assertSame(200, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
        $this->assertResponseBodyContains($response, 'ORDER-NEW-1');

        $sent = json_decode((string) file_get_contents($this->captureFile), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('POST', $sent['method']);
        self::assertStringEndsWith('/v2/checkout/orders', $sent['url']);

        $unit = json_decode($sent['body'], true, 512, JSON_THROW_ON_ERROR)['purchase_units'][0];
        self::assertSame((string) $invoiceId, (string) $unit['invoice_id']);
        self::assertSame('73.25', (string) $unit['amount']['value']);
        self::assertSame('EUR', $unit['amount']['currency_code']);
        self::assertSame($urlKey, $unit['custom_id']);
    }
}
