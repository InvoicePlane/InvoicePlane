<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression net for the state-changing endpoints that have no CRUD
 * ControllerTest of their own — every one runs with CSRF protection ON (the
 * production default) and asserts the mutation actually happens with a valid
 * token and does NOT happen without one. Enforced by
 * .claude/skills/config-parity-guard.
 */
#[Group('security')]
class CsrfMutationParityTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    // -------------------------------------------------------------------------
    // import/delete  (Import::delete, ensure_valid_post_request)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_import_batch_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_imports', ['import_date' => date('Y-m-d H:i:s')]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_imports', ['import_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_an_import_batch_without_a_csrf_token(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_imports', ['import_date' => date('Y-m-d H:i:s')]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/import/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_imports', ['import_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // users/delete_user_client  (Users::delete_user_client, ensure_valid_post_request)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_unassigns_a_client_from_a_user_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $userId = $this->seedSecondaryUser();
        $ucId   = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $this->seedClient()]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete_user_client/' . $userId . '/' . $ucId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token unassign redirects.');
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $ucId]);
    }

    #[Test]
    public function it_does_not_unassign_a_client_from_a_user_without_a_csrf_token(): void
    {
        /* Arrange */
        $userId = $this->seedSecondaryUser();
        $ucId   = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $this->seedClient()]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/users/delete_user_client/' . $userId . '/' . $ucId);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $ucId]);
    }

    // -------------------------------------------------------------------------
    // payments/delete  (Payments::delete, ensure_valid_post_request)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_payment_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $id        = $this->seedPayment($invoiceId, ['payment_note' => 'parity-delete']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/payments/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_payment_without_a_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $id        = $this->seedPayment($invoiceId, ['payment_note' => 'parity-keep']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/payments/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // invoices/recalculate_all_invoices  (ensure_valid_post_request)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_recalculates_invoice_amounts_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $this->databaseUpdate('ip_invoice_amounts', ['invoice_total' => '999.99', 'invoice_balance' => '999.99'], ['invoice_id' => $invoiceId]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoices/recalculate_all_invoices');

        /* Assert */
        $this->assertDatabaseMissing('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_total' => '999.99']);
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_total' => '0.00']);
    }

    #[Test]
    public function it_does_not_recalculate_invoice_amounts_without_a_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $this->databaseUpdate('ip_invoice_amounts', ['invoice_total' => '888.88'], ['invoice_id' => $invoiceId]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/recalculate_all_invoices');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_total' => '888.88']);
    }

    // -------------------------------------------------------------------------
    // quotes/recalculate_all_quotes  (ensure_valid_post_request)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_recalculates_quote_amounts_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $quoteId = (int) $this->seedModel('Quote', ['client_id' => $this->seedClient()])->quote_id;
        $this->databaseInsert('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '777.77']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/quotes/recalculate_all_quotes');

        /* Assert */
        $this->assertDatabaseMissing('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '777.77']);
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '0.00']);
    }

    #[Test]
    public function it_does_not_recalculate_quote_amounts_without_a_csrf_token(): void
    {
        /* Arrange */
        $quoteId = (int) $this->seedModel('Quote', ['client_id' => $this->seedClient()])->quote_id;
        $this->databaseInsert('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '666.66']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/quotes/recalculate_all_quotes');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $this->assertDatabaseHas('ip_quote_amounts', ['quote_id' => $quoteId, 'quote_total' => '666.66']);
    }

    // -------------------------------------------------------------------------
    // sessions/passwordreset  (Sessions::passwordreset, verify_csrf_token)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_changes_a_password_via_reset_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $token = 'parity-reset-token-' . bin2hex(random_bytes(6));
        $id    = $this->seedSecondaryUser();
        $this->databaseUpdate('ip_users', [
            'user_passwordreset_token'        => $token,
            'user_passwordreset_token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ], ['user_id' => $id]);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'];

        /* Act */
        $response = $this->postWithValidCsrfToken('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => (string) $id,
            'token'            => $token,
            'new_password'     => 'BrandNewPass123',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A completed reset redirects to login.');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'];
        self::assertNotSame($before, $after, 'The password hash must change.');
    }

    #[Test]
    public function it_does_not_change_a_password_via_reset_without_a_csrf_token(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $token = 'parity-reset-token-' . bin2hex(random_bytes(6));
        $id    = $this->seedSecondaryUser();
        $this->databaseUpdate('ip_users', [
            'user_passwordreset_token'        => $token,
            'user_passwordreset_token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ], ['user_id' => $id]);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'];

        /* Act */
        $response = $this->postWithoutCsrfToken('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => (string) $id,
            'token'            => $token,
            'new_password'     => 'BrandNewPass123',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'];
        self::assertSame($before, $after, 'The password hash must be untouched.');
    }

    private function seedSecondaryUser(): int
    {
        // Delegate to the shared seedModel() row-builder instead of
        // duplicating its ip_users defaults here; only the type differs.
        return (int) $this->seedModel('User', ['user_type' => 2])->user_id;
    }
}
