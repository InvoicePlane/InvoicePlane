<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Reported on v1.7.2 with CSRF_PROTECTION=true and CSRF_REGENERATE=false: filter/ajax/*, invoices/ajax/*,
 * quotes/ajax/* and the delete actions returned 403 although the templates carry the token.
 *
 * Every other CSRF test in the suite either disables protection or covers one route. This one drives the
 * routes the report names through the real CodeIgniter token check with a matching token and cookie, so a
 * 403 here means the server rejected a request that carried a valid token.
 */
#[Group('security')]
final class CsrfEnabledAjaxAndDeleteTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfProtection();
        $this->withEnvironment(['CSRF_REGENERATE' => 'false']);
        $this->actingAsAdmin();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function filterEndpoints(): array
    {
        return [
            'invoices' => ['filter/ajax/filter_invoices'],
            'quotes'   => ['filter/ajax/filter_quotes'],
            'clients'  => ['filter/ajax/filter_clients'],
        ];
    }

    #[Test]
    #[DataProvider('filterEndpoints')]
    public function it_accepts_a_filter_ajax_post_that_carries_a_valid_csrf_token(string $uri): void
    {
        /* Act */
        $response = $this->ajaxPostWithValidCsrfToken($uri, ['filter_query' => 'a']);

        /* Assert */
        self::assertNotSame(403, $response->statusCode(), "POST {$uri} with a valid CSRF token must not be rejected.");
    }

    #[Test]
    public function it_deletes_a_draft_invoice_on_a_post_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->postWithValidCsrfToken("invoices/delete/{$invoiceId}");

        /* Assert */
        self::assertNotSame(403, $response->statusCode(), 'A delete POST with a valid CSRF token must not be rejected.');
        self::assertNull($this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]), 'The draft invoice must be gone.');
    }

    #[Test]
    public function it_rejects_a_delete_post_without_a_csrf_token_and_keeps_the_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->postWithoutCsrfToken("invoices/delete/{$invoiceId}");

        /* Assert */
        self::assertSame(403, $response->statusCode());
        self::assertNotNull($this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]));
    }

    #[Test]
    public function it_accepts_a_json_ajax_post_with_a_valid_token_when_the_token_regenerates(): void
    {
        /* Arrange: with regeneration on, json_encode_ajax() echoes the next token back to the client */
        $this->withEnvironment(['CSRF_REGENERATE' => 'true']);

        /* Act: payments/ajax/add answers through Base_Controller::json_encode_ajax() */
        $response = $this->ajaxPostWithValidCsrfToken('payments/ajax/add', ['invoice_id' => '0']);

        /* Assert */
        self::assertNotSame(403, $response->statusCode(), 'json_encode_ajax() must not re-run the CSRF check the bootstrap already passed.');
        self::assertStringContainsString('_ip_csrf', $response->body(), 'The JSON answer must echo the next token (status ' . $response->statusCode() . ').');
    }
}
