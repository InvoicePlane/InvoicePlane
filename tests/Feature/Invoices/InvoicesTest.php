<?php

declare(strict_types=1);

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\AbstractTestCase;

class InvoicesTest extends AbstractTestCase
{
    private const CSRF_TOKEN = 'regression-csrf-token-0123456789';

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (isset($this->this)) {
            $this->__GuestGetController_tearDown();
        }
        parent::tearDown();
    }

    protected function __CronController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect(): void

        {

            $this->__CronController_setUp();

            /* Arrange */

            /* (authenticated admin via __CronController_setUp) */



            /* Act */

            $response = $this->get('/invoices/status/all');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login(): void

        {

            $this->__CronController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_returns_500_for_a_wrong_cron_key(): void

        {

            /* Arrange: show_error(..., 500) is outside request.php's 400-499

             * "treat as HTTP response" range, so it surfaces as an exception. */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);

            $seeded = $this->__CronRecurController_seedRecurringInvoice();



            /* Act */

            try {

                $this->get('/invoices/cron/recur/wrong-key');

                self::fail('Expected a 500 for a wrong cron key.');

            } catch (RuntimeException $exception) {

                /* Assert */

                self::assertStringContainsString('Wrong cron key provided', $exception->getMessage());

            }



            $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);

        }
    #[Test]

    public function it_returns_500_for_a_missing_cron_key(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);



            /* Act */

            try {

                $this->get('/invoices/cron/recur');

                self::fail('Expected a 500 for a missing cron key.');

            } catch (RuntimeException $exception) {

                /* Assert */

                self::assertStringContainsString('Wrong cron key provided', $exception->getMessage());

            }

        }
    #[Test]

    public function it_generates_a_due_recurring_invoice_with_the_correct_cron_key(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);

            $seeded = $this->__CronRecurController_seedRecurringInvoice();



            /* Act */

            $response = $this->get('/invoices/cron/recur/the-real-key');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            $this->assertDatabaseCount('ip_invoices', 2, ['client_id' => $seeded['clientId']]);

            $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $seeded['recurringId'], 'recur_next_date' => date('Y-m-d', strtotime('-1 day'))]);

        }
    #[Test]

    public function it_does_not_generate_an_invoice_for_a_not_yet_due_recurring_invoice(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);

            $seeded = $this->__CronRecurController_seedRecurringInvoice(['recur_next_date' => date('Y-m-d', strtotime('+10 days'))]);



            /* Act */

            $response = $this->get('/invoices/cron/recur/the-real-key');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);

        }
    #[Test]

    public function it_does_not_generate_an_invoice_for_an_expired_recurring_series(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'the-real-key']);

            $seeded = $this->__CronRecurController_seedRecurringInvoice(['recur_end_date' => date('Y-m-d', strtotime('-1 day'))]);



            /* Act */

            $response = $this->get('/invoices/cron/recur/the-real-key');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertDatabaseCount('ip_invoices', 1, ['client_id' => $seeded['clientId']]);

        }
    private function __CronRecurController_seedRecurringInvoice(array $overrides = []): array

        {

            $clientId    = $this->seedClient();

            $invoiceId   = $this->seedInvoice($clientId, ['invoice_number' => 'CRON-SRC-' . bin2hex(random_bytes(4))]);

            $recurringId = $this->databaseInsert('ip_invoices_recurring', array_merge([

                'invoice_id'       => $invoiceId,

                'recur_start_date' => date('Y-m-d', strtotime('-1 day')),

                'recur_end_date'   => null,

                'recur_frequency'  => '1M',

                'recur_next_date'  => date('Y-m-d', strtotime('-1 day')),

            ], $overrides));



            return ['clientId' => $clientId, 'invoiceId' => $invoiceId, 'recurringId' => $recurringId];

        }
    private string $uploadDir;
    protected function __GuestGetController_setUp(): void

        {



            $this->uploadDir = dirname(__DIR__, 3) . '/uploads/customer_files';

            if ( ! is_dir($this->uploadDir)) {

                mkdir($this->uploadDir, 0777, true);

            }

        }
    protected function __GuestGetController_tearDown(): void

        {

            foreach (glob($this->uploadDir . '/*_guesttest*') ?: [] as $file) {

                if (is_file($file)) {

                    unlink($file);

                }

            }





        }



        // -------------------------------------------------------------------------

        // show_files

        // -------------------------------------------------------------------------
    #[Test]

    public function it_returns_an_empty_response_for_show_files_with_no_key(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/get/show_files');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame('{}', trim($response->body()));

        }
    #[Test]

    public function it_returns_an_empty_response_for_show_files_on_a_draft_invoice(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange: draft (status 1) is never guest_visible() */

            $urlKey = $this->__GuestGetController_seedVisibleInvoiceUrlKey(1);



            /* Act */

            $response = $this->get('/guest/get/show_files/' . $urlKey);



            /* Assert */

            self::assertSame('{}', trim($response->body()));

        }
    #[Test]

    public function it_returns_an_empty_response_for_show_files_with_no_uploads(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $urlKey = $this->__GuestGetController_seedVisibleInvoiceUrlKey();



            /* Act */

            $response = $this->get('/guest/get/show_files/' . $urlKey);



            /* Assert */

            self::assertSame('{}', trim($response->body()));

        }
    #[Test]

    public function it_lists_uploaded_files_for_a_guest_visible_invoice(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $urlKey   = bin2hex(random_bytes(16));

            $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2]);

            $this->databaseInsert('ip_uploads', [

                'client_id'          => $clientId,

                'url_key'            => $urlKey,

                'file_name_original' => 'attachment.pdf',

                'file_name_new'      => $urlKey . '_guesttest_attachment.pdf',

                'uploaded_date'      => date('Y-m-d'),

            ]);

            file_put_contents($this->uploadDir . '/' . $urlKey . '_guesttest_attachment.pdf', 'attachment-bytes');



            /* Act */

            $response = $this->get('/guest/get/show_files/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'attachment.pdf');

        }



        // -------------------------------------------------------------------------

        // get_file / attachment

        // -------------------------------------------------------------------------
    #[Test]

    public function it_returns_400_for_get_file_with_no_filename(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/get/get_file');



            /* Assert */

            $this->assertResponseStatusCode($response, 400);

        }
    #[Test]

    public function it_returns_404_for_get_file_with_a_malformed_url_key_prefix(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/get/get_file/not-a-valid-key_file.pdf');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_get_file_whose_url_key_is_not_guest_visible(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange: well-formed 32-char key, but no invoice/quote owns it */

            $fakeKey = bin2hex(random_bytes(16));



            /* Act */

            $response = $this->get('/guest/get/get_file/' . $fakeKey . '_file.pdf');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_get_file_whose_url_key_belongs_to_a_draft_invoice(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $urlKey = $this->__GuestGetController_seedVisibleInvoiceUrlKey(1);



            /* Act */

            $response = $this->get('/guest/get/get_file/' . $urlKey . '_file.pdf');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_a_visible_invoice_whose_file_does_not_exist_on_disk(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange: valid, guest-visible invoice, but no file was ever written */

            $urlKey = $this->__GuestGetController_seedVisibleInvoiceUrlKey();



            /* Act */

            $response = $this->get('/guest/get/get_file/' . $urlKey . '_missing.pdf');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_downloads_an_existing_file_for_a_guest_visible_invoice(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $urlKey   = $this->__GuestGetController_seedVisibleInvoiceUrlKey();

            $filename = $urlKey . '_guesttest.pdf';

            file_put_contents($this->uploadDir . '/' . $filename, 'pdf-bytes');



            /* Act */

            $response = $this->get('/guest/get/get_file/' . $filename);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame('pdf-bytes', $response->body());

        }
    #[Test]

    public function it_rejects_a_path_traversal_attempt_in_the_filename(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $urlKey = $this->__GuestGetController_seedVisibleInvoiceUrlKey();



            /* Act */

            $response = $this->get('/guest/get/get_file/' . rawurlencode($urlKey . '_../../../../etc/passwd'));



            /* Assert */

            self::assertNotSame(200, $response->statusCode());

        }
    #[Test]

    public function it_serves_attachment_route_the_same_as_get_file(): void

        {

            $this->__GuestGetController_setUp();

            /* Arrange */

            $urlKey   = $this->__GuestGetController_seedVisibleInvoiceUrlKey();

            $filename = $urlKey . '_guesttest2.pdf';

            file_put_contents($this->uploadDir . '/' . $filename, 'attachment-bytes');



            /* Act */

            $response = $this->get('/guest/get/attachment/' . $filename);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame('attachment-bytes', $response->body());

        }
    private function __GuestGetController_seedVisibleInvoiceUrlKey(int $statusId = 2): string

        {

            $clientId = $this->seedClient();

            $urlKey   = bin2hex(random_bytes(16)); // 32 hex chars

            $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => $statusId]);



            return $urlKey;

        }
    #[Test]

    public function it_returns_404_for_an_empty_invoice_key(): void

        {

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/view/invoice/');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_an_unknown_invoice_key(): void

        {

            /* Arrange */



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

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/view/quote/');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_an_unknown_quote_key(): void

        {

            /* Arrange */



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 1]);



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

            $this->__GuestViewController_actingAsGuestUser($clientId);



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

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

            $this->__GuestViewController_seedQuote($otherClientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

            $this->__GuestViewController_actingAsGuestUser($ownClientId);



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

            $this->__GuestViewController_actingAsGuestUser($clientId);



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

            $this->__GuestViewController_seedQuote($otherClientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

            $this->__GuestViewController_actingAsGuestUser($ownClientId);



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

            $this->__GuestViewController_seedQuote($clientId, ['quote_url_key' => $urlKey, 'quote_status_id' => 2]);

            $this->__GuestViewController_actingAsGuestUser($clientId);



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

            /* Arrange */



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

            /* Arrange */



            /* Act */

            $response = $this->get('/guest/view/generate_quote_pdf/does-not-exist');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    private function __GuestViewController_actingAsGuestUser(int $clientId): int

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
    private function __GuestViewController_seedQuote(int $clientId, array $overrides_from_guestviewcontroller = []): int

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

            ], $overrides_from_guestviewcontroller));

        }
    protected function __InvoiceDeletionValidationFeature_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_deletes_a_draft_invoice(): void

        {

            $this->__InvoiceDeletionValidationFeature_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Draft Invoice Delete Client']);

            $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);



            /* Act */

            $response = $this->post('/invoices/delete/' . $invoiceId, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);

        }
    #[Test]

    public function it_does_not_delete_a_sent_invoice_when_deletion_is_disabled(): void

        {

            $this->__InvoiceDeletionValidationFeature_setUp();

            /**

             * ENABLE_INVOICE_DELETION defaults to false (ipconfig.php.example), so

             * a non-draft invoice must survive a delete attempt.

             */



            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Sent Invoice Delete Client']);

            $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);



            /* Act */

            $response = $this->post('/invoices/delete/' . $invoiceId, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Blocked delete still redirects back to the invoice list.');

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_invoicedeletionvalidationfeature(): void

        {

            $this->__InvoiceDeletionValidationFeature_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __InvoiceGroupsController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_invoice_groups(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Listed Group',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_left_pad'          => 0,

            ]);



            /* Act */

            $response = $this->get('/invoice_groups');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Group');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_invoice_group_form(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/invoice_groups/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_an_invoice_group(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /**

             * POST /invoice_groups/form

             * {

             *     "invoice_group_name": "Yearly 2025",

             *     "invoice_group_identifier_format": "{number}",

             *     "invoice_group_next_id": "1",

             *     "invoice_group_left_pad": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/invoice_groups/form', [

                'invoice_group_name'              => 'Yearly 2025',

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_next_id'           => '1',

                'invoice_group_left_pad'          => '0',

                'btn_submit'                      => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful create must redirect.');

            $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Yearly 2025']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_invoice_group_name(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Editable Group',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_left_pad'          => 0,

            ]);



            /* Act */

            $response = $this->get('/invoice_groups/form/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'Editable Group');

        }
    #[Test]

    public function it_updates_an_invoice_group(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /**

             * POST /invoice_groups/form/{id}

             * {

             *     "invoice_group_name": "Renamed Group",

             *     "invoice_group_identifier_format": "{number}",

             *     "invoice_group_next_id": "5",

             *     "invoice_group_left_pad": "3",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Original Group',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_left_pad'          => 0,

            ]);



            /* Act */

            $response = $this->post('/invoice_groups/form/' . $id, [

                'invoice_group_name'              => 'Renamed Group',

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_next_id'           => '5',

                'invoice_group_left_pad'          => '3',

                'btn_submit'                      => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful update must redirect.');

            $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Renamed Group']);

            $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Original Group']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_an_invoice_group(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Deletable Group',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_left_pad'          => 0,

            ]);

            $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Deletable Group']);



            /* Act */

            $response = $this->post('/invoice_groups/delete/' . $id, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Deletable Group']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_invoice_group_name(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /**

             * POST /invoice_groups/form

             * {

             *     "invoice_group_name": "",

             *     "invoice_group_identifier_format": "{number}",

             *     "invoice_group_next_id": "1",

             *     "invoice_group_left_pad": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/invoice_groups/form', [

                'invoice_group_name'              => '',

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_next_id'           => '1',

                'invoice_group_left_pad'          => '0',

                'btn_submit'                      => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            // Baseline seeding always creates one default invoice group; a failed

            // create must not add a second one.

            $this->assertDatabaseCount('ip_invoice_groups', 1);

        }
    #[Test]

    public function it_fails_to_create_without_identifier_format(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /**

             * POST /invoice_groups/form

             * {

             *     "invoice_group_name": "Missing Format",

             *     "invoice_group_identifier_format": "",

             *     "invoice_group_next_id": "1",

             *     "invoice_group_left_pad": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/invoice_groups/form', [

                'invoice_group_name'              => 'Missing Format',

                'invoice_group_identifier_format' => '',

                'invoice_group_next_id'           => '1',

                'invoice_group_left_pad'          => '0',

                'btn_submit'                      => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Missing Format']);

        }
    #[Test]

    public function it_fails_to_update_without_invoice_group_name(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /**

             * POST /invoice_groups/form/{id}

             * {

             *     "invoice_group_name": "",

             *     "invoice_group_identifier_format": "{number}",

             *     "invoice_group_next_id": "1",

             *     "invoice_group_left_pad": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_invoice_groups', [

                'invoice_group_name'              => 'Will Not Change',

                'invoice_group_next_id'           => 1,

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_left_pad'          => 0,

            ]);



            /* Act */

            $response = $this->post('/invoice_groups/form/' . $id, [

                'invoice_group_name'              => '',

                'invoice_group_identifier_format' => '{number}',

                'invoice_group_next_id'           => '1',

                'invoice_group_left_pad'          => '0',

                'btn_submit'                      => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Will Not Change']);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login_from_invoicegroupscontroller(): void

        {

            $this->__InvoiceGroupsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoice_groups');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __InvoiceTaxRateService_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_invoicetaxrateservice(): void

        {

            $this->__InvoiceTaxRateService_setUp();

            /* Arrange */

            /* (authenticated admin via __InvoiceTaxRateService_setUp) */



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertThat(

                $response->statusCode(),

                self::logicalOr(

                    self::equalTo(200),

                    self::equalTo(301),

                    self::equalTo(302),

                    self::equalTo(303),

                    self::equalTo(307),

                    self::equalTo(308),

                ),

                sprintf('[GET /invoices] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors(): void

        {

            $this->__InvoiceTaxRateService_setUp();

            /* Arrange */

            /* (authenticated admin via __InvoiceTaxRateService_setUp) */



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_invoicetaxrateservice(): void

        {

            $this->__InvoiceTaxRateService_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __InvoicesAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_invoicesajaxcontroller(): void

        {

            $this->__InvoicesAjaxController_setUp();

            /* Arrange */

            /* (setup done in __InvoicesAjaxController_setUp) */



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertThat(

                $response->statusCode(),

                self::logicalOr(

                    self::equalTo(200),

                    self::equalTo(301),

                    self::equalTo(302),

                    self::equalTo(303),

                    self::equalTo(307),

                    self::equalTo(308),

                ),

                sprintf('[GET /invoices] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_from_invoicesajaxcontroller(): void

        {

            $this->__InvoicesAjaxController_setUp();

            /* Arrange */

            /* (setup done in __InvoicesAjaxController_setUp) */



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_invoicesajaxcontroller(): void

        {

            $this->__InvoicesAjaxController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __InvoicesAjaxModals_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_renders_the_copy_invoice_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_copy_invoice', [

                'invoice_id' => (string) $invoiceId,

                'client_id'  => (string) $clientId,

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_the_change_user_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_change_user', ['invoice_id' => (string) $invoiceId, 'user_id' => '1']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_the_change_client_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_change_client', ['invoice_id' => (string) $invoiceId, 'client_id' => (string) $clientId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_the_create_invoice_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId = $this->seedClient();



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_create_invoice', ['client_id' => (string) $clientId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_the_create_recurring_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_create_recurring', ['invoice_id' => (string) $invoiceId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_the_create_credit_modal(): void

        {

            $this->__InvoicesAjaxModals_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/modal_create_credit', ['invoice_id' => (string) $invoiceId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    protected function __InvoicesAjaxRequiredFields_setUp(): void

        {



            $this->actingAsAdmin();

            // A real install seeds this during the setup wizard (Mdl_setup::$default_settings);

            // Mdl_invoices::get_date_due() builds a DateInterval directly from it with no

            // fallback, so create()/copy_invoice()/create_credit() all need it present.

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);

        }
    #[Test]

    public function it_creates_an_invoice_with_all_required_fields(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $json['invoice_id'], 'client_id' => $clientId_from_invoicesajaxrequiredfields]);

        }
    #[Test]

    public function it_fails_to_create_an_invoice_without_client_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['client_id']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 0);

        }
    #[Test]

    public function it_fails_to_create_an_invoice_without_invoice_date_created(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['invoice_date_created']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 0);

        }
    #[Test]

    public function it_fails_to_create_an_invoice_without_invoice_group_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['invoice_group_id']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 0);

        }
    #[Test]

    public function it_fails_to_create_an_invoice_without_invoice_time_created(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['invoice_time_created']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 0);

        }
    #[Test]

    public function it_fails_to_create_an_invoice_without_user_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['user_id']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 0);

        }
    #[Test]

    public function it_saves_an_invoice_with_all_required_fields(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'SAVE-REQ-001']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/save', $this->__InvoicesAjaxRequiredFields_validSavePayload($invoiceId));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

        }
    #[Test]

    public function it_fails_to_save_an_invoice_without_invoice_date_due(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'SAVE-REQ-002', 'invoice_date_due' => date('Y-m-d', strtotime('+10 days'))]);

            $payload   = $this->__InvoicesAjaxRequiredFields_validSavePayload($invoiceId);

            unset($payload['invoice_date_due']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/save', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_date_due' => date('Y-m-d', strtotime('+10 days'))]);

        }
    #[Test]

    public function it_fails_to_save_an_invoice_without_invoice_date_created(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'SAVE-REQ-003']);

            $payload   = $this->__InvoicesAjaxRequiredFields_validSavePayload($invoiceId);

            unset($payload['invoice_date_created']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/save', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

        }
    #[Test]

    public function it_rejects_an_invoice_number_with_unsafe_characters(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'SAVE-REQ-004']);

            $payload   = $this->__InvoicesAjaxRequiredFields_validSavePayload($invoiceId);



            $payload['invoice_number'] = 'INV<script>alert(1)</script>';



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/save', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_number' => 'SAVE-REQ-004']);

        }



        // -------------------------------------------------------------------------

        // change_user() / change_client()

        // -------------------------------------------------------------------------
    #[Test]

    public function it_changes_the_invoices_user(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $newUserId = $this->databaseInsert('ip_users', [

                'user_name'     => 'New Owner', 'user_email' => 'new-owner@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/change_user', ['user_id' => (string) $newUserId, 'invoice_id' => (string) $invoiceId]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'user_id' => $newUserId]);

        }
    #[Test]

    public function it_fails_to_change_the_invoices_user_for_an_unknown_user_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['user_id' => 1]);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/change_user', ['user_id' => '999999', 'invoice_id' => (string) $invoiceId]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'user_id' => 1]);

        }
    #[Test]

    public function it_changes_the_invoices_client(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields    = $this->seedClient();

            $invoiceId   = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $newClientId = $this->seedClient(['client_name' => 'New Owner Client']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/change_client', ['client_id' => (string) $newClientId, 'invoice_id' => (string) $invoiceId]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'client_id' => $newClientId]);

        }
    #[Test]

    public function it_fails_to_change_the_invoices_client_for_an_unknown_client_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/change_client', ['client_id' => '999999', 'invoice_id' => (string) $invoiceId]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'client_id' => $clientId_from_invoicesajaxrequiredfields]);

        }



        // -------------------------------------------------------------------------

        // delete_item()

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_an_existing_invoice_item(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $itemId    = $this->databaseInsert('ip_invoice_items', [

                'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),

                'item_name'  => 'Deletable', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,

            ]);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => (string) $itemId]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null);

            $this->assertDatabaseMissing('ip_invoice_items', ['item_id' => $itemId]);

        }
    #[Test]

    public function it_does_not_delete_anything_for_a_nonexistent_item_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $itemId    = $this->databaseInsert('ip_invoice_items', [

                'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),

                'item_name'  => 'Untouched', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,

            ]);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/delete_item/' . $invoiceId, ['item_id' => '999999']);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseHas('ip_invoice_items', ['item_id' => $itemId]);

        }



        // -------------------------------------------------------------------------

        // save_invoice_tax_rate() — required: invoice_id, tax_rate_id, include_item_tax

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_save_an_invoice_tax_rate_without_invoice_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/save_invoice_tax_rate', ['tax_rate_id' => '1', 'include_item_tax' => '0']);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoice_tax_rates', 0);

        }



        // -------------------------------------------------------------------------

        // create_recurring() — required: invoice_id, recur_start_date, recur_frequency

        // -------------------------------------------------------------------------
    #[Test]

    public function it_creates_a_recurring_invoice_with_all_required_fields(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create_recurring', [

                'invoice_id'       => (string) $invoiceId,

                'recur_start_date' => date('Y-m-d'),

                'recur_frequency'  => '1D',

            ]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

            $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_id' => $invoiceId]);

        }
    #[Test]

    public function it_fails_to_create_a_recurring_invoice_without_recur_start_date(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create_recurring', [

                'invoice_id'      => (string) $invoiceId,

                'recur_frequency' => '1D',

            ]);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices_recurring', 0);

        }



        // -------------------------------------------------------------------------

        // copy_invoice() / create_credit() (use the default validation_rules())

        // -------------------------------------------------------------------------
    #[Test]

    public function it_copies_an_invoice(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $sourceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'COPY-SRC-001']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/copy_invoice', array_merge($this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields), [

                'invoice_id' => (string) $sourceId,

            ]));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

            self::assertNotSame($sourceId, $json['invoice_id']);

        }
    #[Test]

    public function it_fails_to_copy_an_invoice_without_client_id(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $sourceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $payload  = $this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields);

            unset($payload['client_id']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/copy_invoice', array_merge($payload, ['invoice_id' => (string) $sourceId]));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_invoices', 1);

        }
    #[Test]

    public function it_creates_a_credit_invoice(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields = $this->seedClient();

            $sourceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields, ['invoice_number' => 'CREDIT-SRC-001']);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/create_credit', array_merge($this->__InvoicesAjaxRequiredFields_validCreatePayload($clientId_from_invoicesajaxrequiredfields), [

                'invoice_id' => (string) $sourceId,

            ]));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $json['invoice_id'], 'creditinvoice_parent_id' => $sourceId]);

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $sourceId, 'is_read_only' => 1]);

        }



        // -------------------------------------------------------------------------

        // read-only helpers

        // -------------------------------------------------------------------------
    #[Test]

    public function it_gets_an_item(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */

            $clientId_from_invoicesajaxrequiredfields  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId_from_invoicesajaxrequiredfields);

            $itemId    = $this->databaseInsert('ip_invoice_items', [

                'invoice_id' => $invoiceId, 'item_tax_rate_id' => 0, 'item_date_added' => date('Y-m-d'),

                'item_name'  => 'Get Me', 'item_description' => '', 'item_quantity' => '1.00', 'item_price' => '10.00', 'item_order' => 0,

            ]);



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/get_item', ['item_id' => (string) $itemId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Get Me');

        }
    #[Test]

    public function it_gets_a_recur_start_date(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */



            /* Act */

            $response = $this->ajax('POST', '/invoices/ajax/get_recur_start_date', [

                'invoice_date'    => date('Y-m-d'),

                'recur_frequency' => '1D',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_requires_an_ajax_request(): void

        {

            $this->__InvoicesAjaxRequiredFields_setUp();

            /* Arrange */



            /* Act */

            $response = $this->post('/invoices/ajax/get_recur_start_date', []);



            /* Assert */

            self::assertSame('', $response->body());

        }



        // -------------------------------------------------------------------------

        // create() — required: client_id, invoice_date_created, invoice_time_created, invoice_group_id

        // -------------------------------------------------------------------------
    private function __InvoicesAjaxRequiredFields_validCreatePayload(int $clientId_from_invoicesajaxrequiredfields): array

        {

            return [

                'client_id'            => (string) $clientId_from_invoicesajaxrequiredfields,

                'invoice_date_created' => date('Y-m-d'),

                'invoice_time_created' => date('H:i:s'),

                'invoice_group_id'     => '1',

                'user_id'              => '1',

            ];

        }



        // -------------------------------------------------------------------------

        // save() — required: invoice_date_created, invoice_date_due, invoice_time_created

        // -------------------------------------------------------------------------
    private function __InvoicesAjaxRequiredFields_validSavePayload(int $invoiceId): array

        {

            return [

                'invoice_id'               => (string) $invoiceId,

                'invoice_date_created'     => date('Y-m-d'),

                'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),

                'invoice_time_created'     => date('H:i:s'),

                'invoice_status_id'        => '1',

                'invoice_discount_percent' => '0',

                'invoice_discount_amount'  => '0',

                'items'                    => '[]',

            ];

        }
    protected function __InvoicesController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_lists_invoices_for_authenticated_admin(): void

        {

            $this->__InvoicesController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Invoice List Client']);

            $this->seedInvoice($clientId, ['invoice_number' => 'INV-LIST-001']);



            /* Act */

            $response = $this->get('/invoices/status/all');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'INV-LIST-001');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_invoicescontroller(): void

        {

            $this->__InvoicesController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __RecurringController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_lists_recurring_invoices_for_authenticated_admin(): void

        {

            $this->__RecurringController_setUp();

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

    public function it_redirects_a_guest_to_login_from_recurringcontroller(): void

        {

            $this->__RecurringController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/invoices/recurring');



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertHtmlOmits($response, 'InvoicePlane');

        }
    #[Test]

    public function it_stops_a_recurring_invoice(): void

        {

            $this->__RecurringController_setUp();

            /* Arrange */

            $recurringId = $this->__RecurringController_seedRecurring();



            /* Act */

            $response = $this->post('/invoices/recurring/stop/' . $recurringId);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseRow('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId], [

                'invoice_recurring_id' => $recurringId,

                'recur_end_date'       => date('Y-m-d'),

            ]);

        }
    #[Test]

    public function it_does_not_stop_a_recurring_invoice_on_a_non_post_request(): void

        {

            $this->__RecurringController_setUp();

            /* Arrange */

            $recurringId = $this->__RecurringController_seedRecurring(['recur_next_date' => date('Y-m-d', strtotime('+1 month'))]);



            /* Act */

            $this->get('/invoices/recurring/stop/' . $recurringId);



            /* Assert */

            $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId, 'recur_next_date' => date('Y-m-d', strtotime('+1 month'))]);

        }
    #[Test]

    public function it_deletes_a_recurring_invoice(): void

        {

            $this->__RecurringController_setUp();

            /* Arrange */

            $recurringId = $this->__RecurringController_seedRecurring();



            /* Act */

            $response = $this->post('/invoices/recurring/delete/' . $recurringId);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);

        }
    #[Test]

    public function it_does_not_delete_a_recurring_invoice_on_a_non_post_request(): void

        {

            $this->__RecurringController_setUp();

            /* Arrange */

            $recurringId = $this->__RecurringController_seedRecurring();



            /* Act */

            $this->get('/invoices/recurring/delete/' . $recurringId);



            /* Assert */

            $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);

        }
    private function __RecurringController_seedRecurring(array $overrides_from_recurringcontroller = []): int

        {

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            return $this->databaseInsert('ip_invoices_recurring', array_merge([

                'invoice_id'       => $invoiceId,

                'recur_start_date' => date('Y-m-d'),

                'recur_end_date'   => null,

                'recur_frequency'  => '1M',

                'recur_next_date'  => date('Y-m-d', strtotime('+1 month')),

            ], $overrides_from_recurringcontroller));

        }
    #[Test]

    public function it_denies_a_guest_access_to_another_clients_invoice_pdf(): void

        {

            /* Arrange */

            $ownClientId   = $this->seedClient(['client_name' => 'Guest Owner']);

            $otherClientId = $this->seedClient(['client_name' => 'Other Client']);



            $guestUserId = $this->databaseInsert('ip_users', [

                'user_name'          => 'guest_idor_test',

                'user_email'         => 'guest-idor@test.local',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_type'          => 2,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            // Link guest to ownClient only — NOT otherClient.

            $this->databaseInsert('ip_user_clients', [

                'user_id'   => $guestUserId,

                'client_id' => $ownClientId,

            ]);



            $otherInvoiceId = $this->seedInvoice($otherClientId);



            $this->actingAs([

                'user_id'       => $guestUserId,

                'user_type'     => 2,

                'user_email'    => 'guest-idor@test.local',

                'user_name'     => 'Guest IDOR Test',

                'user_company'  => '',

                'user_language' => 'system',

            ]);



            /* Act */

            $response = $this->get("/guest/invoices/generate_pdf/{$otherInvoiceId}");



            /* Assert */

            self::assertSame(

                404,

                $response->statusCode(),

                'A guest must not be able to retrieve another client\'s invoice PDF — expected 404.'

            );

        }
    #[Test]

    public function it_does_not_mark_an_invoice_sent_from_a_forged_generate_pdf_get(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $this->__SecurityRegression_enablePdfSentMarking('mark_invoices_sent_pdf');

            $this->withEnvironment(['CSRF_PROTECTION' => 'true']);

            $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);



            /* Act */

            $response = $this->get('/invoices/generate_pdf/' . $invoiceId . '/0');



            /* Assert */

            self::assertLessThan(500, $response->statusCode());

            self::assertSame(1, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_status_id']);

        }
    #[Test]

    public function it_marks_an_invoice_sent_only_with_a_matching_generate_pdf_csrf_token(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $this->__SecurityRegression_enablePdfSentMarking('mark_invoices_sent_pdf');

            $this->withEnvironment(['CSRF_PROTECTION' => 'true']);

            $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => '']);



            /* Act: this models the same-origin link rendered with _csrf_query(). */

            $response = $this->get(

                '/invoices/generate_pdf/' . $invoiceId . '/0',

                ['_ip_csrf'       => self::CSRF_TOKEN],

                ['ip_csrf_cookie' => self::CSRF_TOKEN]

            );



            /* Assert */

            self::assertLessThan(500, $response->statusCode());

            self::assertSame(2, (int) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_status_id']);

        }
    #[Test]

    public function it_rejects_a_path_traversal_value_for_the_invoice_logo_setting(): void

        {

            /* Arrange */

            // Before the fix, the invoice_logo setting was saved without filename validation.

            // An attacker who can POST settings could set invoice_logo to a path like

            // ../../config/database.php and have it served as a "logo".

            $this->actingAsAdmin();



            $originalLogo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);



            /* Act */

            $response = $this->post('/settings', [

                'settings' => [

                    'invoice_logo' => '../../../application/config/database.php',

                ],

            ]);



            /* Assert */

            // The controller must redirect back with an error — not accept the value.

            self::assertTrue(

                $response->isRedirect(),

                'A path-traversal invoice_logo value must cause a redirect (validation rejection), not a 200.'

            );



            // The stored value must not have changed to the traversal path.

            $stored      = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);

            $storedValue = $stored['setting_value'] ?? ($originalLogo['setting_value'] ?? '');



            self::assertStringNotContainsString(

                '../',

                (string) $storedValue,

                'A traversal path must never be persisted as the invoice_logo setting value.'

            );

        }
    #[Test]

    public function it_falls_back_to_the_default_template_for_a_path_traversal_pdf_template_name(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $clientId  = $this->seedClient(['client_name' => 'Template RCE Client']);

            $invoiceId = $this->seedInvoice($clientId);



            $traversalPayloads = [

                '..%2F..%2F..%2Fapplication%2Fconfig%2Fdatabase',

                '....%2F%2F....%2F%2Fetc%2F%2Fpasswd',

                '%2e%2e%2fapplication%2fconfig%2fdatabase',

            ];



            foreach ($traversalPayloads as $payload) {

                /* Act */

                $response = $this->get("/invoices/generate_pdf/{$invoiceId}/1/{$payload}");



                /* Assert */

                self::assertNotSame(

                    500,

                    $response->statusCode(),

                    "Traversal payload [{$payload}] as the PDF template name must not crash the request."

                );



                self::assertStringNotContainsString(

                    'DB_PASSWORD',

                    $response->body(),

                    "Traversal payload [{$payload}] must not leak application/config/database.php content."

                );

            }

        }
    #[Test]

    public function it_falls_back_to_the_default_template_for_an_unlisted_pdf_template_name(): void

        {

            /* Arrange */

            $this->actingAsAdmin();

            $clientId  = $this->seedClient(['client_name' => 'Template Whitelist Client']);

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->get("/invoices/generate_pdf/{$invoiceId}/1/EvilAttackerTemplate");



            /* Assert */

            self::assertNotSame(

                500,

                $response->statusCode(),

                'A template name outside the static whitelist must not crash the request — it must fall back to the safe default.'

            );

        }
    private function __SecurityRegression_enablePdfSentMarking(string $settingKey): void

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
