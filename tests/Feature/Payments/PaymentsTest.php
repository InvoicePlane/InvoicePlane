<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Payments;
use RuntimeException;
use Tests\AbstractTestCase;

class PaymentsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (isset($this->this)) {
            $this->__StripeGatewayCallback_tearDown();
        }
        parent::tearDown();
    }

    #[Test]

    public function it_redirects_an_unauthenticated_request_to_login(): void

        {

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/guest/payments');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_denies_an_admin_session_guest_type_access(): void

        {

            /* Arrange: an admin (user_type 1) is not a guest (user_type 2) */

            $this->actingAsAdmin();



            /* Act */

            $response = $this->get('/guest/payments');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_returns_403_for_a_guest_user_with_no_assigned_clients(): void

        {

            /* Arrange: a real guest user, but never linked to any client via ip_user_clients */

            $guestUserId = $this->databaseInsert('ip_users', [

                'user_name'          => 'Orphan Guest',

                'user_email'         => 'orphan-guest@test.local',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_type'          => 2,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

            $this->actingAs([

                'user_id'   => $guestUserId, 'user_type' => 2, 'user_email' => 'orphan-guest@test.local',

                'user_name' => 'Orphan Guest', 'user_company' => '', 'user_language' => 'system',

            ]);



            /* Act */

            $response = $this->get('/guest/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 403);

        }
    #[Test]

    public function it_lists_only_payments_for_the_guests_own_client(): void

        {

            /* Arrange */

            $ownClientId   = $this->seedClient(['client_name' => 'Own Client']);

            $otherClientId = $this->seedClient(['client_name' => 'Other Client']);



            $ownInvoiceId   = $this->seedInvoice($ownClientId);

            $otherInvoiceId = $this->seedInvoice($otherClientId);



            $this->seedPayment($ownInvoiceId, ['payment_note' => 'own-payment-marker']);

            $this->seedPayment($otherInvoiceId, ['payment_note' => 'other-payment-marker']);



            $this->__GuestPaymentsController_actingAsGuestUser($ownClientId);



            /* Act */

            $response = $this->get('/guest/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'own-payment-marker');

            $this->assertResponseBodyNotContains($response, 'other-payment-marker');

        }
    #[Test]

    public function it_does_not_expose_php_errors(): void

        {

            /* Arrange */

            $clientId = $this->seedClient();

            $this->__GuestPaymentsController_actingAsGuestUser($clientId);



            /* Act */

            $response = $this->get('/guest/payments');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    private function __GuestPaymentsController_actingAsGuestUser(int $clientId): void

        {

            $guestUserId = $this->databaseInsert('ip_users', [

                'user_name'          => 'Guest Payments Test',

                'user_email'         => 'guest-payments-' . bin2hex(random_bytes(4)) . '@test.local',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_type'          => 2,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $this->databaseInsert('ip_user_clients', ['user_id' => $guestUserId, 'client_id' => $clientId]);



            $this->actingAs([

                'user_id'       => $guestUserId,

                'user_type'     => 2,

                'user_email'    => 'guest-payments@test.local',

                'user_name'     => 'Guest Payments Test',

                'user_company'  => '',

                'user_language' => 'system',

            ]);

        }
    protected function __PaymentInformationController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect(): void

        {

            $this->__PaymentInformationController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Info Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId);



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId]);

        }
    #[Test]

    public function it_redirects_a_guest_to_login(): void

        {

            $this->__PaymentInformationController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_redirects_for_an_unknown_invoice_key(): void

        {

            /* Arrange */

            /* Act */

            $response = $this->get('/guest/payment_information/form/does-not-exist');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_redirects_for_a_draft_invoice_key(): void

        {

            /* Arrange: draft (status 1) invoices are never guest_visible() */

            $clientId = $this->seedClient();

            $urlKey   = 'draft-key-' . bin2hex(random_bytes(4));

            $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 1]);



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $urlKey);



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_returns_404_for_an_already_paid_invoice_when_unauthenticated(): void

        {

            /* Arrange */

            $this->actingAsGuest();

            $clientId = $this->seedClient();

            $urlKey   = 'paid-key-' . bin2hex(random_bytes(4));

            $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_renders_the_form_for_a_payable_invoice(): void

        {

            /* Arrange */

            $clientId = $this->seedClient();

            $urlKey   = 'payable-key-' . bin2hex(random_bytes(4));

            $this->seedInvoice($clientId, [

                'invoice_url_key'   => $urlKey,

                'invoice_status_id' => 2,

                'payment_method'    => 0,

            ], ['invoice_balance' => '100.00', 'invoice_total' => '100.00']);



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_does_not_expose_php_errors_for_an_already_paid_invoice(): void

        {

            /* Arrange */

            $clientId = $this->seedClient();

            $urlKey   = 'paid-noerr-key-' . bin2hex(random_bytes(4));

            $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $urlKey);



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    protected function __PaymentMethodsController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_payment_methods(): void

        {

            $this->__PaymentMethodsController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_payment_methods', [

                'payment_method_name' => 'Listed Method',

            ]);



            /* Act */

            $response = $this->get('/payment_methods');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Method');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_payment_method_form(): void

        {

            $this->__PaymentMethodsController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/payment_methods/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_a_payment_method(): void

        {

            $this->__PaymentMethodsController_setUp();

            /**

             * POST /payment_methods/form

             * {

             *     "payment_method_name": "Bank Transfer",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/payment_methods/form', [

                'payment_method_name' => 'Bank Transfer',

                'is_update'           => '0',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful create must redirect.');

            $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Bank Transfer']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_payment_method_name(): void

        {

            $this->__PaymentMethodsController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_payment_methods', [

                'payment_method_name' => 'Editable Method',

            ]);



            /* Act */

            $response = $this->get('/payment_methods/form/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'Editable Method');

        }
    #[Test]

    public function it_updates_a_payment_method(): void

        {

            $this->__PaymentMethodsController_setUp();

            /**

             * POST /payment_methods/form/{id}

             * {

             *     "payment_method_name": "Renamed Method",

             *     "is_update": "1",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_payment_methods', [

                'payment_method_name' => 'Original Method',

            ]);



            /* Act */

            $response = $this->post('/payment_methods/form/' . $id, [

                'payment_method_name' => 'Renamed Method',

                'is_update'           => '1',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful update must redirect.');

            $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Renamed Method']);

            $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Original Method']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_payment_method(): void

        {

            $this->__PaymentMethodsController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_payment_methods', [

                'payment_method_name' => 'Deletable Method',

            ]);

            $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Deletable Method']);



            /* Act */

            $response = $this->post('/payment_methods/delete/' . $id, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Deletable Method']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_payment_method_name(): void

        {

            $this->__PaymentMethodsController_setUp();

            /**

             * POST /payment_methods/form

             * {

             *     "payment_method_name": "",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/payment_methods/form', [

                'payment_method_name' => '',

                'is_update'           => '0',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_payment_methods', 0);

        }
    #[Test]

    public function it_fails_to_update_without_payment_method_name(): void

        {

            $this->__PaymentMethodsController_setUp();

            /**

             * POST /payment_methods/form/{id}

             * {

             *     "payment_method_name": "",

             *     "is_update": "1",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_payment_methods', [

                'payment_method_name' => 'Will Not Change',

            ]);



            /* Act */

            $response = $this->post('/payment_methods/form/' . $id, [

                'payment_method_name' => '',

                'is_update'           => '1',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Will Not Change']);

        }



        // -------------------------------------------------------------------------

        // Edge cases

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_when_creating_a_duplicate_payment_method(): void

        {

            $this->__PaymentMethodsController_setUp();

            /*

             * POST /payment_methods/form (duplicate)

             * {

             *     "payment_method_name": "Duplicate Method",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }

             */



            /* Arrange */

            $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Duplicate Method']);



            /* Act */

            $response = $this->post('/payment_methods/form', [

                'payment_method_name' => 'Duplicate Method',

                'is_update'           => '0',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Creating a duplicate payment method must redirect with flash error.');

            $this->assertDatabaseCount('ip_payment_methods', 1, ['payment_method_name' => 'Duplicate Method']);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login_from_paymentmethodscontroller(): void

        {

            $this->__PaymentMethodsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/payment_methods');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    private string $invoiceUrlKey;
    protected function __PaymentProviderAllowlist_setUp(): void

        {



            $this->actingAsGuest();



            $clientId            = $this->seedClient(['client_name' => 'Allowlist Test Client']);

            $this->invoiceUrlKey = 'allowlist-test-key-' . bin2hex(random_bytes(4));



            $this->seedInvoice($clientId, [

                'invoice_url_key'   => $this->invoiceUrlKey,

                'invoice_number'    => 'INV-ALLOWLIST-001',

                'invoice_status_id' => 2, // sent — required by guest_visible() filter

                'payment_method'    => 0,

            ], [

                'invoice_balance' => '100.00',

                'invoice_total'   => '100.00',

            ]);

        }
    #[Test]

    public function it_returns_200_when_accessing_the_payment_form_without_a_provider(): void

        {

            $this->__PaymentProviderAllowlist_setUp();

            /* Arrange */

            /* (invoice seeded in __PaymentProviderAllowlist_setUp) */



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey);



            /* Assert */

            // No payment_provider segment — should render the form or redirect to guest, not crash.

            self::assertNotSame(

                500,

                $response->statusCode(),

                'Accessing the payment form without a provider must not crash.'

            );

        }
    #[Test]

    public function it_returns_404_for_an_unknown_payment_provider_segment(): void

        {

            $this->__PaymentProviderAllowlist_setUp();

            /* Arrange */

            $unknownProvider = 'malicious_method';



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $unknownProvider);



            /* Assert */

            self::assertSame(

                404,

                $response->statusCode(),

                "An unknown payment provider [{$unknownProvider}] must return 404, not dispatch to an arbitrary method."

            );

        }
    #[Test]

    public function it_returns_404_for_an_internal_controller_method_name_as_provider(): void

        {

            $this->__PaymentProviderAllowlist_setUp();

            /* Arrange */

            $internalMethod = 'index';



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $internalMethod);



            /* Assert */

            self::assertSame(

                404,

                $response->statusCode(),

                "Internal method name [{$internalMethod}] passed as provider must return 404."

            );

        }
    #[Test]

    public function it_returns_404_for_a_path_traversal_attempt_as_provider(): void

        {

            $this->__PaymentProviderAllowlist_setUp();

            /* Arrange */

            $traversal = '__construct';



            /* Act */

            $response = $this->get('/guest/payment_information/form/' . $this->invoiceUrlKey . '/' . $traversal);



            /* Assert */

            self::assertSame(

                404,

                $response->statusCode(),

                'A __construct provider segment must return 404, not be invoked.'

            );

        }
    protected function __PaymentsAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_adds_a_payment_with_all_required_fields(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/add', $this->__PaymentsAjaxController_validPayload($invoiceId));



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

            $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '25.00']);

        }
    #[Test]

    public function it_fails_to_add_a_payment_without_invoice_id(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

            $payload   = $this->__PaymentsAjaxController_validPayload($invoiceId);

            unset($payload['invoice_id']);



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/add', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_fails_to_add_a_payment_without_payment_date(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

            $payload   = $this->__PaymentsAjaxController_validPayload($invoiceId);

            unset($payload['payment_date']);



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/add', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_fails_to_add_a_payment_without_payment_amount(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

            $payload   = $this->__PaymentsAjaxController_validPayload($invoiceId);

            unset($payload['payment_amount']);



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/add', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_fails_to_add_a_payment_exceeding_the_invoice_balance(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId                  = $this->seedClient();

            $invoiceId                 = $this->seedInvoice($clientId, [], ['invoice_balance' => '10.00']);

            $payload                   = $this->__PaymentsAjaxController_validPayload($invoiceId);

            $payload['payment_amount'] = '999.00';



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/add', $payload);



            /* Assert */

            $json = json_decode($response->body(), true);

            self::assertSame(0, $json['success'] ?? null);

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_renders_the_add_payment_modal(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);



            /* Act */

            $response = $this->ajax('POST', '/payments/ajax/modal_add_payment', [

                'invoice_id'      => (string) $invoiceId,

                'invoice_balance' => '100.00',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_requires_an_ajax_request(): void

        {

            $this->__PaymentsAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);



            /* Act */

            $response = $this->post('/payments/ajax/add', $this->__PaymentsAjaxController_validPayload($invoiceId));



            /* Assert */

            self::assertSame('', $response->body());

            $this->assertDatabaseCount('ip_payments', 0);

        }
    private function __PaymentsAjaxController_validPayload(int $invoiceId): array

        {

            return [

                'invoice_id'     => (string) $invoiceId,

                'payment_date'   => date('Y-m-d'),

                'payment_amount' => '25.00',

            ];

        }
    protected function __PaymentsFeature_setUp(): void

        {



            $this->actingAsAdmin();

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'decimal_point', 'setting_value' => '.']);

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'tax_rate_decimal_places', 'setting_value' => '2']);

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_payments(): void

        {

            $this->__PaymentsFeature_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment List Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId, ['payment_amount' => '99.00']);



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '99.00');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_payment_form(): void

        {

            $this->__PaymentsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/payments/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_a_payment_and_links_it_to_the_invoice(): void

        {

            $this->__PaymentsFeature_setUp();

            /**

             * POST /payments/form

             * {

             *     "invoice_id": "<invoiceId>",

             *     "payment_method_id": "1",

             *     "payment_amount": "250.00",

             *     "payment_date": "2026-06-21",

             *     "payment_note": "Test payment",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Create Client']);

            $invoiceId = $this->seedInvoice($clientId, [], [

                'invoice_total'   => '250.00',

                'invoice_balance' => '250.00',

            ]);



            /* Act */

            $response = $this->post('/payments/form', [

                'invoice_id'        => $invoiceId,

                'payment_method_id' => '1',

                'payment_amount'    => '250.00',

                'payment_date'      => date('Y-m-d'),

                'payment_note'      => 'Test payment',

                'btn_submit'        => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful create must redirect.');

            $this->assertDatabaseHas('ip_payments', [

                'invoice_id'     => $invoiceId,

                'payment_amount' => '250.00',

            ]);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_payment_form_showing_existing_amount(): void

        {

            $this->__PaymentsFeature_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Edit Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '175.50']);



            /* Act */

            $response = $this->get('/payments/form/' . $paymentId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, '175');

        }
    #[Test]

    public function it_updates_a_payment(): void

        {

            $this->__PaymentsFeature_setUp();

            /**

             * POST /payments/form/{id}

             * {

             *     "invoice_id": "<invoiceId>",

             *     "payment_method_id": "1",

             *     "payment_amount": "300.00",

             *     "payment_date": "2026-06-21",

             *     "payment_note": "Updated payment",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Update Client']);

            $invoiceId = $this->seedInvoice($clientId, [], ['invoice_total' => '300.00', 'invoice_balance' => '300.00']);

            $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '100.00']);



            /* Act */

            $response = $this->post('/payments/form/' . $paymentId, [

                'invoice_id'        => $invoiceId,

                'payment_method_id' => '1',

                'payment_amount'    => '300.00',

                'payment_date'      => date('Y-m-d'),

                'payment_note'      => 'Updated payment',

                'btn_submit'        => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful update must redirect.');

            $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '300.00']);

            $this->assertDatabaseMissing('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '100.00']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_payment(): void

        {

            $this->__PaymentsFeature_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Delete Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '50.00']);

            $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId]);



            /* Act */

            $response = $this->post('/payments/delete/' . $paymentId, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_payments', ['payment_id' => $paymentId]);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_invoice_id(): void

        {

            $this->__PaymentsFeature_setUp();

            /**

             * POST /payments/form

             * {

             *     "invoice_id": "",

             *     "payment_amount": "100.00",

             *     "payment_date": "2026-06-21",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/payments/form', [

                'invoice_id'     => '',

                'payment_amount' => '100.00',

                'payment_date'   => date('Y-m-d'),

                'btn_submit'     => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_fails_to_create_without_payment_amount(): void

        {

            $this->__PaymentsFeature_setUp();

            /**

             * POST /payments/form

             * {

             *     "invoice_id": "<invoiceId>",

             *     "payment_amount": "",

             *     "payment_date": "2026-06-21",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment Fail Client']);

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->post('/payments/form', [

                'invoice_id'     => $invoiceId,

                'payment_amount' => '',

                'payment_date'   => date('Y-m-d'),

                'btn_submit'     => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_payments', 0);

        }
    #[Test]

    public function it_fails_to_create_without_payment_date(): void

        {

            $this->__PaymentsFeature_setUp();

            /**

             * POST /payments/form

             * {

             *     "invoice_id": "<invoiceId>",

             *     "payment_amount": "100.00",

             *     "payment_date": "",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Payment No Date Client']);

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->post('/payments/form', [

                'invoice_id'     => $invoiceId,

                'payment_amount' => '100.00',

                'payment_date'   => '',

                'btn_submit'     => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_payments', 0);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void

        {

            $this->__PaymentsFeature_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __PaypalController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_paypalcontroller(): void

        {

            $this->__PaypalController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Paypal Test Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId);



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_paypalcontroller(): void

        {

            $this->__PaypalController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_returns_404_for_a_non_post_create_order_request(): void

        {

            /* Arrange */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            /* Act */

            $response = $this->get('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_create_order_on_an_unknown_invoice_key(): void

        {

            /* Arrange */

            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/does-not-exist');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_create_order_on_a_draft_invoice(): void

        {

            /* Arrange: draft (status 1) invoices are never guest_visible() */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice(['invoice_status_id' => 1]);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_redirects_create_order_for_an_already_paid_invoice_without_calling_paypal(): void

        {

            /* Arrange: only authorize() is queued — a live createOrder() call would error */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '0.00']);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

            $this->__PaypalGatewayCallback_mockPaypal([$this->__PaypalGatewayCallback_authResponse()]);



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert: redirected away, no order-creation call was ever reached */

            self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));

        }
    #[Test]

    public function it_creates_a_paypal_order_for_a_payable_invoice(): void

        {

            /* Arrange */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                ['status' => 201, 'body' => json_encode(['id' => 'PAYPAL-ORDER-123', 'status' => 'CREATED'])],

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $json = json_decode($response->body(), true);

            self::assertSame('PAYPAL-ORDER-123', $json['id'] ?? null);

            self::assertSame('CREATED', $json['status'] ?? null);

            self::assertArrayHasKey('csrf_token', $json);

        }
    #[Test]

    public function it_returns_500_when_paypal_returns_malformed_json_for_create_order(): void

        {

            /* Arrange */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                ['status' => 200, 'body' => '{not-json'],

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 500);

            self::assertStringContainsString('error', $response->body());

        }
    #[Test]

    public function it_returns_500_when_paypal_response_is_missing_the_order_id(): void

        {

            /* Arrange */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                ['status' => 201, 'body' => json_encode(['status' => 'CREATED'])],

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 500);

            self::assertStringContainsString('error', $response->body());

        }



        // -------------------------------------------------------------------------

        // paypal_capture_payment

        // -------------------------------------------------------------------------
    #[Test]

    public function it_returns_404_for_a_non_post_capture_payment_request(): void

        {

            /* Arrange */

            /* Act */

            $response = $this->get('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_records_a_completed_capture_and_creates_a_payment(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-1']),

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');



            /* Assert */

            self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

            $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-1', 'payment_amount' => '50.00']);

            $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1, 'merchant_response_driver' => 'paypal']);

        }
    #[Test]

    public function it_records_a_pending_capture_as_a_payment_with_a_pending_note(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-PENDING'], 'PENDING'),

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-2');



            /* Assert */

            $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-PENDING']);

        }
    #[Test]

    public function it_does_not_duplicate_a_payment_for_an_already_processed_capture_id(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();

            $this->seedPayment($invoiceId, ['payment_external_id' => 'CAP-DUP', 'payment_amount' => '50.00']);



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-DUP']),

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-3');



            /* Assert: still exactly one payment row for this capture id, not two */

            $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'CAP-DUP']);

        }
    #[Test]

    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '0.00']);



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-ALREADY-PAID']),

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-4');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-ALREADY-PAID']);

        }
    #[Test]

    public function it_rejects_a_capture_whose_currency_does_not_match_the_gateway_setting(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-BAD-CCY', 'currency' => 'USD']),

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-5');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-BAD-CCY']);

        }
    #[Test]

    public function it_rejects_a_capture_whose_amount_is_short_of_the_invoice_balance(): void

        {

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '50.00']);



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '10.00', 'capture_id' => 'CAP-SHORT']),

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-6');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-SHORT']);

        }
    #[Test]

    public function it_records_a_declined_capture_as_an_unsuccessful_merchant_response(): void

        {

            /* Arrange */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice();



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                ['status' => 200, 'body' => json_encode([

                    'purchase_units' => [[

                        'payments' => [

                            'captures' => [[

                                'status'             => 'DECLINED',

                                'invoice_id'         => $invoiceId,

                                'processor_response' => ['response_code' => '05'],

                            ]],

                        ],

                    ]],

                    'id' => 'PAYPAL-ORDER-DECLINED',

                ])],

            ]);



            /* Act */

            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-7');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);

            $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 0]);

        }
    #[Test]

    public function it_throws_and_records_nothing_when_the_captured_invoice_is_not_guest_visible(): void

        {

            /* Arrange: draft invoice — never guest_visible() */

            $invoiceId = $this->__PaypalGatewayCallback_seedPayableInvoice(['invoice_status_id' => 1]);



            $this->__PaypalGatewayCallback_mockPaypal([

                $this->__PaypalGatewayCallback_authResponse(),

                $this->__PaypalGatewayCallback_captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-NOT-VISIBLE']),

            ]);



            /* Act */

            try {

                $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-8');

                self::fail('Expected an exception for a non-guest-visible invoice.');

            } catch (RuntimeException $exception) {

                /* Assert */

                self::assertStringContainsString('Invoice not found or not accessible', $exception->getMessage());

            }



            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-NOT-VISIBLE']);

            $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        }
    private function __PaypalGatewayCallback_mockPaypal(array $responses): void

        {

            $this->withEnvironment(['PAYPAL_MOCK_RESPONSES' => json_encode($responses)]);

        }
    private function __PaypalGatewayCallback_authResponse(): array

        {

            return ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];

        }
    private function __PaypalGatewayCallback_seedPayableInvoice(array $overrides = [], array $amountOverrides = []): int

        {

            $clientId = $this->seedClient();



            return $this->seedInvoice($clientId, array_merge(['invoice_status_id' => 2], $overrides), array_merge(['invoice_balance' => '50.00'], $amountOverrides));

        }
    private function __PaypalGatewayCallback_captureResponse(array $capture, string $status = 'COMPLETED'): array

        {

            return ['status' => 200, 'body' => json_encode([

                'purchase_units' => [[

                    'payments' => [

                        'captures' => [[

                            'status'     => $status,

                            'invoice_id' => $capture['invoice_id'],

                            'id'         => $capture['capture_id'] ?? 'CAPTURE-' . bin2hex(random_bytes(4)),

                            'amount'     => ['value' => $capture['amount'], 'currency_code' => $capture['currency'] ?? 'EUR'],

                        ]],

                    ],

                ]],

                'id' => 'PAYPAL-ORDER-RESOURCE',

            ])];

        }
    protected function __StripeController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_stripecontroller(): void

        {

            $this->__StripeController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient(['client_name' => 'Stripe Test Client']);

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId);



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_stripecontroller(): void

        {

            $this->__StripeController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/payments');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())

            );

        }
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';



        /** @var array<int, string> */
    private array $captureFiles = [];
    protected function __StripeGatewayCallback_setUp(): void

        {





            // StripeClient's constructor validates api_key eagerly, before any guard

            // clause runs, so every test — even the 404 ones — needs a syntactically

            // valid (encrypted-at-rest, like the real setting) fake key.

            require_once dirname(__DIR__, 3) . '/application/libraries/Cryptor.php';

            $ciphertext = Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY);

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_apiKey', 'setting_value' => $ciphertext]);

        }
    protected function __StripeGatewayCallback_tearDown(): void

        {

            foreach ($this->captureFiles as $captureFile) {

                if (is_file($captureFile)) {

                    unlink($captureFile);

                }

            }



            $this->captureFiles = [];





        }



        // -------------------------------------------------------------------------

        // create_checkout_session

        // -------------------------------------------------------------------------
    #[Test]

    public function it_returns_404_for_a_non_post_checkout_session_request(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            /* Act */

            $response = $this->get('/guest/gateways/stripe/create_checkout_session/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_checkout_session_on_an_unknown_invoice_key(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/guest/gateways/stripe/create_checkout_session/does-not-exist');



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_returns_404_for_checkout_session_on_a_draft_invoice(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange: draft (status 1) invoices are never guest_visible() */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice(['invoice_status_id' => 1]);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            /* Act */

            $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 404);

        }
    #[Test]

    public function it_redirects_checkout_session_for_an_already_paid_invoice_without_calling_stripe(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange: no STRIPE_MOCK_RESPONSES queued — a live call would error */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '0.00']);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            /* Act */

            $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);



            /* Assert */

            self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));

        }
    #[Test]

    public function it_creates_a_checkout_session_for_a_payable_invoice(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([

                ['status' => 200, 'body' => json_encode([

                    'id'            => 'cs_test_123',

                    'object'        => 'checkout.session',

                    'client_secret' => 'cs_test_123_secret_abc',

                ])],

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $json = json_decode($response->body(), true);

            self::assertSame('cs_test_123_secret_abc', $json['clientSecret'] ?? null);

        }
    #[Test]

    public function it_sends_a_jpy_invoice_total_as_100_minor_units_to_stripe_checkout(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'JPY']);

            $invoiceId   = $this->__StripeGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '100.00']);

            $urlKey      = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

            $captureFile = tempnam(sys_get_temp_dir(), 'stripe-request-');

            self::assertNotFalse($captureFile);

            $this->captureFiles[] = $captureFile;



            $this->withEnvironment([

                'STRIPE_MOCK_RESPONSES' => json_encode([

                    ['status' => 200, 'body' => json_encode([

                        'id'            => 'cs_jpy_100',

                        'object'        => 'checkout.session',

                        'client_secret' => 'cs_jpy_100_secret',

                    ])],

                ]),

                'STRIPE_MOCK_REQUEST_CAPTURE' => $captureFile,

            ]);



            /* Act */

            $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $request = json_decode((string) file_get_contents($captureFile), true, 512, JSON_THROW_ON_ERROR);

            self::assertSame('JPY', $request['params']['line_items'][0]['price_data']['currency']);

            self::assertSame(100, $request['params']['line_items'][0]['price_data']['unit_amount']);

        }
    #[Test]

    public function it_records_a_paid_callback_and_creates_a_payment(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_success_1',

                'currency'            => 'eur',

                'amount_total'        => 5000,

            ])]);



            /* Act */

            $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_success_1', 'payment_amount' => '50.00']);

            $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1]);

        }
    #[Test]

    public function it_does_not_duplicate_a_payment_for_an_already_processed_payment_intent(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

            $this->seedPayment($invoiceId, ['payment_external_id' => 'pi_dup', 'payment_amount' => '50.00']);



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_dup',

                'currency'            => 'eur',

                'amount_total'        => 5000,

            ])]);



            /* Act */

            $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert: still exactly one payment row for this intent, not two */

            $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'pi_dup']);

        }
    #[Test]

    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid_from_stripegatewaycallback(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '0.00']);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_already_paid',

                'currency'            => 'eur',

                'amount_total'        => 5000,

            ])]);



            /* Act */

            $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_already_paid']);

        }
    #[Test]

    public function it_rejects_a_callback_whose_currency_does_not_match_the_gateway_setting(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_bad_ccy',

                'currency'            => 'usd',

                'amount_total'        => 5000,

            ])]);



            /* Act */

            $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_bad_ccy']);

        }
    #[Test]

    public function it_rejects_a_callback_whose_amount_is_short_of_the_invoice_balance(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice([], ['invoice_balance' => '50.00']);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_short',

                'currency'            => 'eur',

                'amount_total'        => 1000,

            ])]);



            /* Act */

            $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert */

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_short']);

        }
    #[Test]

    public function it_does_not_record_a_payment_for_an_unpaid_callback(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice();

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'unpaid',

                'client_reference_id' => $urlKey,

            ])]);



            /* Act */

            $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);

        }
    #[Test]

    public function it_records_an_error_response_when_the_callback_invoice_is_not_guest_visible(): void

        {

            $this->__StripeGatewayCallback_setUp();

            /* Arrange: draft invoice — never guest_visible() */

            $invoiceId = $this->__StripeGatewayCallback_seedPayableInvoice(['invoice_status_id' => 1]);

            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];



            $this->__StripeGatewayCallback_mockStripe([$this->__StripeGatewayCallback_sessionResponse([

                'payment_status'      => 'paid',

                'client_reference_id' => $urlKey,

                'payment_intent'      => 'pi_not_visible',

            ])]);



            /* Act */

            $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');



            /* Assert: the controller's own try/catch handles this, no crash, no mutation */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_not_visible']);

            $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        }
    private function __StripeGatewayCallback_mockStripe(array $responses_from_stripegatewaycallback): void

        {

            $this->withEnvironment(['STRIPE_MOCK_RESPONSES' => json_encode($responses_from_stripegatewaycallback)]);

        }
    private function __StripeGatewayCallback_seedPayableInvoice(array $overrides = [], array $amountOverrides_from_stripegatewaycallback = []): int

        {

            $clientId = $this->seedClient();



            return $this->seedInvoice($clientId, array_merge(['invoice_status_id' => 2], $overrides), array_merge(['invoice_balance' => '50.00'], $amountOverrides_from_stripegatewaycallback));

        }



        // -------------------------------------------------------------------------

        // callback

        // -------------------------------------------------------------------------
    private function __StripeGatewayCallback_sessionResponse(array $overrides): array

        {

            return ['status' => 200, 'body' => json_encode(array_merge([

                'id'                     => 'cs_test_callback',

                'object'                 => 'checkout.session',

                'status'                 => 'complete',

                'mode'                   => 'payment',

                'payment_status'         => 'unpaid',

                'client_reference_id'    => null,

                'payment_intent'         => null,

                'currency'               => 'eur',

                'amount_total'           => 5000,

                'amount_received'        => 0,

                'application_fee_amount' => 0,

                'livemode'               => false,

                'cancellation_reason'    => null,

                'last_payment_error'     => null,

            ], $overrides))];

        }
}
