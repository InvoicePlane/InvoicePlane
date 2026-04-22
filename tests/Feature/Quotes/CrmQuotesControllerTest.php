<?php

namespace Tests\Feature\Quotes;

use Modules\Crm\Controllers\QuotesController as GuestQuotesController;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * QuotesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal quote viewing and approval.
 */
#[CoversClass(GuestQuotesController::class)]
#[CoversClass(Tests\Feature\Quotes\CrmQuotesController::class)]

class CrmQuotesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays guest quotes list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_quotes_list(): void
    {
        /* Arrange */
        // Guest portal accessible without authentication

        /* Act */
        $response = $this->get(route('guest.quotes'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_quotes');
    }

    /**
     * Test view displays specific quote by URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_quote_by_url_key(): void
    {
        /* Arrange */
        $quote = $this->seedModel('Quote', ['quote_url_key' => 'test-quote-key']);

        /* Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'test-quote-key']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_quote_view');
        $response->assertViewHas('quote');

        $viewQuote = $response->viewData('quote');
        $this->assertEquals($quote->quote_id, $viewQuote->quote_id);
    }

    /**
     * Test view returns 404 for invalid URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_for_invalid_quote_url_key(): void
    {
        /* Arrange */
        // No quote with this URL key

        /* Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'non-existent-key']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test approve updates quote status to approved.
     */
    #[Test]
    public function it_approves_quote_when_approve_called(): void
    {
        /* Arrange */
        $quote = $this->seedModel('Quote', [
            'quote_url_key'   => 'approve-key',
            'quote_status_id' => 2, // Sent
        ]);

        /* Act */
        $response = $this->get(route('guest.quotes.approve', ['urlKey' => 'approve-key']));

        /* Assert */
        $response->assertRedirect();
        $response->assertSessionHas('alert_success');

        $quote->refresh();
        $this->assertEquals(4, $quote->quote_status_id); // Approved
    }

    /**
     * Test approve returns 404 for invalid URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_approving_non_existent_quote(): void
    {
        /* Arrange */
        // No quote with this URL key

        /* Act */
        $response = $this->get(route('guest.quotes.approve', ['urlKey' => 'invalid-key']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test quote operations are accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /* Arrange */
        $quote = $this->seedModel('Quote', ['quote_url_key' => 'guest-quote-key']);

        /* Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'guest-quote-key']));

        /* Assert */
        $response->assertOk();
    }
}
