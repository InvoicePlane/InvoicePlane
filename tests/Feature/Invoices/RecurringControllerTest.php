<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Test coverage for Invoices Recurring Controller (application/modules/invoices/controllers/Recurring.php).
 */
class RecurringControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_lists_recurring_invoices_for_authenticated_admin(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Visible recurring client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'REC-VISIBLE-001']);
        $this->databaseInsert('ip_invoices_recurring', [
            'invoice_id'       => $invoiceId,
            'recur_start_date' => date('Y-m-d'),
            'recur_end_date'   => null,
            'recur_frequency'  => '1M',
            'recur_next_date'  => date('Y-m-d', strtotime('+1 month')),
        ]);

        /* Act */
        $response = $this->get('/invoices/recurring');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Visible recurring client');
        $this->assertResponseBodyContains($response, 'REC-VISIBLE-001');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoices');

        /* Assert */
        $this->assertResponseRedirectTo($response, '/sessions/login');
        $this->assertHtmlOmits($response, 'InvoicePlane');
    }

    #[Test]
    public function it_stops_a_recurring_invoice(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurring();

        /* Act */
        $response = $this->post('/invoices/recurring/stop/' . $recurringId);

        /* Assert */
        $this->assertResponseRedirectTo($response, '/invoices/recurring/index');
        $this->assertDatabaseRow('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId], [
            'invoice_recurring_id' => (string) $recurringId,
            'recur_end_date'       => date('Y-m-d'),
        ]);
    }

    #[Test]
    public function it_does_not_stop_a_recurring_invoice_on_a_non_post_request(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurring(['recur_next_date' => date('Y-m-d', strtotime('+1 month'))]);

        /* Act */
        $this->get('/invoices/recurring/stop/' . $recurringId);

        /* Assert */
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId, 'recur_next_date' => date('Y-m-d', strtotime('+1 month'))]);
    }

    #[Test]
    public function it_deletes_a_recurring_invoice(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurring();

        /* Act */
        $response = $this->post('/invoices/recurring/delete/' . $recurringId);

        /* Assert */
        $this->assertResponseRedirectTo($response, '/invoices/recurring/index');
        $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);
    }

    #[Test]
    public function it_does_not_delete_a_recurring_invoice_on_a_non_post_request(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurring();

        /* Act */
        $this->get('/invoices/recurring/delete/' . $recurringId);

        /* Assert */
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);
    }

    private function seedRecurring(array $overrides = []): int
    {
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        return $this->databaseInsert('ip_invoices_recurring', array_merge([
            'invoice_id'       => $invoiceId,
            'recur_start_date' => date('Y-m-d'),
            'recur_end_date'   => null,
            'recur_frequency'  => '1M',
            'recur_next_date'  => date('Y-m-d', strtotime('+1 month')),
        ], $overrides));
    }
}
