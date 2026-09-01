<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 — "Unable to delete invoice". Controller: Invoices::delete().
 *
 * With CSRF protection enabled (the production default), deleting a draft
 * invoice from the confirmation modal silently failed: the user was redirected
 * back to the invoice list and the invoice was left in place, with
 * "CSRF validation failed: Missing or invalid submitted token" in the log.
 *
 * Root cause: CodeIgniter's Security::csrf_verify() runs on every POST during
 * bootstrap. On a valid token it *removes* $_POST[csrf_token_name] (and, with
 * csrf_regenerate on, rotates the cookie). Invoices::delete() then called
 * ensure_valid_post_request() -> verify_csrf_token(), which re-read the now
 * empty $_POST token and rejected the request even though the framework had
 * already validated it.
 *
 * @see \Tests\Concerns\PerformsCsrfProtectedRequests
 */
#[Group('security')]
class Issue1694InvoiceDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_draft_invoice_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Issue 1694 Delete Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoices/delete/' . $invoiceId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Delete must redirect to the invoice list. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_rejects_the_delete_when_no_csrf_token_is_supplied(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Issue 1694 No-Token Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/delete/' . $invoiceId);

        /* Assert — framework CSRF check blocks the request before the controller runs. */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_rejects_the_delete_when_the_csrf_token_does_not_match_the_cookie(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Issue 1694 Bad-Token Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->post(
            '/invoices/delete/' . $invoiceId,
            ['_ip_csrf' => 'issue-1694-csrf-token-0123456789'],
            [],
            ['ip_csrf_cookie' => 'a-different-cookie-value']
        );

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId]);
    }
}
