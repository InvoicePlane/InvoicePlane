<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class QuotesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_quotes_by_status(): void
    {
        /* Arrange */
        $this->seedQuote(['quote_number' => 'QUO-LIST-001']);

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'QUO-LIST-001');
    }

    // -------------------------------------------------------------------------
    // View
    // -------------------------------------------------------------------------

    #[Test]
    public function it_views_a_single_quote_and_shows_the_quote_number(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote(['quote_number' => 'QUO-VIEW-001']);

        /* Act */
        $response = $this->get('/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'QUO-VIEW-001');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_quote(): void
    {
        /* Arrange */
        $quoteId = $this->seedQuote(['quote_number' => 'QUO-DEL-001']);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId]);

        /* Act */
        $response = $this->post('/quotes/delete/' . $quoteId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $quoteId]);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_index_to_all_quotes_list(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/quotes');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'GET /quotes must redirect to status/all.');
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }

    private function seedQuote(array $overrides = []): int
    {
        $clientId = $this->seedClient(['client_name' => 'Quote Client ' . bin2hex(random_bytes(3))]);

        return $this->databaseInsert('ip_quotes', array_merge([
            'client_id'              => $clientId,
            'user_id'                => 1,
            'invoice_group_id'       => 1,
            'quote_date_created'     => date('Y-m-d'),
            'quote_date_modified'    => date('Y-m-d'),
            'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),
            'quote_number'           => 'QUO-' . bin2hex(random_bytes(4)),
            'quote_url_key'          => bin2hex(random_bytes(16)),
            'quote_discount_amount'  => '0',
            'quote_discount_percent' => '0',
        ], $overrides));
    }
}
