<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Quotes::delete() (application/modules/quotes).
 *
 * Mdl_quotes::default_join() INNER-joins ip_clients and ip_users, so the quote
 * is seeded against a real client (and the baseline admin user) for get_by_id()
 * to resolve.
 */
#[Group('security')]
class Issue1694QuotesDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_quote_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $quoteId = (int) $this->seedModel('Quote', ['client_id' => $this->seedClient()])->quote_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/quotes/delete/' . $quoteId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('quotes/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $quoteId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $quoteId = (int) $this->seedModel('Quote', ['client_id' => $this->seedClient()])->quote_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/quotes/delete/' . $quoteId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId]);
    }
}
