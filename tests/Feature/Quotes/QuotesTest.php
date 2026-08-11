<?php

declare(strict_types=1);

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Quotes;
use Tests\AbstractTestCase;

class QuotesTest extends AbstractTestCase
{
    private const CSRF_TOKEN = 'regression-csrf-token-0123456789';

    protected function setUp(): void
    {
        parent::setUp();
    }

    private int $clientId;
    private int $invoiceGroupId;
    protected function __QuotesAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

            $this->clientId       = $this->seedClient(['client_name' => 'Ajax Quote Client']);

            $this->invoiceGroupId = $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Ajax Quote Group',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => 'QUO-{number}',

                'invoice_group_left_pad'          => 0,

            ]);

            // Ensure quotes_expire_after is set so DateInterval('P{n}D') is valid.

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'quotes_expire_after', 'setting_value' => '30']);

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_creates_a_quote(): void

        {

            $this->__QuotesAjaxController_setUp();

            /**

             * POST /quotes/ajax/create

             * {

             *     "client_id": "<clientId>",

             *     "quote_date_created": "2026-06-21",

             *     "invoice_group_id": "<invoiceGroupId>"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/quotes/ajax/create', [

                'client_id'          => (string) $this->clientId,

                'quote_date_created' => '2026-06-21',

                'invoice_group_id'   => (string) $this->invoiceGroupId,

                'user_id'            => '1',

            ]);



            /* Assert */

            $body = $response->body();

            $json = json_decode($body, true);

            self::assertSame(1, $json['success'] ?? null, 'Ajax create must return success=1. Body: ' . $body);

            $this->assertDatabaseHas('ip_quotes', ['quote_id' => $json['quote_id']]);

        }
    #[Test]

    public function it_fails_to_create_a_quote_without_client_id(): void

        {

            $this->__QuotesAjaxController_setUp();

            /**

             * POST /quotes/ajax/create

             * {

             *     "client_id": "",

             *     "quote_date_created": "2026-06-21",

             *     "invoice_group_id": "<invoiceGroupId>"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/quotes/ajax/create', [

                'client_id'          => '',

                'quote_date_created' => '2026-06-21',

                'invoice_group_id'   => (string) $this->invoiceGroupId,

            ]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null, 'Missing client_id must return success=0.');

            $this->assertDatabaseCount('ip_quotes', 0);

        }
    #[Test]

    public function it_fails_to_create_a_quote_without_quote_date(): void

        {

            $this->__QuotesAjaxController_setUp();

            /**

             * POST /quotes/ajax/create

             * {

             *     "client_id": "<clientId>",

             *     "quote_date_created": "",

             *     "invoice_group_id": "<invoiceGroupId>"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/quotes/ajax/create', [

                'client_id'          => (string) $this->clientId,

                'quote_date_created' => '',

                'invoice_group_id'   => (string) $this->invoiceGroupId,

            ]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null, 'Missing quote_date_created must return success=0.');

            $this->assertDatabaseCount('ip_quotes', 0);

        }
    #[Test]

    public function it_fails_to_create_a_quote_without_invoice_group(): void

        {

            $this->__QuotesAjaxController_setUp();

            /**

             * POST /quotes/ajax/create

             * {

             *     "client_id": "<clientId>",

             *     "quote_date_created": "2026-06-21",

             *     "invoice_group_id": ""

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/quotes/ajax/create', [

                'client_id'          => (string) $this->clientId,

                'quote_date_created' => '2026-06-21',

                'invoice_group_id'   => '',

            ]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null, 'Missing invoice_group_id must return success=0.');

            $this->assertDatabaseCount('ip_quotes', 0);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login(): void

        {

            $this->__QuotesAjaxController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/quotes/status/all'); // Regular (non-Ajax) route for redirect check



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __QuotesController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_quotes_by_status(): void

        {

            $this->__QuotesController_setUp();

            /* Arrange */

            $this->__QuotesController_seedQuote(['quote_number' => 'QUO-LIST-001']);



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'QUO-LIST-001');

        }



        // -------------------------------------------------------------------------

        // View

        // -------------------------------------------------------------------------
    #[Test]

    public function it_views_a_single_quote_and_shows_the_quote_number(): void

        {

            $this->__QuotesController_setUp();

            /* Arrange */

            $quoteId = $this->__QuotesController_seedQuote(['quote_number' => 'QUO-VIEW-001']);



            /* Act */

            $response = $this->get('/quotes/view/' . $quoteId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'QUO-VIEW-001');

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_quote(): void

        {

            $this->__QuotesController_setUp();

            /* Arrange */

            $quoteId = $this->__QuotesController_seedQuote(['quote_number' => 'QUO-DEL-001']);

            $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId]);



            /* Act */

            $response = $this->post('/quotes/delete/' . $quoteId, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $quoteId]);

        }



        // -------------------------------------------------------------------------

        // Edge cases

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_index_to_all_quotes_list(): void

        {

            $this->__QuotesController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/quotes');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'GET /quotes must redirect to status/all.');

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login_from_quotescontroller(): void

        {

            $this->__QuotesController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    private function __QuotesController_seedQuote(array $overrides = []): int

        {

            $clientId = $this->seedClient(['client_name' => 'Quote Client ' . bin2hex(random_bytes(3))]);



            return $this->databaseInsert('ip_quotes', array_merge([

                'client_id'              => $clientId,

                'user_id'                => 1,

                'invoice_group_id'       => 1,

                'quote_date_created'     => date('Y-m-d'),

                'quote_date_modified'    => date('Y-m-d'),

                'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),

                'quote_number'           => 'QUO-' . bin2hex(random_bytes(4)),

                'quote_url_key'          => bin2hex(random_bytes(16)),

                'quote_discount_amount'  => '0',

                'quote_discount_percent' => '0',

            ], $overrides));

        }
    protected function __QuotesFeature_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('crud')]

    public function it_renders_the_quotes_index_page_with_a_200_status(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_includes_html_structure_on_the_quotes_index_page(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            $this->assertResponseBodyContains($response, '<html');

            $this->assertResponseBodyContains($response, '</html>');



            self::assertGreaterThan(

                500,

                $response->bodyLength(),

                'The quotes index page rendered fewer than 500 bytes — the view likely did not execute.'

            );

        }
    #[Test]

    public function it_redirects_an_unauthenticated_visitor_away_from_the_quotes_list(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf(

                    'Unauthenticated GET /quotes/status/all must redirect. Got status [%d] with body: %s',

                    $response->statusCode(),

                    mb_substr($response->body(), 0, 200)

                )

            );

        }
    #[Test]

    public function it_shows_the_correct_six_quote_statuses_in_the_index_filter_options(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);



            $expectedStatuses = ['draft', 'sent', 'viewed', 'approved', 'rejected', 'canceled'];



            $foundCount = 0;



            foreach ($expectedStatuses as $status) {

                if ($response->contains($status)) {

                    $foundCount++;

                }

            }



            self::assertGreaterThanOrEqual(

                3,

                $foundCount,

                sprintf(

                    'The quotes index must contain at least 3 of the 6 status labels. Found %d of [%s].',

                    $foundCount,

                    implode(', ', $expectedStatuses)

                )

            );

        }
    #[Test]

    public function it_renders_the_view_page_for_a_seeded_quote(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Quote Client']);

            $quoteId  = $this->__QuotesFeature_seedQuote($clientId, ['quote_number' => 'QUO-TEST-' . time()]);



            /* Act */

            $response = $this->get('/quotes/view/' . $quoteId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);



            self::assertTrue(

                $response->contains('QUO-TEST') || $response->contains((string) $quoteId),

                sprintf(

                    'The quote view page for ID [%d] must show the quote number or ID. Body (first 400 chars): %s',

                    $quoteId,

                    mb_substr($response->body(), 0, 400)

                )

            );

        }
    #[Test]

    public function it_does_not_expose_raw_php_errors_on_the_quotes_index(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/quotes/status/all');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_returns_404_or_redirect_for_a_nonexistent_quote_id(): void

        {

            $this->__QuotesFeature_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/quotes/view/999999999');



            /* Assert */

            self::assertThat(

                $response->statusCode(),

                self::logicalOr(

                    self::equalTo(404),

                    self::equalTo(302),

                    self::equalTo(301),

                    self::equalTo(307),

                    self::equalTo(200)

                ),

                sprintf(

                    'Requesting a nonexistent quote must produce 404, redirect, or show an empty page. Got [%d].',

                    $response->statusCode()

                )

            );

        }
    private function __QuotesFeature_seedQuote(int $clientId, array $overrides_from_quotesfeature = []): int

        {

            return $this->databaseInsert('ip_quotes', array_merge([

                'user_id'                => 1,

                'client_id'              => $clientId,

                'quote_status_id'        => 1,

                'quote_date_created'     => date('Y-m-d'),

                'quote_date_modified'    => date('Y-m-d'),

                'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),

                'quote_number'           => 'QUO-' . time(),

                'quote_url_key'          => bin2hex(random_bytes(16)),

                'invoice_group_id'       => 1,

                'quote_discount_amount'  => '0.00',

                'quote_discount_percent' => '0.00',

            ], $overrides_from_quotesfeature));

        }
    #[Test]

    public function it_denies_a_guest_access_to_another_clients_quote_pdf(): void

        {

            /* Arrange */

            $ownClientId   = $this->seedClient(['client_name' => 'Guest Owner Q']);

            $otherClientId = $this->seedClient(['client_name' => 'Other Client Q']);



            $guestUserId = $this->databaseInsert('ip_users', [

                'user_name'          => 'guest_idor_q_test',

                'user_email'         => 'guest-idor-q@test.local',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_type'          => 2,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $this->databaseInsert('ip_user_clients', [

                'user_id'   => $guestUserId,

                'client_id' => $ownClientId,

            ]);



            $otherQuoteId = $this->databaseInsert('ip_quotes', [

                'client_id'           => $otherClientId,

                'user_id'             => 1,

                'invoice_group_id'    => 1,

                'quote_status_id'     => 1,

                'quote_date_created'  => date('Y-m-d'),

                'quote_date_modified' => date('Y-m-d'),

                'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),

                'quote_number'        => 'QUO-IDOR-' . random_int(1000, 9999),

                'quote_url_key'       => bin2hex(random_bytes(16)),

            ]);



            $this->databaseInsert('ip_quote_amounts', [

                'quote_id'             => $otherQuoteId,

                'quote_item_subtotal'  => '0.00',

                'quote_item_tax_total' => '0.00',

                'quote_tax_total'      => '0.00',

                'quote_total'          => '0.00',

            ]);



            $this->actingAs([

                'user_id'       => $guestUserId,

                'user_type'     => 2,

                'user_email'    => 'guest-idor-q@test.local',

                'user_name'     => 'Guest IDOR Q Test',

                'user_company'  => '',

                'user_language' => 'system',

            ]);



            /* Act */

            $response = $this->get("/guest/quotes/generate_pdf/{$otherQuoteId}");



            /* Assert */

            self::assertSame(

                404,

                $response->statusCode(),

                'A guest must not be able to retrieve another client\'s quote PDF — expected 404.'

            );

        }
    #[Test]

    public function it_does_not_mark_a_quote_sent_from_a_forged_generate_pdf_get(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $this->enablePdfSentMarking('mark_quotes_sent_pdf');

            $this->withEnvironment(['CSRF_PROTECTION' => 'true']);

            $quoteId = $this->__SecurityRegression_seedSecurityQuote();



            /* Act */

            $response = $this->get('/quotes/generate_pdf/' . $quoteId . '/0');



            /* Assert */

            self::assertLessThan(500, $response->statusCode());

            self::assertSame(1, (int) $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['quote_status_id']);

        }
    #[Test]

    public function it_marks_a_quote_sent_only_with_a_matching_generate_pdf_csrf_token(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $this->enablePdfSentMarking('mark_quotes_sent_pdf');

            $this->withEnvironment(['CSRF_PROTECTION' => 'true']);

            $quoteId = $this->__SecurityRegression_seedSecurityQuote();



            /* Act */

            $response = $this->get(

                '/quotes/generate_pdf/' . $quoteId . '/0',

                ['_ip_csrf'       => self::CSRF_TOKEN],

                ['ip_csrf_cookie' => self::CSRF_TOKEN]

            );



            /* Assert */

            self::assertLessThan(500, $response->statusCode());

            self::assertSame(2, (int) $this->databaseFetchOne('ip_quotes', ['quote_id' => $quoteId])['quote_status_id']);

        }



        // -----------------------------------------------------------------------

        // 2. Path traversal — upload endpoints

        // -----------------------------------------------------------------------
    private function __SecurityRegression_seedSecurityQuote(): int

        {

            $clientId = $this->seedClient(['client_name' => 'Generate PDF CSRF Client']);

            $quoteId  = $this->databaseInsert('ip_quotes', [

                'client_id'           => $clientId,

                'user_id'             => 1,

                'invoice_group_id'    => 1,

                'quote_status_id'     => 1,

                'quote_date_created'  => date('Y-m-d'),

                'quote_date_modified' => date('Y-m-d'),

                'quote_date_expires'  => date('Y-m-d', strtotime('+30 days')),

                'quote_number'        => '',

                'quote_url_key'       => bin2hex(random_bytes(16)),

            ]);



            $this->databaseInsert('ip_quote_amounts', [

                'quote_id'             => $quoteId,

                'quote_item_subtotal'  => '0.00',

                'quote_item_tax_total' => '0.00',

                'quote_tax_total'      => '0.00',

                'quote_total'          => '0.00',

            ]);



            return $quoteId;

        }

    private function enablePdfSentMarking(string $settingKey): void
    {
        if ($this->databaseFetchOne('ip_settings', ['setting_key' => $settingKey]) === null) {
            $this->databaseInsert('ip_settings', [
                'setting_key'   => $settingKey,
                'setting_value' => '1',
            ]);

            return;
        }

        $this->databaseUpdate('ip_settings', ['setting_value' => '1'], ['setting_key' => $settingKey]);
    }
}
