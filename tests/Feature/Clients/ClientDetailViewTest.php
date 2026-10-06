<?php

namespace Tests\Feature\Clients;

use Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * clients/view (the client detail page) plus the branches of clients/form that the basic CRUD
 * tests do not reach.
 */
#[CoversClass(Clients::class)]
final class ClientDetailViewTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -- detail page ---------------------------------------------------------

    #[Test]
    public function it_shows_the_clients_billing_totals(): void
    {
        /* Arrange: billed 121 + 50 = 171, paid 21, outstanding 100 + 50 = 150 */
        $clientId = $this->seedClient(['client_name' => 'Totals Client']);
        $this->seedInvoice($clientId, [], ['invoice_total' => '121.00', 'invoice_paid' => '21.00', 'invoice_balance' => '100.00']);
        $this->seedInvoice($clientId, [], ['invoice_total' => '50.00', 'invoice_paid' => '0.00', 'invoice_balance' => '50.00']);
        $this->seedInvoice($this->seedClient(['client_name' => 'Someone Else']), [], ['invoice_total' => '9999.00', 'invoice_paid' => '0.00', 'invoice_balance' => '9999.00']);

        /* Act */
        $response = $this->get('/clients/view/' . $clientId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Totals Client');
        self::assertMatchesRegularExpression('/td-amount">\s*\$171\s*</', $response->body(), 'total billed');
        self::assertMatchesRegularExpression('/td-amount">\s*\$21\s*</', $response->body(), 'total paid');
        self::assertMatchesRegularExpression('/td-amount">\s*\$150\s*</', $response->body(), 'outstanding balance');
        $this->assertResponseBodyNotContains($response, '9999');
    }

    #[Test]
    public function it_lists_only_this_clients_invoices_quotes_and_payments_and_notes(): void
    {
        /* Arrange */
        $mine      = $this->seedClient(['client_name' => 'Mine Client']);
        $other     = $this->seedClient(['client_name' => 'Other Client']);
        $myInvoice = $this->seedInvoice($mine, ['invoice_number' => 'MINE-INV-1']);
        $this->seedInvoice($other, ['invoice_number' => 'OTHER-INV-1']);
        $this->seedPayment($myInvoice, ['payment_note' => 'MINE-PAYMENT-NOTE']);
        $this->seedPayment($this->seedInvoice($other, ['invoice_number' => 'OTHER-INV-2']), ['payment_note' => 'OTHER-PAYMENT-NOTE']);
        $this->seedClientQuote($mine, 'MINE-QUOTE-1');
        $this->seedClientQuote($other, 'OTHER-QUOTE-1');
        $this->databaseInsert('ip_client_notes', ['client_id' => $mine, 'client_note_date' => date('Y-m-d'), 'client_note' => 'MINE-NOTE-TEXT']);
        $this->databaseInsert('ip_client_notes', ['client_id' => $other, 'client_note_date' => date('Y-m-d'), 'client_note' => 'OTHER-NOTE-TEXT']);

        /* Act */
        $response = $this->get('/clients/view/' . $mine);

        /* Assert */
        foreach (['MINE-INV-1', 'MINE-PAYMENT-NOTE', 'MINE-QUOTE-1'] as $own) {
            $this->assertResponseBodyContains($response, $own);
        }
        foreach (['OTHER-INV-1', 'OTHER-INV-2', 'OTHER-PAYMENT-NOTE', 'OTHER-QUOTE-1', 'OTHER-NOTE-TEXT'] as $foreign) {
            $this->assertResponseBodyNotContains($response, $foreign);
        }
    }

    #[Test]
    public function it_paginates_a_clients_invoices_from_the_tab_url(): void
    {
        /* Arrange: 5 invoices, 2 per page */
        $this->setSetting('default_list_limit', '2');
        $clientId = $this->seedClient();
        foreach (range(1, 5) as $n) {
            $this->seedInvoice($clientId, ['invoice_number' => 'PAGE-INV-' . $n]);
        }

        /* Act */
        $first  = $this->get('/clients/view/' . $clientId . '/invoices/0');
        $second = $this->get('/clients/view/' . $clientId . '/invoices/2');
        $third  = $this->get('/clients/view/' . $clientId . '/invoices/4');

        /* Assert: 2 + 2 + 1 distinct invoices, no overlap, none missing */
        $seen = [];
        foreach ([$first, $second, $third] as $page) {
            preg_match_all('/PAGE-INV-\d/', $page->body(), $m);
            $seen[] = array_values(array_unique($m[0]));
        }
        self::assertSame([2, 2, 1], array_map('count', $seen));
        self::assertCount(5, array_unique(array_merge(...$seen)));
    }

    #[Test]
    public function it_returns_404_for_an_unknown_client(): void
    {
        /* Act */
        $response = $this->get('/clients/view/999999');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    // -- form branches -------------------------------------------------------

    #[Test]
    public function it_returns_to_the_list_when_the_client_form_is_cancelled(): void
    {
        /* Act */
        $response = $this->post('/clients/form', ['btn_cancel' => '1', 'client_name' => 'Never Saved']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'clients');
        $this->assertDatabaseMissing('ip_clients', ['client_name' => 'Never Saved']);
    }

    #[Test]
    public function it_returns_404_when_editing_an_unknown_client(): void
    {
        /* Act */
        $response = $this->get('/clients/form/999999');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_stores_a_custom_title_instead_of_the_custom_marker(): void
    {
        /* Act */
        $this->post('/clients/form', [
            'client_name' => 'Titled Client', 'client_title' => 'custom', 'client_title_custom' => 'Dr. Prof.',
            'is_update'   => '0', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Titled Client', 'client_title' => 'Dr. Prof.']);
    }

    #[Test]
    public function it_clears_the_einvoicing_version_when_einvoicing_is_switched_off(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'E-Invoicing Client', 'client_einvoicing_version' => 'Facturxv10']);

        /* Act */
        $this->post('/clients/form/' . $clientId, [
            'client_name' => 'E-Invoicing Client', 'client_start_einvoicing' => '0', 'client_einvoicing_version' => 'Facturxv10',
            'is_update'   => '1', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_clients', ['client_id' => $clientId, 'client_einvoicing_version' => '']);
    }

    #[Test]
    public function it_gives_users_with_all_clients_access_to_a_newly_created_client(): void
    {
        /* Arrange: one "all clients" user, one restricted user */
        $allClients = $this->seedGuest('all-clients@test.local', 1);
        $restricted = $this->seedGuest('restricted@test.local', 0);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/clients/form', ['client_name' => 'Auto Assigned', 'client_active' => '1', 'is_update' => '0', 'btn_submit' => '1']);

        /* Assert */
        $newClient = $this->databaseFetchOne('ip_clients', ['client_name' => 'Auto Assigned']);
        self::assertNotNull($newClient);
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $allClients, 'client_id' => $newClient['client_id']]);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $restricted, 'client_id' => $newClient['client_id']]);
    }

    #[Test]
    public function it_does_not_hand_an_inactive_new_client_to_all_clients_users(): void
    {
        /* Arrange */
        $allClients = $this->seedGuest('all-clients@test.local', 1);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/clients/form', ['client_name' => 'Dormant Client', 'client_active' => '0', 'is_update' => '0', 'btn_submit' => '1']);

        /* Assert */
        $dormant = $this->databaseFetchOne('ip_clients', ['client_name' => 'Dormant Client']);
        self::assertNotNull($dormant);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $allClients, 'client_id' => $dormant['client_id']]);
    }

    #[Test]
    public function it_does_not_reassign_users_when_an_existing_client_is_updated(): void
    {
        /* Arrange */
        $allClients = $this->seedGuest('all-clients@test.local', 1);
        $clientId   = $this->seedClient(['client_name' => 'Already There']);

        /* Act */
        $this->post('/clients/form/' . $clientId, ['client_name' => 'Already There', 'is_update' => '1', 'btn_submit' => '1']);

        /* Assert */
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $allClients, 'client_id' => $clientId]);
    }

    // -- helpers -------------------------------------------------------------

    private function seedClientQuote(int $clientId, string $number): int
    {
        return $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_number'       => $number, 'quote_url_key' => bin2hex(random_bytes(16)),
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+15 days')),
        ]) ?: 0;
    }

    private function seedGuest(string $email, int $allClients): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'         => 2, 'user_name' => $email, 'user_email' => $email, 'user_all_clients' => $allClients,
            'user_password'     => password_hash('secret123', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(8)),
            'user_language'     => 'system', 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
