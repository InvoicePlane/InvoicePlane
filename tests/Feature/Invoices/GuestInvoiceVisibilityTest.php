<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Guest invoice listing visibility — application/modules/guest/controllers/Invoices.php.
 *
 * Verifies that the client scoping in where_in('client_id', $this->user_clients)
 * is not circumvented by ungrouped OR filters in Mdl_invoices methods like is_paid()
 * and is_overdue(). Without proper grouping, an OR at the model layer can escape the
 * guest's client restrictions (CWE-639 / CWE-862).
 */
#[Group('guest')]
#[CoversClass(\Invoices::class)]
class GuestInvoiceVisibilityTest extends AbstractTestCase
{
    private int $clientA;
    private int $clientB;
    private int $guestAccountId;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two clients
        $this->clientA = $this->seedClient(['client_name' => 'Guest Client A']);
        $this->clientB = $this->seedClient(['client_name' => 'Victim Client B']);

        // Create a guest account bound to Client A only
        $this->guestAccountId = $this->databaseInsert('ip_user_accounts', [
            'user_name'          => 'guest_test_user',
            'user_password'      => password_hash('guestpass', PASSWORD_DEFAULT),
            'user_email'         => 'guest-test@test.local',
            'user_type'          => 3, // guest
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        // Bind guest account to Client A only
        $this->databaseInsert('ip_user_clients', [
            'user_id'   => $this->guestAccountId,
            'client_id' => $this->clientA,
        ]);
    }

    // -------------------------------------------------------------------------
    // Paid status listing — OR bypass vulnerability
    // -------------------------------------------------------------------------

    #[Test]
    public function it_does_not_leak_other_clients_paid_invoices_in_listing(): void
    {
        /* Arrange */
        // Guest's own invoice: paid (status 4)
        $this->seedInvoice($this->clientA, [
            'invoice_number'    => 'INV-GUEST-A',
            'invoice_status_id' => 4,
            'invoice_balance'   => '0.00',
        ]);

        // Victim's invoice: paid (status 4) with zero balance
        // This matches BOTH is_paid() conditions: status_id=4 OR balance=0.00
        $this->seedInvoice($this->clientB, [
            'invoice_number'    => 'INV-VICTIM-B',
            'invoice_status_id' => 4,
            'invoice_balance'   => '0.00',
        ]);

        $this->actingAsGuest($this->guestAccountId);

        /* Act */
        $response = $this->get('/guest/invoices/status/paid');

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-GUEST-A', 'Guest should see their own client\'s paid invoices.');
        $this->assertResponseBodyNotContains(
            $response,
            'INV-VICTIM-B',
            'Guest must not see other clients\' invoices, even if they have zero balance (CWE-639 / CWE-862).'
        );
    }

    #[Test]
    public function it_shows_zero_balance_invoices_only_for_the_guests_own_client(): void
    {
        /* Arrange */
        // Guest's own invoice: not paid (status 2, sent) but zero balance
        // This matches is_paid()'s OR arm: status_id=4 OR balance=0.00
        $this->seedInvoice($this->clientA, [
            'invoice_number'    => 'INV-GUEST-ZERO-BAL',
            'invoice_status_id' => 2,
            'invoice_balance'   => '0.00',
        ]);

        // Victim's invoice: zero balance but not paid
        // This should NOT appear because it's not the guest's client
        $this->seedInvoice($this->clientB, [
            'invoice_number'    => 'INV-VICTIM-ZERO-BAL',
            'invoice_status_id' => 2,
            'invoice_balance'   => '0.00',
        ]);

        $this->actingAsGuest($this->guestAccountId);

        /* Act */
        $response = $this->get('/guest/invoices/status/paid');

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-GUEST-ZERO-BAL', 'Zero-balance sent invoices should appear in paid list for guest\'s own client.');
        $this->assertResponseBodyNotContains(
            $response,
            'INV-VICTIM-ZERO-BAL',
            'Zero-balance invoices from other clients must not leak to guest (CWE-639 / CWE-862).'
        );
    }

    #[Test]
    public function it_does_not_leak_other_clients_overdue_invoices_in_listing(): void
    {
        /* Arrange */
        // Create invoices with overdue due dates (is_overdue has similar ungrouped OR)
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Guest's own invoice: overdue
        $this->seedInvoice($this->clientA, [
            'invoice_number'    => 'INV-GUEST-OVERDUE',
            'invoice_status_id' => 2,
            'invoice_due_date'  => $yesterday,
        ]);

        // Victim's invoice: overdue
        $this->seedInvoice($this->clientB, [
            'invoice_number'    => 'INV-VICTIM-OVERDUE',
            'invoice_status_id' => 2,
            'invoice_due_date'  => $yesterday,
        ]);

        $this->actingAsGuest($this->guestAccountId);

        /* Act */
        $response = $this->get('/guest/invoices/status/overdue');

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-GUEST-OVERDUE', 'Guest should see their own client\'s overdue invoices.');
        $this->assertResponseBodyNotContains(
            $response,
            'INV-VICTIM-OVERDUE',
            'Guest must not see other clients\' invoices, even if overdue (CWE-639 / CWE-862).'
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function seedClient(array $overrides = []): int
    {
        return $this->databaseInsert('ip_clients', array_merge([
            'user_id'             => 1,
            'client_name'         => 'Test Client ' . bin2hex(random_bytes(3)),
            'client_active'       => 1,
            'client_date_created' => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    protected function seedInvoice(int $clientId, array $overrides = [], array $amountOverrides = []): int
    {
        return $this->databaseInsert('ip_invoices', array_merge([
            'user_id'               => 1,
            'client_id'             => $clientId,
            'invoice_group_id'      => 1,
            'invoice_status_id'     => 2,
            'invoice_number'        => 'INV-' . bin2hex(random_bytes(4)),
            'invoice_url_key'       => bin2hex(random_bytes(16)),
            'invoice_date_created'  => date('Y-m-d'),
            'invoice_date_modified' => date('Y-m-d H:i:s'),
            'invoice_due_date'      => date('Y-m-d', strtotime('+30 days')),
            'invoice_balance'       => '0.00',
        ], $overrides));
    }
}
