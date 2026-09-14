<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Quotes controller — application/modules/quotes/controllers/Quotes.php.
 *
 * Quotes are created/edited through quotes/ajax/* (see QuotesAjaxControllerTest).
 * This controller lists, views, deletes and strips tax rates. Absorbs the
 * non-AJAX half of QuotesTest and Issue1694QuotesDeleteCsrfTest.
 */
#[Group('quotes')]
#[CoversClass(\Quotes::class)]
class QuotesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Quote List Client']);
        $this->seedQuote(['client_id' => $clientId, 'quote_number' => 'QUO-LIST-0001']);
        $this->seedQuote(['client_id' => $clientId, 'quote_number' => 'QUO-LIST-0002']);

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $this->assertResponseBodyContains($response, 'QUO-LIST-0001');
        $this->assertResponseBodyContains($response, 'QUO-LIST-0002');
    }

    #[Test]
    public function it_shows_a_single_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Quote View Client']);
        $quoteId  = $this->seedQuote(['client_id' => $clientId, 'quote_number' => 'QUO-VIEW-0007']);

        /* Act */
        $response = $this->get('/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'QUO-VIEW-0007');
        $this->assertResponseBodyContains($response, 'Quote View Client');
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $id       = $this->seedQuote(['client_id' => $clientId, 'quote_number' => 'QUO-DEL-0001']);
        $keep     = $this->seedQuote(['client_id' => $clientId, 'quote_number' => 'QUO-DEL-0002']);

        /* Act */
        $response = $this->post('/quotes/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete redirects back to the quote list.');
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $id]);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_quote_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedQuote();

        /* Act */
        $response = $this->postWithValidCsrfToken('/quotes/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_quote_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedQuote(['quote_number' => 'QUO-CSRF-KEPT']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/quotes/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $id, 'quote_number' => 'QUO-CSRF-KEPT']);
    }

    // -------------------------------------------------------------------------
    // Quote tax rates — Quotes::delete_quote_tax
    // -------------------------------------------------------------------------

    #[Test]
    public function it_removes_a_tax_rate_from_a_quote(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote();
        $rateId  = $this->databaseInsert('ip_quote_tax_rates', ['quote_id' => $quoteId, 'tax_rate_id' => 1, 'include_item_tax' => 0, 'quote_tax_rate_amount' => '0.00']);
        $keepId  = $this->databaseInsert('ip_quote_tax_rates', ['quote_id' => $quoteId, 'tax_rate_id' => 1, 'include_item_tax' => 0, 'quote_tax_rate_amount' => '0.00']);

        /* Act */
        $response = $this->post('/quotes/delete_quote_tax/' . $quoteId . '/' . $rateId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Removing a tax rate redirects back to the quote.');
        $this->assertDatabaseMissing('ip_quote_tax_rates', ['quote_tax_rate_id' => $rateId]);
        $this->assertDatabaseHas('ip_quote_tax_rates', ['quote_tax_rate_id' => $keepId]);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_quote(): void
    {
        /* Arrange */
        $this->seedQuote(['quote_number' => 'QUO-SECRET-0001']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'QUO-SECRET-0001');
    }

    private function seedQuote(array $overrides = []): int
    {
        $clientId = $overrides['client_id'] ?? $this->seedClient();
        unset($overrides['client_id']);

        return (int) $this->seedModel('Quote', array_merge(['client_id' => $clientId], $overrides))->quote_id;
    }
}
