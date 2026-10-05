<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Quotes;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * guest/controllers/Quotes.php — the portal where a client's own guest user reviews, approves
 * and rejects quotes. Every query must be confined to the guest's assigned clients.
 */
#[CoversClass(Quotes::class)]
final class GuestQuotesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const DRAFT = 1;
    private const SENT = 2;
    private const VIEWED = 3;
    private const APPROVED = 4;
    private const REJECTED = 5;

    private int $ownClient;
    private int $otherClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ownClient   = $this->seedClient(['client_name' => 'Own Client']);
        $this->otherClient = $this->seedClient(['client_name' => 'Other Client']);

        $guestId = $this->databaseInsert('ip_users', [
            'user_type' => 2, 'user_name' => 'Portal Guest', 'user_email' => 'portal-guest@test.local',
            'user_password' => password_hash('secret123', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(8)),
            'user_language' => 'system', 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $guestId, 'client_id' => $this->ownClient]);
        $this->actingAs(['user_id' => $guestId, 'user_type' => 2, 'user_email' => 'portal-guest@test.local', 'user_name' => 'Portal Guest']);
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_redirects_the_index_to_the_open_quotes(): void
    {
        /* Act */
        $response = $this->get('/guest/quotes');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'guest/quotes/status/open');
    }

    #[Test]
    public function it_lists_only_the_guests_own_open_quotes(): void
    {
        /* Arrange */
        $this->seedGuestQuote($this->ownClient, self::SENT, 'OWN-SENT');
        $this->seedGuestQuote($this->ownClient, self::VIEWED, 'OWN-VIEWED');
        $this->seedGuestQuote($this->ownClient, self::DRAFT, 'OWN-DRAFT');
        $this->seedGuestQuote($this->ownClient, self::APPROVED, 'OWN-APPROVED');
        $this->seedGuestQuote($this->otherClient, self::SENT, 'OTHER-SENT');

        /* Act */
        $response = $this->get('/guest/quotes/status/open');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'OWN-SENT');
        $this->assertResponseBodyContains($response, 'OWN-VIEWED');
        foreach (['OWN-DRAFT', 'OWN-APPROVED', 'OTHER-SENT'] as $hidden) {
            $this->assertResponseBodyNotContains($response, $hidden);
        }
    }

    /** @return array<string, array{0: string, 1: list<string>, 2: list<string>}> */
    public static function statusFilters(): array
    {
        return [
            'all shows every guest-visible own quote' => ['all', ['OWN-SENT', 'OWN-APPROVED', 'OWN-REJECTED'], ['OWN-DRAFT', 'OTHER-APPROVED']],
            'viewed'                                   => ['viewed', ['OWN-VIEWED'], ['OWN-SENT', 'OWN-APPROVED']],
            'approved'                                 => ['approved', ['OWN-APPROVED'], ['OWN-REJECTED', 'OTHER-APPROVED']],
            'rejected'                                 => ['rejected', ['OWN-REJECTED'], ['OWN-APPROVED', 'OTHER-APPROVED']],
        ];
    }

    /**
     * @param list<string> $shown
     * @param list<string> $hidden
     */
    #[Test]
    #[DataProvider('statusFilters')]
    public function it_filters_the_listing_by_status_without_crossing_clients(string $status, array $shown, array $hidden): void
    {
        /* Arrange */
        $this->seedGuestQuote($this->ownClient, self::DRAFT, 'OWN-DRAFT');
        $this->seedGuestQuote($this->ownClient, self::SENT, 'OWN-SENT');
        $this->seedGuestQuote($this->ownClient, self::VIEWED, 'OWN-VIEWED');
        $this->seedGuestQuote($this->ownClient, self::APPROVED, 'OWN-APPROVED');
        $this->seedGuestQuote($this->ownClient, self::REJECTED, 'OWN-REJECTED');
        $this->seedGuestQuote($this->otherClient, self::APPROVED, 'OTHER-APPROVED');

        /* Act */
        $response = $this->get('/guest/quotes/status/' . $status);

        /* Assert */
        foreach ($shown as $number) {
            $this->assertResponseBodyContains($response, $number);
        }
        foreach ($hidden as $number) {
            $this->assertResponseBodyNotContains($response, $number);
        }
    }

    #[Test]
    public function it_shows_an_own_quote_and_marks_a_sent_quote_as_viewed(): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::SENT, 'OWN-VIEW-1');

        /* Act */
        $response = $this->get('/guest/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'OWN-VIEW-1');
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::VIEWED]);
    }

    #[Test]
    public function it_hides_another_clients_quote_and_does_not_mark_it_viewed(): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->otherClient, self::SENT, 'OTHER-VIEW-1');

        /* Act */
        $response = $this->get('/guest/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertResponseBodyNotContains($response, 'OTHER-VIEW-1');
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::SENT]);
    }

    #[Test]
    public function it_hides_a_draft_quote_even_from_its_own_client(): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::DRAFT, 'OWN-DRAFT-VIEW');

        /* Act */
        $response = $this->get('/guest/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertResponseBodyNotContains($response, 'OWN-DRAFT-VIEW');
    }

    #[Test]
    public function it_refuses_the_pdf_of_another_clients_quote(): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->otherClient, self::SENT, 'OTHER-PDF-1');

        /* Act */
        $response = $this->get('/guest/quotes/generate_pdf/' . $quoteId);

        /* Assert: 404 and no mark-as-viewed side effect */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::SENT]);
    }

    /** @return array<string, array{0: string, 1: int}> */
    public static function decisions(): array
    {
        return ['approve' => ['approve', self::APPROVED], 'reject' => ['reject', self::REJECTED]];
    }

    #[Test]
    #[DataProvider('decisions')]
    public function it_applies_the_decision_to_an_own_open_quote(string $action, int $expectedStatus): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::SENT, 'OWN-DECIDE-1');

        /* Act */
        $response = $this->postWithValidCsrfToken('/guest/quotes/' . $action . '/' . $quoteId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A decision must redirect back to the portal.');
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => $expectedStatus]);
    }

    #[Test]
    #[DataProvider('decisions')]
    public function it_cannot_decide_on_another_clients_quote(string $action, int $expectedStatus): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->otherClient, self::SENT, 'OTHER-DECIDE-1');

        /* Act */
        $response = $this->postWithValidCsrfToken('/guest/quotes/' . $action . '/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::SENT]);
    }

    #[Test]
    #[DataProvider('decisions')]
    public function it_cannot_change_a_decision_that_was_already_made(string $action, int $expectedStatus): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::APPROVED, 'OWN-FINAL-1');

        /* Act */
        $response = $this->postWithValidCsrfToken('/guest/quotes/' . $action . '/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::APPROVED]);
    }

    #[Test]
    #[DataProvider('decisions')]
    public function it_cannot_decide_on_a_draft_quote(string $action, int $expectedStatus): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::DRAFT, 'OWN-DRAFT-DECIDE');

        /* Act */
        $response = $this->postWithValidCsrfToken('/guest/quotes/' . $action . '/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::DRAFT]);
    }

    #[Test]
    #[DataProvider('decisions')]
    public function it_ignores_a_decision_sent_as_a_plain_get(string $action, int $expectedStatus): void
    {
        /* Arrange */
        $quoteId = $this->seedGuestQuote($this->ownClient, self::SENT, 'OWN-GET-1');

        /* Act */
        $response = $this->get('/guest/quotes/' . $action . '/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => self::SENT]);
    }

    private function seedGuestQuote(int $clientId, int $status, string $number): int
    {
        $quoteId = $this->databaseInsert('ip_quotes', [
            'user_id' => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => $status,
            'quote_number' => $number, 'quote_url_key' => bin2hex(random_bytes(16)),
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
        ]);
        $this->databaseInsert('ip_quote_amounts', [
            'quote_id' => $quoteId, 'quote_item_subtotal' => '10.00', 'quote_item_tax_total' => '0.00',
            'quote_tax_total' => '0.00', 'quote_total' => '10.00',
        ]);

        return $quoteId;
    }
}
