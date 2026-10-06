<?php

namespace Tests\Feature\Models;

use Merchant_responses_model;
use MerchantResponseDriver;
use PeppolDocumentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * The ledger of every provider exchange (payments, outbound transmissions, inbound invoices,
 * events). It must record faithfully, never store secrets, and never double-count.
 */
#[CoversClass(Merchant_responses_model::class)]
final class MerchantResponsesModelTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    private Merchant_responses_model $model;

    private int $clientId;

    private int $merchantId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model      = $this->ciModel('integrations/models/Merchant_responses_model');
        $this->clientId   = $this->seedClient();
        $this->merchantId = $this->seedMerchant('Qonto main');
    }

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    // -- payments ------------------------------------------------------------

    #[Test]
    public function it_records_a_successful_payment_as_accepted(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);

        /* Act */
        $id = $this->model->create_payment_response($invoiceId, MerchantResponseDriver::Stripe, 'Paid in full', 'pi_123', true);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('accepted', $row['status']);
        self::assertSame('1', (string) $row['merchant_response_successful']);
        self::assertSame('out', $row['direction']);
        self::assertSame('payment', $row['record_type']);
        self::assertSame('pi_123', $row['merchant_response_reference']);
        self::assertSame((string) $invoiceId, (string) $row['invoice_id']);
    }

    #[Test]
    public function it_records_a_failed_payment_as_rejected(): void
    {
        /* Act */
        $id = $this->model->create_payment_response($this->seedInvoice($this->clientId), MerchantResponseDriver::PayPal, 'Card declined', 'ord_9', false);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('rejected', $row['status']);
        self::assertSame('0', (string) $row['merchant_response_successful']);
    }

    // -- outbound ------------------------------------------------------------

    #[Test]
    public function it_maps_provider_statuses_and_keeps_the_provider_reference(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);

        /* Act */
        $id = $this->model->create_outbound($this->merchantId, $invoiceId, [
            'status' => 'approved', 'external_id' => 'ext-777', 'message' => 'Accepted by the platform', 'http_code' => 201, 'success' => true,
        ], MerchantResponseDriver::Qonto);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('accepted', $row['status']);
        self::assertSame('ext-777', $row['merchant_response_reference']);
        self::assertSame('201', (string) $row['http_code']);
        self::assertSame((string) $this->merchantId, (string) $row['merchant_client_id']);
        self::assertSame('outbound_status', $row['record_type']);
        self::assertNull($row['error_detail'], 'A successful exchange must not record an error detail.');
    }

    #[Test]
    public function it_invents_a_traceable_reference_when_the_provider_returns_none(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);

        /* Act */
        $id = $this->model->create_outbound($this->merchantId, $invoiceId, ['status' => 'pending'], MerchantResponseDriver::Qonto);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertMatchesRegularExpression('/^invoice-' . $invoiceId . '-\d{14}$/', $row['merchant_response_reference']);
    }

    #[Test]
    public function it_records_the_failure_detail_and_explicit_error_code_of_a_rejected_send(): void
    {
        /* Act */
        $id = $this->model->create_outbound(
            $this->merchantId,
            $this->seedInvoice($this->clientId),
            ['status' => 'rejected', 'message' => 'Buyer not on the network', 'http_code' => 422, 'error_code' => 'provider_code'],
            MerchantResponseDriver::Qonto,
            'send_exception',
        );

        /* Assert: the caller's code wins over the provider's; the provider message becomes the detail */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('rejected', $row['status']);
        self::assertSame('send_exception', $row['error_code']);
        self::assertSame('Buyer not on the network', $row['error_detail']);
        self::assertSame('0', (string) $row['merchant_response_successful']);
    }

    #[Test]
    public function it_never_stores_credentials_from_provider_payloads(): void
    {
        /* Act */
        $id = $this->model->create_outbound($this->merchantId, $this->seedInvoice($this->clientId), [
            'status'       => 'error',
            'message'      => 'Unauthorized: Bearer eyJhbGciOi.secret.token and api_key=sk_live_ABC123',
            'access_token' => 'tok_super_secret',
        ], MerchantResponseDriver::Qonto);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        foreach (['eyJhbGciOi', 'sk_live_ABC123', 'tok_super_secret'] as $secret) {
            self::assertStringNotContainsString($secret, (string) $row['merchant_response'], 'message leaked ' . $secret);
            self::assertStringNotContainsString($secret, (string) $row['error_detail'], 'error_detail leaked ' . $secret);
            self::assertStringNotContainsString($secret, (string) $row['raw_payload'], 'raw_payload leaked ' . $secret);
        }
    }

    // -- inbound -------------------------------------------------------------

    #[Test]
    public function it_records_an_inbound_message_without_an_invoice(): void
    {
        /* Act */
        $id = $this->model->create_inbound($this->merchantId, ['external_id' => 'in-1', 'message' => 'New document'], MerchantResponseDriver::Qonto);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertNull($row['invoice_id']);
        self::assertSame('in', $row['direction']);
        self::assertSame('incoming_invoice', $row['record_type']);
        self::assertSame('received', $row['status']);
    }

    #[Test]
    public function it_records_provider_replies_that_carry_no_message_or_reference(): void
    {
        /* Arrange: the ledger columns are NOT NULL, and real providers do omit these fields */
        $invoiceId = $this->seedInvoice($this->clientId);

        /* Act */
        $inbound = $this->model->create_inbound($this->merchantId, [], MerchantResponseDriver::Qonto);
        $item    = $this->model->create_inbound_item($this->merchantId, ['status' => 'received'], MerchantResponseDriver::Qonto);
        $polled  = $this->model->save_status($invoiceId, ['status' => 'pending'], MerchantResponseDriver::Qonto);
        $event   = $this->model->create_event_item($this->merchantId, ['status' => 'delivered'], MerchantResponseDriver::Qonto);

        /* Assert: every call produced a row instead of a database error */
        foreach ([$inbound, $item, $polled, $event] as $id) {
            self::assertGreaterThan(0, $id);
            $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
            self::assertSame('', $row['merchant_response_reference']);
        }
    }

    #[Test]
    public function it_upserts_an_inbound_invoice_by_its_external_id(): void
    {
        /* Act */
        $first  = $this->model->create_inbound_item($this->merchantId, ['id' => 'doc-1', 'status' => 'received', 'message' => 'v1'], MerchantResponseDriver::Qonto);
        $second = $this->model->create_inbound_item($this->merchantId, ['id' => 'doc-1', 'status' => 'delivered', 'message' => 'v2'], MerchantResponseDriver::Qonto);

        /* Assert: same row, updated in place, creation time preserved */
        self::assertSame($first, $second);
        $this->assertDatabaseCount('ip_merchant_responses', 1, ['merchant_response_reference' => 'doc-1']);
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $first]);
        self::assertSame('delivered', $row['status']);
        self::assertSame('v2', $row['merchant_response']);
    }

    #[Test]
    public function it_keeps_same_external_ids_of_different_merchants_apart(): void
    {
        /* Arrange */
        $otherMerchant = $this->seedMerchant('Second merchant');

        /* Act */
        $a = $this->model->create_inbound_item($this->merchantId, ['id' => 'shared-id'], MerchantResponseDriver::Qonto);
        $b = $this->model->create_inbound_item($otherMerchant, ['id' => 'shared-id'], MerchantResponseDriver::Qonto);

        /* Assert */
        self::assertNotSame($a, $b);
        $this->assertDatabaseCount('ip_merchant_responses', 2, ['merchant_response_reference' => 'shared-id']);
    }

    #[Test]
    public function it_stores_only_whitelisted_document_columns_and_sanitizes_the_validation_error(): void
    {
        /* Act: "evil_column" would break the INSERT (or inject) if it were passed through */
        $id = $this->model->create_inbound_item(
            $this->merchantId,
            ['id' => 'doc-2'],
            MerchantResponseDriver::Qonto,
            '0208:0123456789',
            PeppolDocumentType::BillingInvoice,
            [
                'document_path'             => 'incoming/doc-2.xml', 'document_name' => 'doc-2.xml', 'document_validation_status' => 'invalid',
                'document_validation_error' => "Bad XML: password=hunter2\x00", 'evil_column' => 'DROP TABLE', 'created_at' => '1999-01-01 00:00:00',
            ],
        );

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('incoming/doc-2.xml', $row['document_path']);
        self::assertSame('invalid', $row['document_validation_status']);
        self::assertStringNotContainsString('hunter2', (string) $row['document_validation_error']);
        self::assertSame('0208:0123456789', $row['peppol_participant_id']);
        self::assertNotSame('1999-01-01 00:00:00', $row['created_at'], 'created_at is not a caller-controlled document field.');
    }

    #[Test]
    public function it_reports_a_valid_incoming_document_only_for_the_right_merchant_and_status(): void
    {
        /* Arrange */
        $other = $this->seedMerchant('Other');
        $this->model->create_inbound_item($this->merchantId, ['id' => 'ok-doc'], MerchantResponseDriver::Qonto, null, null, ['document_validation_status' => 'valid']);
        $this->model->create_inbound_item($this->merchantId, ['id' => 'bad-doc'], MerchantResponseDriver::Qonto, null, null, ['document_validation_status' => 'invalid']);

        /* Assert */
        self::assertTrue($this->model->has_valid_incoming_document($this->merchantId, 'ok-doc'));
        self::assertFalse($this->model->has_valid_incoming_document($this->merchantId, 'bad-doc'));
        self::assertFalse($this->model->has_valid_incoming_document($other, 'ok-doc'), 'Another merchant must not see this document.');
        self::assertNotNull($this->model->get_valid_incoming_document_id($this->merchantId, 'ok-doc'));
        self::assertNull($this->model->get_valid_incoming_document_id($this->merchantId, 'bad-doc'));
    }

    // -- events --------------------------------------------------------------

    #[Test]
    public function it_deduplicates_identical_events_and_links_them_to_the_invoice(): void
    {
        /* Arrange: an earlier outbound send with provider reference ext-evt */
        $invoiceId = $this->seedInvoice($this->clientId, ['invoice_number' => 'EVT-001']);
        $this->model->create_outbound($this->merchantId, $invoiceId, ['status' => 'sent', 'external_id' => 'ext-evt'], MerchantResponseDriver::Qonto);
        $event = ['external_id' => 'ext-evt', 'status' => 'delivered', 'event_type' => 'delivered', 'message' => 'Delivered to buyer'];

        /* Act */
        $first  = $this->model->create_event_item($this->merchantId, $event, MerchantResponseDriver::Qonto);
        $second = $this->model->create_event_item($this->merchantId, $event, MerchantResponseDriver::Qonto);

        /* Assert */
        self::assertGreaterThan(0, $first);
        self::assertSame(0, $second, 'The identical event must be ignored.');
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $first]);
        self::assertSame((string) $invoiceId, (string) $row['invoice_id']);
        self::assertSame('Delivered to buyer', $row['merchant_response']);
        self::assertSame('invoice_event', $row['record_type']);
        self::assertSame(64, strlen((string) $row['event_hash']));
    }

    #[Test]
    public function it_falls_back_through_message_fields_for_the_event_text(): void
    {
        /* Act */
        $reason = $this->model->create_event_item($this->merchantId, ['id' => 'e1', 'reason' => 'Wrong VAT id'], MerchantResponseDriver::Qonto);
        $status = $this->model->create_event_item($this->merchantId, ['id' => 'e2', 'status' => 'approved'], MerchantResponseDriver::Qonto);

        /* Assert */
        self::assertSame('Wrong VAT id', $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $reason])['merchant_response']);
        self::assertSame('accepted', $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $status])['merchant_response'], 'With no text fields the mapped status is the message.');
    }

    #[Test]
    public function it_lists_events_with_the_merchant_label_and_invoice_number(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId, ['invoice_number' => 'EVT-LIST-1']);
        $this->model->create_outbound($this->merchantId, $invoiceId, ['status' => 'sent', 'external_id' => 'ext-list'], MerchantResponseDriver::Qonto);
        $this->model->create_event_item($this->merchantId, ['external_id' => 'ext-list', 'status' => 'delivered'], MerchantResponseDriver::Qonto);

        /* Act */
        $events = $this->model->get_events();

        /* Assert */
        self::assertCount(1, $events);
        self::assertSame('Qonto main', $events[0]['merchant_client_label']);
        self::assertSame('EVT-LIST-1', $events[0]['invoice_number']);
    }

    // -- status polling ------------------------------------------------------

    #[Test]
    public function it_selects_only_pollable_latest_outbound_sends_for_status_checks(): void
    {
        /* Arrange */
        $pending   = $this->seedInvoice($this->clientId);
        $done      = $this->seedInvoice($this->clientId);
        $synthetic = $this->seedInvoice($this->clientId);
        $reSent    = $this->seedInvoice($this->clientId);
        $otherInv  = $this->seedInvoice($this->clientId);
        $other     = $this->seedMerchant('Elsewhere');

        $this->outbound($pending, 'pending', 'ref-pending');
        $this->outbound($done, 'accepted', 'ref-done');
        $this->outbound($synthetic, 'pending', 'invoice-' . $synthetic . '-20260101000000');
        $this->outbound($reSent, 'pending', 'ref-old', '2026-01-01 10:00:00');
        $this->outbound($reSent, 'sent', 'ref-new', '2026-01-02 10:00:00');
        $this->model->create_outbound($other, $otherInv, ['status' => 'pending', 'external_id' => 'ref-other-merchant'], MerchantResponseDriver::Qonto);

        /* Act */
        $candidates = $this->model->get_status_candidates($this->merchantId);

        /* Assert */
        $byInvoice = array_column($candidates, 'merchant_response_reference', 'invoice_id');
        self::assertSame('ref-pending', $byInvoice[$pending] ?? null);
        self::assertSame('ref-new', $byInvoice[$reSent] ?? null, 'Only the latest send per invoice is polled.');
        self::assertArrayNotHasKey($done, $byInvoice, 'Terminal statuses are not polled.');
        self::assertArrayNotHasKey($synthetic, $byInvoice, 'A locally invented reference cannot be polled at the provider.');
        self::assertArrayNotHasKey($otherInv, $byInvoice, 'Another merchant\'s sends are out of scope.');
        self::assertCount(2, $candidates);
    }

    // -- readers -------------------------------------------------------------

    #[Test]
    public function it_returns_the_latest_outbound_response_for_an_invoice_optionally_per_merchant(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);
        $other     = $this->seedMerchant('Second');
        $this->outbound($invoiceId, 'sent', 'older', '2026-01-01 09:00:00');
        $this->outbound($invoiceId, 'delivered', 'newer', '2026-01-03 09:00:00');
        $this->model->create_outbound($other, $invoiceId, ['status' => 'pending', 'external_id' => 'second-merchant'], MerchantResponseDriver::Qonto);

        /* Assert */
        self::assertSame('second-merchant', $this->model->get_last_response_by_invoice($invoiceId)['merchant_response_reference']);
        self::assertSame('newer', $this->model->get_last_response_by_invoice($invoiceId, $this->merchantId)['merchant_response_reference']);
        self::assertSame([], $this->model->get_last_response_by_invoice(999999));
    }

    #[Test]
    public function it_separates_outbound_history_from_inbound_and_scopes_it_to_the_invoice_and_client(): void
    {
        /* Arrange */
        $mine        = $this->seedInvoice($this->clientId, ['invoice_number' => 'MINE']);
        $otherClient = $this->seedClient(['client_name' => 'Other Co']);
        $theirs      = $this->seedInvoice($otherClient, ['invoice_number' => 'THEIRS']);
        $this->outbound($mine, 'sent', 'mine-1');
        $this->outbound($theirs, 'sent', 'theirs-1');
        $this->model->create_inbound($this->merchantId, ['external_id' => 'inbound-1'], MerchantResponseDriver::Qonto);

        /* Assert */
        self::assertSame(['mine-1'], array_column($this->model->get_outbound_by_invoice($mine), 'merchant_response_reference'));
        self::assertSame(['mine-1'], array_column($this->model->get_by_invoice($mine), 'merchant_response_reference'));
        $forClient = $this->model->get_by_client($this->clientId);
        self::assertSame(['mine-1'], array_column($forClient, 'merchant_response_reference'));
        self::assertSame('MINE', $forClient[0]['invoice_number']);
    }

    #[Test]
    public function it_fetches_incoming_documents_by_id_only_when_they_are_inbound_invoices(): void
    {
        /* Arrange */
        $incoming = $this->model->create_inbound_item($this->merchantId, ['id' => 'inc-1'], MerchantResponseDriver::Qonto);
        $outbound = $this->outbound($this->seedInvoice($this->clientId), 'sent', 'out-1');

        /* Assert */
        self::assertSame('inc-1', $this->model->get_incoming_by_id($incoming)['merchant_response_reference']);
        self::assertSame([], $this->model->get_incoming_by_id($outbound));
        self::assertSame([], $this->model->get_incoming_by_id(999999));
        self::assertSame(['inc-1'], array_column($this->model->get_incoming(), 'merchant_response_reference'));
    }

    // -- status snapshot -----------------------------------------------------

    #[Test]
    public function it_saves_a_polled_status_inheriting_client_and_reference_from_the_last_response(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);
        $this->outbound($invoiceId, 'sent', 'ref-poll');
        $last = $this->model->get_last_response_by_invoice($invoiceId);

        /* Act */
        $id = $this->model->save_status($invoiceId, ['status' => 'rejected', 'message' => 'Refused by buyer', 'http_code' => 200], MerchantResponseDriver::Qonto, $last);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('ref-poll', $row['merchant_response_reference']);
        self::assertSame((string) $this->merchantId, (string) $row['merchant_client_id']);
        self::assertSame('rejected', $row['status']);
        self::assertSame('Refused by buyer', $row['error_detail']);
    }

    #[Test]
    public function it_omits_the_error_detail_when_a_polled_status_is_successful(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->clientId);

        /* Act */
        $id = $this->model->save_status($invoiceId, ['status' => 'accepted', 'message' => 'All good'], MerchantResponseDriver::Qonto);

        /* Assert */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['merchant_response_id' => $id]);
        self::assertSame('accepted', $row['status']);
        self::assertNull($row['error_detail']);
    }

    // -- helpers -------------------------------------------------------------

    private function seedMerchant(string $label): int
    {
        return $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'qonto', 'label' => $label, 'enabled' => 1, 'auth_type' => 'oauth2', 'settings_json' => '{}',
        ]);
    }

    private function outbound(int $invoiceId, string $status, string $reference, ?string $createdAt = null): int
    {
        $id = $this->model->create_outbound($this->merchantId, $invoiceId, ['status' => $status, 'external_id' => $reference], MerchantResponseDriver::Qonto);
        if ($createdAt !== null) {
            $this->databaseUpdate('ip_merchant_responses', ['created_at' => $createdAt], ['merchant_response_id' => $id]);
        }

        return $id;
    }
}
