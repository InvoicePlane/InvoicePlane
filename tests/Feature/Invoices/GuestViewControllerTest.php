<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/View.php — the public invoice/quote viewer (magic-link
 * pattern: possession of the unguessable url_key grants view access), plus
 * the session-scoped quote approve/reject actions (real IDOR surface: a
 * guest must only be able to approve/reject quotes for their own clients).
 */
class GuestViewControllerTest extends AbstractTestCase
{
    // -------------------------------------------------------------------------
    // invoice()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_an_empty_invoice_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/invoice/');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_an_unknown_invoice_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/invoice/does-not-exist');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_a_draft_invoice_key(): void
    {
        /* Arrange: draft (status 1) is never guest_visible() */
        $clientId = $this->seedClient();
        $urlKey   = 'draft-inv-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 1]);

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_renders_a_guest_visible_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'visible-inv-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2, 'payment_method' => 0]);

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_404_for_an_empty_quote_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/quote/');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_an_unknown_quote_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/quote/does-not-exist');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_a_draft_quote_key(): void
    {
        /* Arrange: draft (status 1) is never guest_visible() */
        $clientId = $this->seedClient();
        $urlKey   = 'draft-quo-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 1]);

        /* Act */
        $response = $this->get('/guest/view/quote/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_renders_a_guest_visible_quote(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'visible-quo-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

        /* Act */
        $response = $this->get('/guest/view/quote/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    // -------------------------------------------------------------------------
    // approve_quote() / reject_quote() — the real IDOR-sensitive surface
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_a_non_post_approve_quote_request(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'get-approve-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuestUser($clientId);

        /* Act */
        $response = $this->get('/guest/view/approve_quote/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
    }

    #[Test]
    public function it_denies_approve_quote_for_an_unauthenticated_guest(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'anon-approve-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuest();

        /* Act */
        $response = $this->post('/guest/view/approve_quote/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
    }

    #[Test]
    public function it_denies_approving_a_quote_belonging_to_a_different_client(): void
    {
        /* Arrange: guest is assigned to ownClient only */
        $ownClientId   = $this->seedClient(['client_name' => 'Own Client']);
        $otherClientId = $this->seedClient(['client_name' => 'Other Client']);
        $urlKey        = 'idor-approve-' . bin2hex(random_bytes(4));
        $this->seedQuote($otherClientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuestUser($ownClientId);

        /* Act */
        $response = $this->post('/guest/view/approve_quote/' . $urlKey);

        /* Assert: 404, not leaked as an authorization error, and never mutated */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
    }

    #[Test]
    public function it_approves_a_quote_for_its_own_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'own-approve-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuestUser($clientId);

        /* Act */
        $response = $this->post('/guest/view/approve_quote/' . $urlKey);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 4]);
    }

    #[Test]
    public function it_denies_rejecting_a_quote_belonging_to_a_different_client(): void
    {
        /* Arrange */
        $ownClientId   = $this->seedClient(['client_name' => 'Own Client 2']);
        $otherClientId = $this->seedClient(['client_name' => 'Other Client 2']);
        $urlKey        = 'idor-reject-' . bin2hex(random_bytes(4));
        $this->seedQuote($otherClientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuestUser($ownClientId);

        /* Act */
        $response = $this->post('/guest/view/reject_quote/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
    }

    #[Test]
    public function it_rejects_a_quote_for_its_own_client(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'own-reject-' . bin2hex(random_bytes(4));
        $this->seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);
        $this->actingAsGuestUser($clientId);

        /* Act */
        $response = $this->post('/guest/view/reject_quote/' . $urlKey);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_quotes', ['quote_url_key' => $urlKey, 'quote_status_id' => 5]);
    }

    // -------------------------------------------------------------------------
    // PDF generation guard clauses
    // -------------------------------------------------------------------------

    #[Test]
    public function it_silently_produces_no_invoice_pdf_for_an_unknown_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/generate_invoice_pdf/does-not-exist');

        /* Assert: no matching invoice means the method falls through with no output, no crash */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_404_for_sumex_pdf_when_the_invoice_has_no_sumex_id(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'no-sumex-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2]);

        /* Act */
        $response = $this->get('/guest/view/generate_sumex_pdf/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_quote_pdf_on_an_unknown_key(): void
    {
        /* Act */
        $response = $this->get('/guest/view/generate_quote_pdf/does-not-exist');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    private function actingAsGuestUser(int $clientId): int
    {
        $guestUserId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Guest View Test',
            'user_email'         => 'guest-view-' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $guestUserId, 'client_id' => $clientId]);
        $this->actingAs([
            'user_id'   => $guestUserId, 'user_type' => 2, 'user_email' => 'guest-view@test.local',
            'user_name' => 'Guest View Test', 'user_company' => '', 'user_language' => 'system',
        ]);

        return $guestUserId;
    }

    // -------------------------------------------------------------------------
    // quote()
    // -------------------------------------------------------------------------

    private function seedQuote(int $clientId, array $overrides = []): int
    {
        return $this->databaseInsert('ip_quotes', array_merge([
            'user_id'             => 1,
            'client_id'           => $clientId,
            'invoice_group_id'    => 1,
            'quote_status_id'     => 2,
            'quote_date_created'  => date('Y-m-d'),
            'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),
            'quote_number'        => 'QUO-' . time() . '-' . random_int(100, 999),
            'quote_url_key'       => bin2hex(random_bytes(16)),
        ], $overrides));
    }
}
