<?php

declare(strict_types=1);

namespace Tests\Unit\Payments;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PaypalLib;
use ReflectionMethod;
use Tests\AbstractTestCase;

class PaymentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (isset($this->this)) {
            $this->tearDownPaypalLib();
        }
        parent::tearDown();
    }

    protected function setUpCurrencyMinorUnits(): void

        {





            require_once dirname(__DIR__, 3) . '/application/helpers/currency_helper.php';

        }
    #[Test]

    public function it_converts_standard_currency_amounts_to_minor_units(): void

        {

            $this->setUpCurrencyMinorUnits();

            /* Arrange */

            $amount = '12.34';



            /* Act */

            $minorUnits = amount_to_minor_units($amount, 100);



            /* Assert */

            self::assertSame(1234, $minorUnits);

        }
    #[Test]

    public function it_converts_zero_decimal_currency_amounts_without_scaling(): void

        {

            $this->setUpCurrencyMinorUnits();

            /* Arrange */

            $amount = '500';



            /* Act */

            $minorUnits = amount_to_minor_units($amount, 1);



            /* Assert */

            self::assertSame(500, $minorUnits);

        }
    #[Test]

    public function it_converts_minor_units_back_to_major_units(): void

        {

            $this->setUpCurrencyMinorUnits();

            /* Arrange */

            $minorUnits = 1234;



            /* Act */

            $amount = amount_from_minor_units($minorUnits, 100);



            /* Assert */

            self::assertSame(12.34, $amount);

        }
    #[Test]

    public function it_rejects_a_non_positive_minor_unit_multiplier(): void

        {

            $this->setUpCurrencyMinorUnits();

            /* Arrange */

            $amount = '12.34';



            /* Act */

            $this->expectException(InvalidArgumentException::class);

            amount_to_minor_units($amount, 0);



            /* Assert */

            self::fail('The invalid multiplier should have thrown an exception.');

        }
    private string $captureFile;
    protected function setUpPaypalLib(): void

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
    protected function tearDownPaypalLib(): void

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

            $this->setUpPaypalLib();

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

            $this->setUpPaypalLib();

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
