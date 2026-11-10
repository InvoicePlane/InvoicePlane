<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-6pr3-c7rf-gc7x (invoices) and GHSA-257j-c733-6h5f (quotes): generate_pdf must only mark a document as sent
 * and assign its number when the GET carries a token matching the CSRF cookie. The older regression test only checks
 * the status flip with no token at all; this one pins the number assignment, each rejected token shape, the
 * setting switch and that the PDF itself is still served to a forged request.
 */
#[Group('security')]
final class GeneratePdfCsrfGateTest extends AbstractTestCase
{
    private const TOKEN = 'gate-csrf-token-0123456789-abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->setSetting('generate_invoice_number_for_draft', '0');
        $this->setSetting('generate_quote_number_for_draft', '0');
    }

    /** @return array<string, array{array<string, mixed>, array<string, string>}> */
    public static function rejectedTokens(): array
    {
        return [
            'no token and no cookie' => [[], []],
            'token without cookie'   => [['_ip_csrf' => self::TOKEN], []],
            'cookie without token'   => [[], ['ip_csrf_cookie' => self::TOKEN]],
            'mismatching token'      => [['_ip_csrf' => 'forged-token-0123456789-abcdefghi'], ['ip_csrf_cookie' => self::TOKEN]],
            'token is a prefix'      => [['_ip_csrf' => substr(self::TOKEN, 0, -1)], ['ip_csrf_cookie' => self::TOKEN]],
            'empty token and cookie' => [['_ip_csrf' => ''], ['ip_csrf_cookie' => '']],
            'array token'            => [['_ip_csrf' => [self::TOKEN]], ['ip_csrf_cookie' => self::TOKEN]],
        ];
    }

    #[Test]
    #[DataProvider('rejectedTokens')]
    public function it_neither_marks_nor_numbers_an_invoice_for_a_rejected_token(array $query, array $cookies): void
    {
        /* Arrange */
        $this->setSetting('mark_invoices_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $forged  = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);
        $genuine = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);

        /* Act */
        $forgedResponse  = $this->get('/invoices/generate_pdf/' . $forged . '/1', $query, $cookies);
        $genuineResponse = $this->get('/invoices/generate_pdf/' . $genuine . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Assert */
        self::assertSame(200, $forgedResponse->statusCode());
        self::assertStringStartsWith('%PDF', $forgedResponse->body());
        self::assertLessThan(500, $genuineResponse->statusCode());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $forged, 'invoice_status_id' => 1, 'invoice_number' => '']);
        self::assertSame(2, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $genuine])['invoice_status_id'], 'positive control: a valid pair marks the invoice');
    }

    #[Test]
    #[DataProvider('rejectedTokens')]
    public function it_neither_marks_nor_numbers_a_quote_for_a_rejected_token(array $query, array $cookies): void
    {
        /* Arrange */
        $this->setSetting('mark_quotes_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $forged  = $this->seedQuote();
        $genuine = $this->seedQuote();

        /* Act */
        $forgedResponse  = $this->get('/quotes/generate_pdf/' . $forged . '/1', $query, $cookies);
        $genuineResponse = $this->get('/quotes/generate_pdf/' . $genuine . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Assert */
        self::assertSame(200, $forgedResponse->statusCode());
        self::assertStringStartsWith('%PDF', $forgedResponse->body());
        self::assertLessThan(500, $genuineResponse->statusCode());
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $forged, 'quote_status_id' => 1, 'quote_number' => '']);
        self::assertSame(2, (int) $this->databaseFetchOne('ip_quotes', ['quote_id' => $genuine])['quote_status_id'], 'positive control: a valid pair marks the quote');
    }

    #[Test]
    public function it_assigns_the_invoice_number_together_with_the_sent_status_for_a_valid_token(): void
    {
        /* Arrange */
        $this->setSetting('mark_invoices_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);

        /* Act */
        $this->get('/invoices/generate_pdf/' . $invoiceId . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Assert */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        self::assertSame(2, (int) $invoice['invoice_status_id']);
        self::assertNotSame('', (string) $invoice['invoice_number'], 'the official number is assigned with the status flip');
    }

    #[Test]
    public function it_assigns_the_quote_number_together_with_the_sent_status_for_a_valid_token(): void
    {
        /* Arrange */
        $this->setSetting('mark_quotes_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $quoteId = $this->seedQuote();

        /* Act */
        $this->get('/quotes/generate_pdf/' . $quoteId . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Assert */
        $quote = $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId]);
        self::assertSame(2, (int) $quote['quote_status_id']);
        self::assertNotSame('', (string) $quote['quote_number'], 'the official number is assigned with the status flip');
    }

    #[Test]
    public function it_leaves_documents_alone_when_the_mark_sent_setting_is_off_even_with_a_valid_token(): void
    {
        /* Arrange */
        $this->setSetting('mark_invoices_sent_pdf', '0');
        $this->setSetting('mark_quotes_sent_pdf', '0');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);
        $quoteId   = $this->seedQuote();

        /* Act */
        $this->get('/invoices/generate_pdf/' . $invoiceId . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);
        $this->get('/quotes/generate_pdf/' . $quoteId . '/0', ['_ip_csrf' => self::TOKEN], ['ip_csrf_cookie' => self::TOKEN]);

        /* Assert */
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 1, 'invoice_number' => '']);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => 1, 'quote_number' => '']);
    }

    #[Test]
    public function it_still_streams_the_invoice_pdf_to_a_request_without_a_token(): void
    {
        /* Arrange */
        $this->setSetting('mark_invoices_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'true']);
        $invoiceId = $this->seedInvoice($this->seedClient());

        /* Act */
        $response = $this->get('/invoices/generate_pdf/' . $invoiceId . '/1');

        /* Assert: the safe read is served, only the mutation is withheld */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringStartsWith('%PDF', $response->body());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 1]);
    }

    #[Test]
    public function it_marks_a_document_without_a_token_only_when_csrf_protection_is_switched_off(): void
    {
        /* Arrange */
        $this->setSetting('mark_invoices_sent_pdf', '1');
        $this->setSetting('mark_quotes_sent_pdf', '1');
        $this->withEnvironment(['CSRF_PROTECTION' => 'false']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);
        $quoteId   = $this->seedQuote();

        /* Act */
        $this->get('/invoices/generate_pdf/' . $invoiceId . '/0');
        $this->get('/quotes/generate_pdf/' . $quoteId . '/0');

        /* Assert */
        self::assertSame(2, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_status_id']);
        self::assertSame(2, (int) $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['quote_status_id']);
    }

    private function setSetting(string $key, string $value): void
    {
        if ($this->databaseFetchOne('ip_settings', ['setting_key' => $key]) === null) {
            $this->databaseInsert('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);

            return;
        }

        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }

    private function seedQuote(): int
    {
        $quoteId = $this->databaseInsert('ip_quotes', [
            'client_id'           => $this->seedClient(),
            'user_id'             => 1,
            'invoice_group_id'    => 1,
            'quote_status_id'     => 1,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_modified' => date('Y-m-d'),
            'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),
            'quote_number'        => '',
            'quote_url_key'       => bin2hex(random_bytes(16)),
        ]);
        $this->databaseInsert('ip_quote_amounts', [
            'quote_id' => $quoteId, 'quote_item_subtotal' => '0.00', 'quote_item_tax_total' => '0.00', 'quote_tax_total' => '0.00', 'quote_total' => '0.00',
        ]);

        return $quoteId;
    }
}
