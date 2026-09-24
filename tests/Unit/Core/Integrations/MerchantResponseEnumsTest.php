<?php

namespace Tests\Unit\Core;

use MerchantResponseDirection;
use MerchantResponseDriver;
use MerchantResponseStatus;
use MerchantResponseType;
use PeppolDocumentType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QontoClient;
use RequestMethod;
use SuperPdpClient;

/**
 * Unit tests for the integration value-type enums.
 *
 * These backed enums are persisted in ip_merchant_responses / ip_merchant_clients
 * and exchanged with providers, so their string backing values are a contract:
 * changing one silently breaks stored rows and provider payloads. The behavioural
 * logic worth guarding is MerchantResponseStatus::isSuccessful().
 */
#[Group('unit')]
class MerchantResponseEnumsTest extends TestCase
{
    // -------------------------------------------------------------------------
    // MerchantResponseStatus::isSuccessful()
    // -------------------------------------------------------------------------

    /** @return array<string, array{0: MerchantResponseStatus, 1: bool|null}> */
    public static function statusSuccessProvider(): array
    {
        return [
            'accepted is success'      => [MerchantResponseStatus::Accepted, true],
            'received is success'      => [MerchantResponseStatus::Received, true],
            'sent is success'          => [MerchantResponseStatus::Sent, true],
            'rejected is failure'      => [MerchantResponseStatus::Rejected, false],
            'error is failure'         => [MerchantResponseStatus::Error, false],
            'draft is indeterminate'   => [MerchantResponseStatus::Draft, null],
            'pending is indeterminate' => [MerchantResponseStatus::Pending, null],
            'unknown is indeterminate' => [MerchantResponseStatus::Unknown, null],
        ];
    }

    #[Test]
    #[DataProvider('statusSuccessProvider')]
    public function it_maps_status_to_success_state(MerchantResponseStatus $status, ?bool $expected): void
    {
        self::assertSame($expected, $status->isSuccessful());
    }

    #[Test]
    public function it_backs_every_status_with_the_expected_string(): void
    {
        self::assertSame('draft', MerchantResponseStatus::Draft->value);
        self::assertSame('sent', MerchantResponseStatus::Sent->value);
        self::assertSame('accepted', MerchantResponseStatus::Accepted->value);
        self::assertSame('rejected', MerchantResponseStatus::Rejected->value);
        self::assertSame('received', MerchantResponseStatus::Received->value);
        self::assertSame('error', MerchantResponseStatus::Error->value);
        self::assertSame('unknown', MerchantResponseStatus::Unknown->value);
    }

    #[Test]
    public function it_resolves_a_status_from_its_backing_value(): void
    {
        self::assertSame(MerchantResponseStatus::Accepted, MerchantResponseStatus::from('accepted'));
        self::assertNull(MerchantResponseStatus::tryFrom('not-a-status'));
    }

    // -------------------------------------------------------------------------
    // MerchantResponseType / Direction / Driver
    // -------------------------------------------------------------------------

    #[Test]
    public function it_backs_response_types_with_stable_values(): void
    {
        self::assertSame('payment', MerchantResponseType::Payment->value);
        self::assertSame('outbound_status', MerchantResponseType::OutboundStatus->value);
        self::assertSame('incoming_invoice', MerchantResponseType::IncomingInvoice->value);
        self::assertSame('invoice_event', MerchantResponseType::InvoiceEvent->value);
    }

    #[Test]
    public function it_backs_directions_with_stable_values(): void
    {
        self::assertSame('in', MerchantResponseDirection::In->value);
        self::assertSame('out', MerchantResponseDirection::Out->value);
    }

    #[Test]
    public function it_backs_provider_drivers_with_stable_values(): void
    {
        self::assertSame('superpdp', MerchantResponseDriver::SuperPdp->value);
        self::assertSame('qonto', MerchantResponseDriver::Qonto->value);
        self::assertSame('letspeppol', MerchantResponseDriver::LetsPeppol->value);
        /* legacy payment-gateway rows keep their historical casing */
        self::assertSame('paypal', MerchantResponseDriver::PayPal->value);
        self::assertSame('Stripe', MerchantResponseDriver::Stripe->value);
    }

    #[Test]
    public function it_matches_provider_driver_codes_to_the_client_codes(): void
    {
        /* The driver enum values must line up with each provider's clientCode(). */
        self::assertSame(QontoClient::clientCode(), MerchantResponseDriver::Qonto->value);
        self::assertSame(SuperPdpClient::clientCode(), MerchantResponseDriver::SuperPdp->value);
    }

    // -------------------------------------------------------------------------
    // RequestMethod
    // -------------------------------------------------------------------------

    #[Test]
    public function it_backs_request_methods_with_http_verbs(): void
    {
        self::assertSame('GET', RequestMethod::GET->value);
        self::assertSame('POST', RequestMethod::POST->value);
    }

    // -------------------------------------------------------------------------
    // PeppolDocumentType
    // -------------------------------------------------------------------------

    #[Test]
    public function it_exposes_the_bis_billing_invoice_document_id(): void
    {
        self::assertStringContainsString(
            'billing:3.0',
            PeppolDocumentType::BillingInvoice->value
        );
        self::assertStringContainsString(
            'CreditNote',
            PeppolDocumentType::BillingCreditNote->value
        );
    }

    #[Test]
    public function it_keeps_all_document_type_ids_unique(): void
    {
        $values = array_map(
            static fn (PeppolDocumentType $case): string => $case->value,
            PeppolDocumentType::cases()
        );

        self::assertSame($values, array_unique($values));
    }
}
