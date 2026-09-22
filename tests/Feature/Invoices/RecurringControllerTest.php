<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Recurring controller — application/modules/invoices/controllers/Recurring.php
 * (routes under /invoices/recurring).
 *
 * Recurring schedules are created from an invoice through
 * POST /invoices/ajax/create_recurring (Invoices\Ajax::create_recurring, see
 * InvoicesAjaxControllerTest for the field-level validation cases); this
 * controller itself only lists, stops and deletes them. The create test below
 * covers the invoice -> schedule -> list round trip end to end. Absorbs
 * Issue1694RecurringInvoiceDeleteCsrfTest.
 */
#[Group('invoices')]
#[CoversClass(\Recurring::class)]
class RecurringControllerTest extends AbstractTestCase
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
    public function it_lists_every_recurring_schedule(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Recurring Client A']);
        $this->seedRecurring($this->seedInvoice($clientId, ['invoice_number' => 'INV-REC-0001']));
        $this->seedRecurring($this->seedInvoice($clientId, ['invoice_number' => 'INV-REC-0002']));

        /* Act */
        $response = $this->get('/invoices/recurring');

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-REC-0001');
        $this->assertResponseBodyContains($response, 'INV-REC-0002');
    }

    // -------------------------------------------------------------------------
    // Create — a schedule is spun off an existing invoice, then shows in the list
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_recurring_schedule_from_an_invoice_and_shows_it_in_the_list(): void
    {
        /**
         * POST /invoices/ajax/create_recurring
         * {
         *   "invoice_id": "<seeded invoice id>",
         *   "recur_start_date": "<today>",
         *   "recur_frequency": "1D"
         * }.
         */
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => 'INV-REC-NEW-01']);

        /* Act */
        $create = $this->ajax('POST', '/invoices/ajax/create_recurring', [
            'invoice_id'       => (string) $invoiceId,
            'recur_start_date' => date('Y-m-d'),
            'recur_frequency'  => '1D',
        ]);
        $list = $this->get('/invoices/recurring');

        /* Assert */
        $this->assertResponseBodyContains($create, '"success":1');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_id' => $invoiceId]);
        $this->assertResponseBodyContains($list, 'INV-REC-NEW-01');
    }

    // -------------------------------------------------------------------------
    // Stop
    // -------------------------------------------------------------------------

    #[Test]
    public function it_stops_a_recurring_schedule(): void
    {
        /* Arrange */
        $id = $this->seedRecurring(null, ['recur_next_date' => date('Y-m-d', strtotime('+1 month'))]);

        /* Act */
        $response = $this->post('/invoices/recurring/stop/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Stopping a schedule redirects to the recurring list.');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_end_date' => date('Y-m-d')]);
    }

    #[Test]
    public function it_does_not_stop_a_recurring_schedule_on_a_plain_get_request(): void
    {
        /* Arrange */
        // ensure_valid_post_request() rejects the HTTP method before the CSRF
        // check runs — a distinct guard from the CSRF-token tests below.
        $nextDate = date('Y-m-d', strtotime('+1 month'));
        $id       = $this->seedRecurring(null, ['recur_next_date' => $nextDate]);

        /* Act */
        $response = $this->get('/invoices/recurring/stop/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A GET is bounced by ensure_valid_post_request(), not acted on.');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id, 'recur_next_date' => $nextDate]);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_recurring_schedule(): void
    {
        /* Arrange */
        $id   = $this->seedRecurring();
        $keep = $this->seedRecurring();

        /* Act */
        $response = $this->post('/invoices/recurring/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'invoices/recurring/index');
        $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $id]);
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $keep]);
    }

    #[Test]
    public function it_does_not_delete_a_recurring_schedule_on_a_plain_get_request(): void
    {
        /* Arrange */
        // Base_Controller::__construct() 404s any GET whose URL contains
        // "delete" before the controller ever runs.
        $id = $this->seedRecurring();

        /* Act */
        $response = $this->get('/invoices/recurring/delete/' . $id);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'The global GET-to-delete gate in Base_Controller must reject this before it is acted on.');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_recurring_schedule_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedRecurring();

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoices/recurring/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_recurring_schedule_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedRecurring();

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/recurring/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_recurring_schedule(): void
    {
        /* Arrange */
        $this->seedRecurring($this->seedInvoice($this->seedClient(), ['invoice_number' => 'INV-REC-SECRET']));
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoices/recurring');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'INV-REC-SECRET');
    }

    private function seedRecurring(?int $invoiceId = null, array $overrides = []): int
    {
        $invoiceId ??= $this->seedInvoice($this->seedClient());

        return $this->databaseInsert('ip_invoices_recurring', array_merge([
            'invoice_id'       => $invoiceId,
            'recur_start_date' => date('Y-m-d'),
            'recur_next_date'  => date('Y-m-d', strtotime('+1 month')),
            'recur_frequency'  => '1',
        ], $overrides));
    }
}
