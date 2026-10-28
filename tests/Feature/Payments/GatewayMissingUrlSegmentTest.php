<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-r382-4chp-xj3p: a request to a guest gateway endpoint without its URL segment (invoice key, order id or Checkout
 * Session id) must answer 404 before anything is processed. CodeIgniter passes a method exactly the segments in the URL,
 * so a required argument turned the request into an ArgumentCountError (a PHP error page), and an unguarded optional one
 * into a TypeError. One test per endpoint that takes a segment.
 */
#[Group('security')]
#[Group('payments')]
final class GatewayMissingUrlSegmentTest extends AbstractTestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function endpoints(): array
    {
        return [
            'stripe create_checkout_session' => ['POST', '/guest/gateways/stripe/create_checkout_session'],
            'stripe callback'                => ['GET', '/guest/gateways/stripe/callback'],
            'paypal paypal_create_order'     => ['POST', '/guest/gateways/paypal/paypal_create_order'],
            'paypal paypal_capture_payment'  => ['POST', '/guest/gateways/paypal/paypal_capture_payment'],
        ];
    }

    #[Test]
    #[DataProvider('endpoints')]
    public function it_answers_404_when_the_url_segment_is_missing(string $method, string $uri): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->request($method, $uri);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'Body: ' . substr($response->body(), 0, 300));
    }
}
