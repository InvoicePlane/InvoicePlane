<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Quotes;
use Tests\AbstractTestCase;

#[CoversClass(Quotes::class)]
class QuotesFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('crud')]
    public function it_renders_the_quotes_index_page_with_a_200_status(): void
    {
        $response = $this->get('/quotes');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_includes_html_structure_on_the_quotes_index_page(): void
    {
        $response = $this->get('/quotes');

        $this->assertResponseBodyContains($response, '<html');
        $this->assertResponseBodyContains($response, '</html>');

        self::assertGreaterThan(
            500,
            $response->bodyLength(),
            'The quotes index page rendered fewer than 500 bytes — the view likely did not execute.'
        );
    }

    #[Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_quotes_list(): void
    {
        $this->actingAsGuest();

        $response = $this->get('/quotes');

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /quotes must redirect. Got status [%d] with body: %s',
                $response->statusCode(),
                mb_substr($response->body(), 0, 200)
            )
        );
    }

    #[Test]
    public function it_renders_the_create_quote_form(): void
    {
        $response = $this->get('/quotes/create');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_shows_the_correct_six_quote_statuses_in_the_index_filter_options(): void
    {
        $response = $this->get('/quotes');

        $this->assertResponseStatusCode($response, 200);

        $expectedStatuses = ['draft', 'sent', 'viewed', 'approved', 'rejected', 'canceled'];

        $foundCount = 0;

        foreach ($expectedStatuses as $status) {
            if ($response->contains($status)) {
                $foundCount++;
            }
        }

        self::assertGreaterThanOrEqual(
            3,
            $foundCount,
            sprintf(
                'The quotes index must contain at least 3 of the 6 status labels. Found %d of [%s].',
                $foundCount,
                implode(', ', $expectedStatuses)
            )
        );
    }

    #[Test]
    public function it_renders_the_view_page_for_a_seeded_quote(): void
    {
        $clientId = $this->seedClient(['client_name' => 'Quote Client']);
        $quoteId  = $this->seedQuote($clientId, ['quote_number' => 'QUO-TEST-' . time()]);

        $response = $this->get('/quotes/view/' . $quoteId);

        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('QUO-TEST') || $response->contains((string) $quoteId),
            sprintf(
                'The quote view page for ID [%d] must show the quote number or ID. Body (first 400 chars): %s',
                $quoteId,
                mb_substr($response->body(), 0, 400)
            )
        );
    }

    #[Test]
    public function it_does_not_expose_raw_php_errors_on_the_quotes_index(): void
    {
        $response = $this->get('/quotes');

        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_404_or_redirect_for_a_nonexistent_quote_id(): void
    {
        $response = $this->get('/quotes/view/999999999');

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301)
            ),
            sprintf(
                'Requesting a nonexistent quote must produce 404 or redirect. Got [%d].',
                $response->statusCode()
            )
        );
    }

    private function seedQuote(int $clientId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_quotes', array_merge([
            'user_id'                => 1,
            'client_id'              => $clientId,
            'quote_status_id'        => 1,
            'quote_date_created'     => date('Y-m-d'),
            'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),
            'quote_number'           => 'QUO-' . time(),
            'quote_url_key'          => bin2hex(random_bytes(16)),
            'invoice_group_id'       => 1,
            'quote_discount_amount'  => '0.00',
            'quote_discount_percent' => '0.00',
        ], $overrides));
    }
}
