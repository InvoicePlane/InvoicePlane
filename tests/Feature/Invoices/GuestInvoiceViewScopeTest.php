<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/invoices/view/<id> must serve a guest only the invoices of the clients assigned to the account, even for an
 * invoice that is otherwise visible (sent, not a draft). Removing the client scope in Invoices::view() leaves the
 * listing tests green, because they never request a single invoice by id (CWE-639 / CWE-862).
 */
#[Group('guest')]
#[Group('security')]
final class GuestInvoiceViewScopeTest extends AbstractTestCase
{
    private int $guestId;

    private int $ownInvoice;

    private int $foreignInvoice;

    protected function setUp(): void
    {
        parent::setUp();

        $own     = $this->seedClient(['client_name' => 'Own Client']);
        $foreign = $this->seedClient(['client_name' => 'Foreign Client Ltd']);

        $this->guestId = $this->databaseInsert('ip_users', [
            'user_name'          => 'guest_view_user',
            'user_password'      => password_hash('guestpass', PASSWORD_DEFAULT),
            'user_email'         => 'guest-view@test.local',
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $this->guestId, 'client_id' => $own]);

        $this->ownInvoice     = $this->seedInvoice($own, ['invoice_number' => 'INV-OWN-VIEW', 'invoice_status_id' => 2], ['invoice_balance' => '10.00']);
        $this->foreignInvoice = $this->seedInvoice($foreign, ['invoice_number' => 'INV-FOREIGN-VIEW', 'invoice_status_id' => 2], ['invoice_balance' => '10.00']);

        $this->actingAs([
            'user_id'       => $this->guestId,
            'user_type'     => 2,
            'user_email'    => 'guest-view@test.local',
            'user_name'     => 'guest_view_user',
            'user_company'  => '',
            'user_language' => 'system',
        ]);
    }

    #[Test]
    public function it_serves_a_guest_the_invoice_of_an_assigned_client(): void
    {
        /* Act */
        $response = $this->get('/guest/invoices/view/' . $this->ownInvoice);

        /* Assert: positive control, the page is the right invoice */
        self::assertSame(200, $response->statusCode());
        $this->assertResponseBodyContains($response, 'INV-OWN-VIEW');
    }

    #[Test]
    public function it_answers_404_for_an_invoice_of_another_client_and_leaks_nothing(): void
    {
        /* Act */
        $response = $this->get('/guest/invoices/view/' . $this->foreignInvoice);

        /* Assert */
        self::assertSame(404, $response->statusCode());
        $this->assertResponseBodyNotContains($response, 'INV-FOREIGN-VIEW');
        $this->assertResponseBodyNotContains($response, 'Foreign Client Ltd');
    }

    #[Test]
    public function it_does_not_mark_the_foreign_invoice_as_viewed(): void
    {
        /* Arrange */
        $before = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $this->foreignInvoice]);

        /* Act */
        $this->get('/guest/invoices/view/' . $this->foreignInvoice);

        /* Assert */
        $after = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $this->foreignInvoice]);
        self::assertSame($before['invoice_status_id'], $after['invoice_status_id']);
    }
}
