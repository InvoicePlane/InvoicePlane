<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;

/**
 * GHSA-w5r4-8w63-c5h2 (PR #1717): Mdl_invoices::is_paid() wraps "status = 4 OR balance = 0.00" in a
 * deferred filter group so the guest controller's where_in('client_id') still ANDs over both arms.
 *
 * GuestInvoiceVisibilityTest uses one foreign client with one invoice shape. These tests use a full
 * matrix (both OR arms, the neither-arm case, a multi-client guest, an unassigned third client) and
 * compare the exact SET of listed invoices, so each of the following mutants changes the set:
 *   - dropping filter_group_start()/filter_group_end()           (foreign rows leak through either arm)
 *   - filter_or_where -> filter_where                            (own zero-balance sent invoice disappears)
 *   - status literal 4 -> other                                  (own paid-with-balance invoice disappears)
 *   - dropping the is_paid() filter entirely                     (own open invoice appears)
 */
#[Group('guest')]
#[Group('security')]
final class GuestPaidListingScopeTest extends AbstractTestCase
{
    private int $clientOwn1;

    private int $clientOwn2;

    private int $clientForeign;

    private int $guestUserId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientOwn1    = $this->seedClient(['client_name' => 'Scope Own One']);
        $this->clientOwn2    = $this->seedClient(['client_name' => 'Scope Own Two']);
        $this->clientForeign = $this->seedClient(['client_name' => 'Scope Foreign']);

        $this->guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'scope_guest',
            'user_password'      => password_hash('guestpass', PASSWORD_DEFAULT),
            'user_email'         => 'scope-guest@test.local',
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        foreach ([$this->clientOwn1, $this->clientOwn2] as $clientId) {
            $this->databaseInsert('ip_user_clients', ['user_id' => $this->guestUserId, 'client_id' => $clientId]);
        }
    }

    #[Test]
    public function it_lists_exactly_the_paid_or_zero_balance_invoices_of_the_guests_own_clients(): void
    {
        /* Arrange */
        $this->seedMatrix();
        $this->loginAsGuest();

        /* Act */
        $response = $this->get('/guest/invoices/status/paid');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $listed = $this->listedNumbers($response);
        self::assertSame(
            ['TQ-O1-PAID', 'TQ-O1-PAIDBAL', 'TQ-O1-ZERO', 'TQ-O2-PAID'],
            $listed,
            'Both OR arms must match inside the guest\'s clients and nowhere else; an open invoice must not match.'
        );
    }

    #[Test]
    public function it_does_not_list_foreign_invoices_matching_either_or_arm_even_for_a_single_client_guest(): void
    {
        /* Arrange: narrow the guest to client one only */
        $this->seedMatrix();
        $this->databaseDelete('ip_user_clients', ['user_id' => $this->guestUserId, 'client_id' => $this->clientOwn2]);
        $this->loginAsGuest();

        /* Act */
        $response = $this->get('/guest/invoices/status/paid');

        /* Assert */
        self::assertSame(['TQ-O1-PAID', 'TQ-O1-PAIDBAL', 'TQ-O1-ZERO'], $this->listedNumbers($response));
    }

    #[Test]
    public function it_keeps_the_open_listing_scoped_to_the_guests_clients(): void
    {
        /* Arrange */
        $this->seedMatrix();
        $this->loginAsGuest();

        /* Act */
        $response = $this->get('/guest/invoices/status/open');

        /* Assert: own sent/viewed invoices with a balance, never the foreign one */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame(['TQ-O1-OPEN', 'TQ-O2-OPEN'], $this->listedNumbers($response));
    }

    #[Test]
    public function it_lists_every_matching_invoice_for_an_admin_without_client_scoping(): void
    {
        /* Arrange: positive control, the same data set seen without the guest where_in() */
        $this->seedMatrix();
        $this->actingAsAdmin();

        /* Act */
        $response = $this->get('/invoices/status/paid');

        /* Assert: is_paid() itself keeps both arms and still excludes the open invoices */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame(
            ['TQ-F-PAID', 'TQ-F-PAIDBAL', 'TQ-F-ZERO', 'TQ-O1-PAID', 'TQ-O1-PAIDBAL', 'TQ-O1-ZERO', 'TQ-O2-PAID'],
            $this->listedNumbers($response),
        );
    }

    /**
     * The guest "paid" list must apply guest_visible(): a draft's balance is 0.00, so it satisfies the
     * "balance = 0.00" arm, and guests would see the number, dates and total of drafts the detail view
     * deliberately refuses to show (status 1 -> 404).
     */
    #[Test]
    public function it_hides_own_draft_invoices_from_the_guest_paid_listing(): void
    {
        /* Arrange */
        $this->seedInvoice($this->clientOwn1, ['invoice_number' => 'TQ-O1-DRAFT', 'invoice_status_id' => 1], ['invoice_balance' => '0.00']);
        $this->seedInvoice($this->clientOwn1, ['invoice_number' => 'TQ-O1-PAID', 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);
        $this->loginAsGuest();

        /* Act */
        $response = $this->get('/guest/invoices/status/paid');

        /* Assert */
        self::assertSame(['TQ-O1-PAID'], $this->listedNumbers($response));
    }

    #[Test]
    public function it_lists_only_the_guests_own_overdue_invoices_in_the_overdue_listing(): void
    {
        /* Arrange */
        $past = date('Y-m-d', strtotime('-10 days'));
        $this->seedInvoice($this->clientOwn1, ['invoice_number' => 'TQ-O1-LATE', 'invoice_status_id' => 2, 'invoice_date_due' => $past], ['invoice_balance' => '30.00']);
        $this->seedInvoice($this->clientOwn1, ['invoice_number' => 'TQ-O1-FUTURE', 'invoice_status_id' => 2], ['invoice_balance' => '30.00']);
        $this->seedInvoice($this->clientOwn1, ['invoice_number' => 'TQ-O1-LATEDRAFT', 'invoice_status_id' => 1, 'invoice_date_due' => $past], ['invoice_balance' => '30.00']);
        $this->seedInvoice($this->clientForeign, ['invoice_number' => 'TQ-F-LATE', 'invoice_status_id' => 2, 'invoice_date_due' => $past], ['invoice_balance' => '30.00']);
        $this->loginAsGuest();

        /* Act */
        $response = $this->get('/guest/invoices/status/overdue');

        /* Assert: only the own, sent, past-due invoice; not a not-yet-due one, a draft or a foreign one */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame(['TQ-O1-LATE'], $this->listedNumbers($response));
    }

    private function seedMatrix(): void
    {
        $rows = [
            // [client, number, status, balance]
            [$this->clientOwn1, 'TQ-O1-PAID', 4, '0.00'],     // arm 1 and arm 2
            [$this->clientOwn1, 'TQ-O1-PAIDBAL', 4, '10.00'], // arm 1 only
            [$this->clientOwn1, 'TQ-O1-ZERO', 2, '0.00'],     // arm 2 only
            [$this->clientOwn1, 'TQ-O1-OPEN', 3, '25.00'],    // neither arm
            [$this->clientOwn2, 'TQ-O2-PAID', 4, '0.00'],
            [$this->clientOwn2, 'TQ-O2-OPEN', 2, '40.00'],
            [$this->clientForeign, 'TQ-F-PAID', 4, '0.00'],
            [$this->clientForeign, 'TQ-F-PAIDBAL', 4, '10.00'],
            [$this->clientForeign, 'TQ-F-ZERO', 2, '0.00'],
            [$this->clientForeign, 'TQ-F-OPEN', 2, '99.00'],
        ];

        foreach ($rows as [$clientId, $number, $status, $balance]) {
            $this->seedInvoice($clientId, ['invoice_number' => $number, 'invoice_status_id' => $status], [
                'invoice_total'   => '100.00',
                'invoice_balance' => $balance,
            ]);
        }
    }

    private function loginAsGuest(): void
    {
        $this->actingAs([
            'user_id'       => $this->guestUserId,
            'user_type'     => 2,
            'user_email'    => 'scope-guest@test.local',
            'user_name'     => 'scope_guest',
            'user_company'  => '',
            'user_language' => 'system',
        ]);
    }

    /** @return list<string> sorted, de-duplicated invoice numbers rendered in the listing */
    private function listedNumbers(HttpResponse $response): array
    {
        preg_match_all('/TQ-[A-Z0-9]+(?:-[A-Z0-9]+)*/', $response->body(), $matches);
        $numbers = array_values(array_unique($matches[0]));
        sort($numbers);

        return $numbers;
    }
}
