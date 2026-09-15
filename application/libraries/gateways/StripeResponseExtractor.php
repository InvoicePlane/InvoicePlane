<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Pulls the fields the Stripe gateway callback needs off a
 * \Stripe\Checkout\Session (or any object exposing the same properties) and
 * normalises them, so guest/gateways/Stripe.php does not read raw SDK
 * properties inline.
 *
 * Every accessor tolerates a null session (the SDK retrieve() call may have
 * thrown before assignment), so the callback's finally block can query it
 * safely. Mirrors PaypalResponseExtractor for the Stripe side.
 *
 * Live API reachability is a separate concern — see StripeApiClient::ping().
 */
class StripeResponseExtractor
{
    /**
     * Whether Stripe reports the session as paid.
     *
     * Equivalent to comparing against \Stripe\Checkout\Session::PAYMENT_STATUS_PAID,
     * which is the string 'paid'.
     */
    public static function isPaid(?object $session): bool
    {
        return ($session->payment_status ?? null) === 'paid';
    }

    /**
     * The PaymentIntent id, or null when the session carries none.
     */
    public static function extractPaymentIntentId(?object $session): ?string
    {
        $intent = $session->payment_intent ?? null;

        if ($intent === null || $intent === '') {
            return null;
        }

        return (string) $intent;
    }

    /**
     * The invoice url key the checkout session was created for.
     */
    public static function extractInvoiceKey(?object $session): ?string
    {
        $key = $session->client_reference_id ?? null;

        return $key === null ? null : (string) $key;
    }

    /**
     * The session currency as an upper-case ISO code ('' when absent).
     */
    public static function extractCurrency(?object $session): string
    {
        return mb_strtoupper((string) ($session->currency ?? ''));
    }

    /**
     * The captured total, in the currency's minor unit (Stripe's integer
     * amount_total). 0 when absent.
     */
    public static function extractAmountTotalMinor(?object $session): int
    {
        return (int) ($session->amount_total ?? 0);
    }
}
