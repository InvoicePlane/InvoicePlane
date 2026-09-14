<?php

namespace Tests\Unit\Libraries\Gateways;

use PHPUnit\Framework\TestCase;
use StripeResponseExtractor;

/**
 * Unit coverage for StripeResponseExtractor — the field accessors
 * guest/gateways/Stripe.php::callback() reads off the retrieved
 * \Stripe\Checkout\Session.
 *
 * The end-to-end behaviour is exercised in
 * tests/Feature/Payments/StripeFlowTest.php.
 */
class StripeResponseExtractorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_a_paid_session_as_paid(): void
    {
        $this->assertTrue(StripeResponseExtractor::isPaid($this->session(['payment_status' => 'paid'])));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_an_unpaid_session_as_not_paid(): void
    {
        $this->assertFalse(StripeResponseExtractor::isPaid($this->session(['payment_status' => 'unpaid'])));
        $this->assertFalse(StripeResponseExtractor::isPaid($this->session(['payment_status' => 'no_payment_required'])));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_treats_a_null_session_as_not_paid(): void
    {
        $this->assertFalse(StripeResponseExtractor::isPaid(null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_the_payment_intent_id(): void
    {
        $this->assertSame('pi_123', StripeResponseExtractor::extractPaymentIntentId($this->session(['payment_intent' => 'pi_123'])));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_when_the_payment_intent_is_absent_or_empty(): void
    {
        $this->assertNull(StripeResponseExtractor::extractPaymentIntentId($this->session(['payment_intent' => null])));
        $this->assertNull(StripeResponseExtractor::extractPaymentIntentId($this->session(['payment_intent' => ''])));
        $this->assertNull(StripeResponseExtractor::extractPaymentIntentId(null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_the_invoice_key_from_the_client_reference_id(): void
    {
        $this->assertSame('abc-url-key', StripeResponseExtractor::extractInvoiceKey($this->session(['client_reference_id' => 'abc-url-key'])));
        $this->assertNull(StripeResponseExtractor::extractInvoiceKey($this->session(['client_reference_id' => null])));
        $this->assertNull(StripeResponseExtractor::extractInvoiceKey(null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_upper_cases_the_currency_and_defaults_to_empty_string(): void
    {
        $this->assertSame('EUR', StripeResponseExtractor::extractCurrency($this->session(['currency' => 'eur'])));
        $this->assertSame('', StripeResponseExtractor::extractCurrency($this->session(['currency' => null])));
        $this->assertSame('', StripeResponseExtractor::extractCurrency(null));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_extracts_the_minor_unit_total_as_an_integer(): void
    {
        $this->assertSame(5000, StripeResponseExtractor::extractAmountTotalMinor($this->session(['amount_total' => 5000])));
        $this->assertSame(5000, StripeResponseExtractor::extractAmountTotalMinor($this->session(['amount_total' => '5000'])));
        $this->assertSame(0, StripeResponseExtractor::extractAmountTotalMinor($this->session(['amount_total' => null])));
        $this->assertSame(0, StripeResponseExtractor::extractAmountTotalMinor(null));
    }

    /**
     * A stand-in for \Stripe\Checkout\Session: a plain object exposing the same
     * properties, so these tests need neither the SDK nor a live API.
     *
     * @param array<string, mixed> $overrides
     */
    private function session(array $overrides): object
    {
        return (object) $overrides;
    }
}
