<?php

namespace Tests\Unit\Quotes;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the QuoteServiceTest module via CI3 HTTP harness.
 */
class QuoteServiceTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Quote Service Client Nu']);
        $quoteId  = $this->databaseInsert('ip_quotes', [
            'client_id'          => $clientId,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_modified' => date('Y-m-d'),
            'user_id'            => 1,
            'invoice_group_id'   => 1,
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_number'       => 'QUO-NU-001',
            'quote_url_key'      => 'nukey001',
        ]);

        /* Act */
        $response = $this->get('/quotes/view/' . $quoteId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'QUO-NU-001');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/quotes/status/all');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/quotes] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
