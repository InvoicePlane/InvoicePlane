<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\AbstractTestCase;
use Welcome;

class CoreTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (isset($this->file)) {
            $this->__ImportController_tearDown();
        }
        if (isset($this->this)) {
            $this->__MdlUploads_tearDown();
        }
        parent::tearDown();
    }

    protected function __Authentication_setUp(): void

        {



            // public route — no auth needed

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect(): void

        {

            $this->__Authentication_setUp();

            /* Arrange */

            /* (public route — no auth needed) */



            /* Act */

            $response = $this->get('/sessions/login');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    protected function __ControllersAuthGuard_setUp(): void

        {



            $this->actingAsGuest();

        }
    public static function __ControllersAuthGuard_adminRouteProvider(): array

        {

            return [

                'invoices index'        => ['/invoices'],

                'invoices status all'   => ['/invoices/status/all'],

                'invoices status draft' => ['/invoices/status/draft'],

                'invoices status paid'  => ['/invoices/status/paid'],

                'clients index'         => ['/clients'],

                'clients status active' => ['/clients/status/active'],

                'payments index'        => ['/payments'],

                'payments online_logs'  => ['/payments/online_logs'],

                'quotes index'          => ['/quotes'],

                'products index'        => ['/products'],

                'tasks index'           => ['/tasks'],

                'tax_rates index'       => ['/tax_rates'],

                'units index'           => ['/units'],

                'families index'        => ['/families'],

                'payment_methods index' => ['/payment_methods'],

                'invoice_groups index'  => ['/invoice_groups'],

                'email_templates index' => ['/email_templates'],

                'custom_fields index'   => ['/custom_fields'],

                'custom_values index'   => ['/custom_values'],

                'users index'           => ['/users'],

                'settings index'        => ['/settings'],

                'reports index'         => ['/reports/sales_by_client'],

                'dashboard'             => ['/dashboard'],

                'import index'          => ['/import'],

                'projects index'        => ['/projects'],

            ];

        }
    #[Test]

    public function it_redirects_an_unauthenticated_visitor_away_from_admin_module(): void

        {

            $this->__ControllersAuthGuard_setUp();

            /* Arrange */

            foreach (self::__ControllersAuthGuard_adminRouteProvider() as [$uri]) {

                /* Act */

                $response = $this->get($uri);



                /* Assert */

                self::assertTrue(

                    $response->isRedirect(),

                    sprintf(

                        'Unauthenticated GET [%s] must redirect to login. Got status [%d] with body (first 200 chars): %s',

                        $uri,

                        $response->statusCode(),

                        mb_substr($response->body(), 0, 200)

                    )

                );



                self::assertFalse(

                    $response->contains('<form') && $response->contains('invoice'),

                    sprintf('[%s] exposed admin form content.', $uri)

                );

            }

        }
    #[Test]

    public function it_does_not_expose_php_errors_on_an_unauthenticated_request_to_admin_route(): void

        {

            $this->__ControllersAuthGuard_setUp();

            /* Arrange */

            foreach (self::__ControllersAuthGuard_adminRouteProvider() as [$uri]) {

                /* Act */

                $response = $this->get($uri);



                /* Assert */

                $this->assertResponseHasNoPhpErrors($response);

            }

        }
    protected function __CoreAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_coreajaxcontroller(): void

        {

            $this->__CoreAjaxController_setUp();

            /* Arrange */

            $this->seedClient(['client_name' => 'Ajax Test Client']);



            /* Act */

            $response = $this->get('/clients/status/active');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login(): void

        {

            $this->__CoreAjaxController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/clients/status/active');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/clients] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __CustomFieldsService_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_customfieldsservice(): void

        {

            $this->__CustomFieldsService_setUp();

            /* Arrange */

            /* (authenticated admin via __CustomFieldsService_setUp) */



            /* Act */

            $response = $this->get('/custom_fields');



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

                sprintf('[GET /custom_fields] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors(): void

        {

            $this->__CustomFieldsService_setUp();

            /* Arrange */

            /* (authenticated admin via __CustomFieldsService_setUp) */



            /* Act */

            $response = $this->get('/custom_fields');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_customfieldsservice(): void

        {

            $this->__CustomFieldsService_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/custom_fields');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/custom_fields] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_creates_a_custom_field_for_an_allowed_table(): void

        {

            $this->__CustomFieldsService_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/custom_fields/form', [

                'custom_field_table'    => 'ip_client_custom',

                'custom_field_label'    => 'Client Reference',

                'custom_field_type'     => 'TEXT',

                'custom_field_order'    => '1',

                'custom_field_location' => '0',

                'btn_submit'            => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful custom field create must redirect.');

            $this->assertDatabaseHas('ip_custom_fields', [

                'custom_field_table' => 'ip_client_custom',

                'custom_field_label' => 'Client Reference',

                'custom_field_type'  => 'TEXT',

            ]);

        }
    #[Test]

    public function it_rejects_custom_field_table_names_outside_the_allowlist(): void

        {

            $this->__CustomFieldsService_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/custom_fields/form', [

                'custom_field_table'    => 'ip_client_custom; DROP TABLE ip_users; --',

                'custom_field_label'    => 'Injected Table',

                'custom_field_type'     => 'TEXT',

                'custom_field_order'    => '1',

                'custom_field_location' => '0',

                'btn_submit'            => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_label' => 'Injected Table']);

            $this->assertDatabaseHas('ip_users', ['user_email' => 'admin@test.local']);

        }
    protected function __CustomValuesController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_customvaluescontroller(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            /* (authenticated admin via __CustomValuesController_setUp) */



            /* Act */

            $response = $this->get('/custom_values');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_customvaluescontroller(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/custom_values');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/custom_values] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_creates_a_custom_value_for_an_allowed_custom_field(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->databaseInsert('ip_custom_fields', [

                'custom_field_table'    => 'ip_client_custom',

                'custom_field_label'    => 'Client Tier',

                'custom_field_type'     => 'SINGLE-CHOICE',

                'custom_field_order'    => 1,

                'custom_field_location' => 0,

            ]);



            /* Act */

            $response = $this->post('/custom_values/create/' . $fieldId, [

                'custom_values_value' => 'Gold',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful custom value create must redirect.');

            $this->assertDatabaseHas('ip_custom_values', [

                'custom_values_field' => $fieldId,

                'custom_values_value' => 'Gold',

            ]);

        }
    #[Test]

    public function it_does_not_create_orphan_custom_values_for_missing_fields(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/custom_values/create/999999', [

                'custom_values_value' => 'Orphan',

                'btn_submit'          => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Missing custom field create must return without saving.');

            $this->assertDatabaseMissing('ip_custom_values', ['custom_values_value' => 'Orphan']);

        }
    #[Test]

    public function it_fails_to_create_a_custom_value_without_a_value(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();



            /* Act */

            $this->post('/custom_values/create/' . $fieldId, ['custom_values_value' => '', 'btn_submit' => '1']);



            /* Assert */

            $this->assertDatabaseCount('ip_custom_values', 0);

        }
    #[Test]

    public function it_shows_the_field_page_for_an_existing_custom_field(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();



            /* Act */

            $response = $this->get('/custom_values/field/' . $fieldId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_updates_an_existing_custom_value(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();

            $valueId = $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'Silver']);



            /* Act */

            $response = $this->post('/custom_values/edit/' . $valueId, ['custom_values_value' => 'Platinum', 'btn_submit' => '1']);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $valueId, 'custom_values_value' => 'Platinum']);

        }
    #[Test]

    public function it_fails_to_update_a_custom_value_without_a_value(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();

            $valueId = $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'Silver']);



            /* Act */

            $this->post('/custom_values/edit/' . $valueId, ['custom_values_value' => '', 'btn_submit' => '1']);



            /* Assert */

            $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $valueId, 'custom_values_value' => 'Silver']);

        }
    #[Test]

    public function it_deletes_an_unused_custom_value(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();

            $valueId = $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'Deletable']);



            /* Act */

            $response = $this->post('/custom_values/delete/' . $valueId);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_custom_values', ['custom_values_id' => $valueId]);

        }
    #[Test]

    public function it_does_not_delete_a_custom_value_on_a_non_post_request(): void

        {

            $this->__CustomValuesController_setUp();

            /* Arrange */

            $fieldId = $this->__CustomValuesController_seedCustomField();

            $valueId = $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'Untouched']);



            /* Act */

            $this->get('/custom_values/delete/' . $valueId);



            /* Assert */

            $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $valueId]);

        }
    private function __CustomValuesController_seedCustomField(array $overrides = []): int

        {

            return $this->databaseInsert('ip_custom_fields', array_merge([

                'custom_field_table'    => 'ip_client_custom',

                'custom_field_label'    => 'Client Tier',

                'custom_field_type'     => 'SINGLE-CHOICE',

                'custom_field_order'    => 1,

                'custom_field_location' => 0,

            ], $overrides));

        }
    protected function __DashboardController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('crud')]

    public function it_displays_dashboard_with_a_200_status(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_a_full_html_document_on_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseBodyContains($response, '<html');

            $this->assertResponseBodyContains($response, '</html>');

            self::assertGreaterThan(

                500,

                $response->bodyLength(),

                'Dashboard body is suspiciously short — the layout likely did not render.'

            );

        }
    #[Test]

    public function it_includes_navigation_elements_on_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->contains('invoice') || $response->contains('client') || $response->contains('nav'),

                'The dashboard must contain at least one primary navigation element.'

            );

        }
    #[Test]

    public function it_redirects_a_guest_away_from_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET /dashboard must redirect. Got status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_on_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $first  = $this->get('/dashboard');

            $second = $this->get('/dashboard');



            /* Assert */

            self::assertSame(

                $first->statusCode(),

                $second->statusCode(),

                'Two consecutive GET /dashboard requests must return the same HTTP status.'

            );

        }
    #[Test]

    public function it_does_not_display_invoice_form_content_on_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertFalse(

                $response->contains('<form') && $response->contains('invoice_number'),

                'The dashboard must not render an invoice creation form.'

            );

        }
    #[Test]

    public function it_includes_the_clients_section_link_on_the_dashboard(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            /* (authenticated admin via __DashboardController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertTrue(

                $response->contains('client') || $response->contains('invoice'),

                'Dashboard must reference clients or invoices in its content.'

            );

        }
    #[Test]

    public function it_returns_200_with_seeded_invoices_and_clients(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Dashboard Test Client']);

            $this->seedInvoice($clientId, ['invoice_date_created' => date('Y-m-d')]);



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_returns_200_with_multiple_seeded_clients(): void

        {

            $this->__DashboardController_setUp();

            /* Arrange */

            $this->seedClient(['client_name' => 'Alpha Corp']);

            $this->seedClient(['client_name' => 'Beta Ltd']);

            $this->seedClient(['client_name' => 'Gamma BV']);



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    protected function __DashboardFeature_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_renders_the_dashboard_with_a_200_status_when_authenticated(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_renders_a_full_html_document_on_the_dashboard_from_dashboardfeature(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseBodyContains($response, '<html');

            $this->assertResponseBodyContains($response, '</html>');



            self::assertGreaterThan(

                500,

                $response->bodyLength(),

                'Dashboard body is suspiciously short — the layout likely did not render.'

            );

        }
    #[Test]

    public function it_includes_navigation_elements_on_the_dashboard_from_dashboardfeature(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);



            self::assertTrue(

                $response->contains('invoice') || $response->contains('client') || $response->contains('nav'),

                'The dashboard must contain at least one primary navigation element.'

            );

        }
    #[Test]

    public function it_redirects_a_guest_away_from_the_dashboard_from_dashboardfeature(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET /dashboard must redirect. Got status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_on_the_dashboard_from_dashboardfeature(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_produces_a_deterministic_dashboard_response_on_two_consecutive_requests_from_dashboardfeature(): void

        {

            $this->__DashboardFeature_setUp();

            /* Arrange */

            $first = $this->get('/dashboard');

            /* Act */

            $second = $this->get('/dashboard');



            /* Assert */

            self::assertSame(

                $first->statusCode(),

                $second->statusCode(),

                'Two consecutive GET /dashboard requests must return the same HTTP status.'

            );

        }
    protected function __EmailTemplatesAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_emailtemplatesajaxcontroller(): void

        {

            $this->__EmailTemplatesAjaxController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Ajax Email Template',

                'email_template_subject' => 'Ajax Subject',

                'email_template_body'    => 'Ajax body',

                'email_template_type'    => 'invoice',

            ]);



            /* Act */

            $response = $this->get('/email_templates');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Ajax Email Template');

        }
    protected function __EmailTemplatesAjaxGetContent_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_gets_the_content_of_an_existing_template(): void

        {

            $this->__EmailTemplatesAjaxGetContent_setUp();

            /* Arrange */

            $templateId = $this->databaseInsert('ip_email_templates', [

                'email_template_title' => 'Ajax Get Content Template',

                'email_template_type'  => 'invoice',

                'email_template_body'  => 'Marker body content',

            ]);



            /* Act */

            $response = $this->ajax('POST', '/email_templates/ajax/get_content', ['email_template_id' => (string) $templateId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Marker body content');

        }
    #[Test]

    public function it_returns_null_for_an_unknown_template_id(): void

        {

            $this->__EmailTemplatesAjaxGetContent_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/email_templates/ajax/get_content', ['email_template_id' => '999999']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame('null', trim($response->body()));

        }
    #[Test]

    public function it_requires_an_ajax_request(): void

        {

            $this->__EmailTemplatesAjaxGetContent_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/email_templates/ajax/get_content', ['email_template_id' => '1']);



            /* Assert */

            self::assertSame('', $response->body());

        }
    protected function __EmailTemplatesController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_email_templates(): void

        {

            $this->__EmailTemplatesController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Listed Template',

                'email_template_subject' => 'Hello',

                'email_template_body'    => 'Body text',

            ]);



            /* Act */

            $response = $this->get('/email_templates');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Template');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_email_template_form(): void

        {

            $this->__EmailTemplatesController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/email_templates/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_an_email_template(): void

        {

            $this->__EmailTemplatesController_setUp();

            /**

             * POST /email_templates/form

             * {

             *     "email_template_title": "Invoice Reminder",

             *     "email_template_subject": "Your invoice is due",

             *     "email_template_body": "Please pay your invoice.",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/email_templates/form', [

                'email_template_title'   => 'Invoice Reminder',

                'email_template_subject' => 'Your invoice is due',

                'email_template_body'    => 'Please pay your invoice.',

                'is_update'              => '0',

                'btn_submit'             => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful create must redirect.');

            $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Invoice Reminder']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_template_title(): void

        {

            $this->__EmailTemplatesController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Editable Template',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

            ]);



            /* Act */

            $response = $this->get('/email_templates/form/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'Editable Template');

        }
    #[Test]

    public function it_updates_an_email_template(): void

        {

            $this->__EmailTemplatesController_setUp();

            /**

             * POST /email_templates/form/{id}

             * {

             *     "email_template_title": "Renamed Template",

             *     "email_template_subject": "Updated subject",

             *     "email_template_body": "Updated body.",

             *     "is_update": "1",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Original Template',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

            ]);



            /* Act */

            $response = $this->post('/email_templates/form/' . $id, [

                'email_template_title'   => 'Renamed Template',

                'email_template_subject' => 'Updated subject',

                'email_template_body'    => 'Updated body.',

                'is_update'              => '1',

                'btn_submit'             => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful update must redirect.');

            $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Renamed Template']);

            $this->assertDatabaseMissing('ip_email_templates', ['email_template_title' => 'Original Template']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_an_email_template(): void

        {

            $this->__EmailTemplatesController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Deletable Template',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

            ]);

            $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Deletable Template']);



            /* Act */

            $response = $this->post('/email_templates/delete/' . $id, []);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Delete must redirect.');

            $this->assertDatabaseMissing('ip_email_templates', ['email_template_title' => 'Deletable Template']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_email_template_title(): void

        {

            $this->__EmailTemplatesController_setUp();

            /**

             * POST /email_templates/form

             * {

             *     "email_template_title": "",

             *     "email_template_subject": "Subject",

             *     "email_template_body": "Body",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/email_templates/form', [

                'email_template_title'   => '',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

                'is_update'              => '0',

                'btn_submit'             => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_email_templates', 0);

        }
    #[Test]

    public function it_fails_to_update_without_email_template_title(): void

        {

            $this->__EmailTemplatesController_setUp();

            /**

             * POST /email_templates/form/{id}

             * {

             *     "email_template_title": "",

             *     "email_template_subject": "Subject",

             *     "email_template_body": "Body",

             *     "is_update": "1",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Will Not Change',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

            ]);



            /* Act */

            $response = $this->post('/email_templates/form/' . $id, [

                'email_template_title'   => '',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

                'is_update'              => '1',

                'btn_submit'             => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Will Not Change']);

        }



        // -------------------------------------------------------------------------

        // Edge cases

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_when_creating_a_duplicate_email_template(): void

        {

            $this->__EmailTemplatesController_setUp();

            /*

             * POST /email_templates/form (duplicate)

             * {

             *     "email_template_title": "Duplicate Template",

             *     "email_template_subject": "Subject",

             *     "email_template_body": "Body",

             *     "is_update": "0",

             *     "btn_submit": "1"

             * }

             */



            /* Arrange */

            $this->databaseInsert('ip_email_templates', [

                'email_template_title'   => 'Duplicate Template',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

            ]);



            /* Act */

            $response = $this->post('/email_templates/form', [

                'email_template_title'   => 'Duplicate Template',

                'email_template_subject' => 'Subject',

                'email_template_body'    => 'Body',

                'is_update'              => '0',

                'btn_submit'             => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Creating a duplicate email template must redirect with flash error.');

            $this->assertDatabaseCount('ip_email_templates', 1, ['email_template_title' => 'Duplicate Template']);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login_from_emailtemplatescontroller(): void

        {

            $this->__EmailTemplatesController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/email_templates');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __FilterAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_filters_invoices_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Filter Invoice Client']);

            $this->seedInvoice($clientId, ['invoice_number' => 'FILTER-MATCH-001']);

            $this->seedInvoice($clientId, ['invoice_number' => 'OTHER-002']);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => 'FILTER-MATCH-001']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'FILTER-MATCH-001');

            $this->assertResponseBodyNotContains($response, 'OTHER-002');

        }
    #[Test]

    public function it_does_not_expose_php_errors_when_filtering_invoices_without_a_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_invoices', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_treats_filter_invoices_query_as_a_literal_search_term(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $this->seedInvoice($clientId, ['invoice_number' => 'SAFE-001']);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => "' OR '1'='1"]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_quotes_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $this->databaseInsert('ip_quotes', [

                'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,

                'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),

                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),

                'quote_number'       => 'QUOFILTER-001', 'quote_url_key' => bin2hex(random_bytes(16)),

            ]);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_quotes', ['filter_query' => 'QUOFILTER-001']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'QUOFILTER-001');

        }
    #[Test]

    public function it_filters_clients_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $this->seedClient(['client_name' => 'FilterClientMatch']);

            $this->seedClient(['client_name' => 'OtherClient']);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_clients', ['filter_query' => 'FilterClientMatch']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'FilterClientMatch');

            $this->assertResponseBodyNotContains($response, 'OtherClient');

        }
    #[Test]

    public function it_filters_custom_fields_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_custom_fields', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_custom_values_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_custom_values', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_custom_values_field_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_custom_values_field', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_projects_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $this->databaseInsert('ip_projects', [

                'client_id' => $clientId, 'project_name' => 'FilterProjectMatch',

            ]);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_projects', ['filter_query' => 'FilterProjectMatch']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'FilterProjectMatch');

        }
    #[Test]

    public function it_filters_tasks_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_tasks', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_products_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_products', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_users_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_users', ['filter_query' => 'admin']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_families_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_families', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_recurring_invoices_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_invoices_recuring', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_online_logs_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_online_logs', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_archives_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_archives', ['filter_query' => 'anything']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_payments_by_query(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId, ['payment_note' => 'FilterPaymentMatch']);



            /* Act */

            $response = $this->ajax('POST', '/filter/ajax/filter_payments', ['filter_query' => 'FilterPaymentMatch']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'FilterPaymentMatch');

        }
    #[Test]

    public function it_requires_an_ajax_request_from_filterajaxcontroller(): void

        {

            $this->__FilterAjaxController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/filter/ajax/filter_invoices', ['filter_query' => 'x']);



            /* Assert */

            self::assertSame('', $response->body());

        }
    private string $importDir;
    protected function __ImportController_setUp(): void

        {



            $this->actingAsAdmin();



            $this->importDir = dirname(__DIR__, 3) . '/uploads/import';

            if ( ! is_dir($this->importDir)) {

                mkdir($this->importDir, 0777, true);

            }

        }
    protected function __ImportController_tearDown(): void

        {

            foreach (['clients.csv', 'evil.php', 'invoice_items.csv', 'invoices.csv', 'payments.csv'] as $file) {

                $path = $this->importDir . '/' . $file;

                if (is_file($path)) {

                    unlink($path);

                }

            }





        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_importcontroller(): void

        {

            $this->__ImportController_setUp();

            /* Arrange */

            /* (authenticated admin via __ImportController_setUp) */



            /* Act */

            $response = $this->get('/import');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_importcontroller(): void

        {

            $this->__ImportController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/import');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/import] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_lists_only_allowed_import_files(): void

        {

            $this->__ImportController_setUp();

            /* Arrange */

            file_put_contents($this->importDir . '/clients.csv', "client_name\nAllowed Client\n");

            file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');



            /* Act */

            $response = $this->get('/import/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'clients.csv');

            $this->assertResponseBodyNotContains($response, 'evil.php');

        }
    #[Test]

    public function it_ignores_unapproved_import_filenames_on_submit(): void

        {

            $this->__ImportController_setUp();

            /* Arrange */

            file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');



            /* Act */

            $response = $this->post('/import/form', [

                'files'      => ['evil.php', '../../bootstrap/kernel.php'],

                'btn_submit' => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Import submit must redirect after processing.');

            $this->assertDatabaseCount('ip_imports', 1);

            $this->assertDatabaseCount('ip_import_details', 0);

        }
    protected function __LayoutController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_layoutcontroller(): void

        {

            $this->__LayoutController_setUp();

            /* Arrange */

            /* (authenticated admin via __LayoutController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_layoutcontroller(): void

        {

            $this->__LayoutController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/dashboard] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __LoginSecurity_setUp(): void

        {



            $this->actingAsGuest();

        }



        // -------------------------------------------------------------------------

        // Error consolidation — all failure paths must look identical to the caller

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_after_a_login_attempt_with_an_unknown_email(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $payload = [

                'btn_login' => '1',

                'email'     => 'nobody@does-not-exist.example',

                'password'  => 'irrelevant-password',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'An unknown email must trigger a redirect, not a 200 with details. Got: ' . $response->statusCode()

            );

        }
    #[Test]

    public function it_redirects_after_a_login_attempt_with_a_wrong_password(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $this->databaseInsert('ip_users', [

                'user_name'          => 'Login Security Tester',

                'user_password'      => password_hash('correct-password', PASSWORD_BCRYPT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'loginsec@test.local',

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $payload = [

                'btn_login' => '1',

                'email'     => 'loginsec@test.local',

                'password'  => 'wrong-password',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'A wrong password must trigger a redirect, not reveal any account info. Got: ' . $response->statusCode()

            );

        }
    #[Test]

    public function it_does_not_reveal_whether_an_email_exists_in_error_responses(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $unknownPayload = [

                'btn_login' => '1',

                'email'     => 'ghost@no-account.example',

                'password'  => 'password123',

            ];



            $wrongPasswordPayload = [

                'btn_login' => '1',

                'email'     => 'admin@test.local',

                'password'  => 'definitely-wrong',

            ];



            /* Act */

            $unknownResponse       = $this->post('/sessions/login', $unknownPayload);

            $wrongPasswordResponse = $this->post('/sessions/login', $wrongPasswordPayload);



            /* Assert */

            // Both must redirect — not 200, not 403, not 401

            self::assertTrue(

                $unknownResponse->isRedirect(),

                'Unknown email must redirect, not produce a distinguishable response. Got: ' . $unknownResponse->statusCode()

            );

            self::assertTrue(

                $wrongPasswordResponse->isRedirect(),

                'Wrong password must redirect, not produce a distinguishable response. Got: ' . $wrongPasswordResponse->statusCode()

            );

            // Both must return the same status code so callers cannot distinguish them

            self::assertSame(

                $unknownResponse->statusCode(),

                $wrongPasswordResponse->statusCode(),

                'Unknown-email and wrong-password failures must produce identical HTTP status codes.'

            );

        }
    #[Test]

    public function it_does_not_expose_dashboard_content_after_a_failed_login(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $payload = [

                'btn_login' => '1',

                'email'     => 'nobody@no-account.example',

                'password'  => 'wrong',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertFalse(

                $response->contains('dashboard'),

                'A failed login response body must not contain dashboard content.'

            );

            self::assertFalse(

                $response->contains('invoice'),

                'A failed login response body must not contain application content.'

            );

        }



        // -------------------------------------------------------------------------

        // Inactive-account lockout — correct credentials must not authenticate

        // a deactivated user (Mdl_sessions::auth() user_active check).

        // -------------------------------------------------------------------------
    #[Test]

    public function it_denies_login_for_an_inactive_user_even_with_the_correct_password(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $email = 'inactive-' . bin2hex(random_bytes(4)) . '@test.local';

            $this->databaseInsert('ip_users', [

                'user_name'          => 'Inactive User',

                'user_password'      => password_hash('correct-password', PASSWORD_BCRYPT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => $email,

                'user_type'          => 1,

                'user_active'        => 0,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $payload = [

                'btn_login' => '1',

                'email'     => $email,

                'password'  => 'correct-password',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'A login attempt for an inactive user must redirect, not authenticate. Got: ' . $response->statusCode()

            );

            // Mdl_sessions::auth() must return false for an inactive account, which routes

            // through Sessions::authenticate()'s failure branch and records a login-log

            // failure entry. If the user_active check were removed, auth() would return

            // true on the correct password, the success branch would run instead, and

            // this row would never be written — making this assertion a real regression

            // guard for the auth-bypass fix rather than a redirect-only smoke test.

            $this->assertDatabaseHas('ip_login_log', [

                'login_name' => $email,

                'log_count'  => 1,

            ]);

        }
    #[Test]

    public function it_allows_login_for_an_active_user_with_the_correct_password(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            $email = 'active-' . bin2hex(random_bytes(4)) . '@test.local';

            $this->databaseInsert('ip_users', [

                'user_name'          => 'Active User',

                'user_password'      => password_hash('correct-password', PASSWORD_BCRYPT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => $email,

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $payload = [

                'btn_login' => '1',

                'email'     => $email,

                'password'  => 'correct-password',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'A successful login must redirect to the dashboard. Got: ' . $response->statusCode()

            );

            // No failure log for a genuinely successful login — contrast with the

            // inactive-user case above, which always writes one.

            $this->assertDatabaseMissing('ip_login_log', [

                'login_name' => $email,

            ]);

        }



        // -------------------------------------------------------------------------

        // IP-based rate limiting

        // -------------------------------------------------------------------------
    #[Test]

    public function it_blocks_login_attempts_after_exceeding_the_ip_rate_limit(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            // Seed the session with pre-existing failed attempts that fill the window.

            // LOGIN_IP_MAX_ATTEMPTS defaults to 20. We set 20 timestamps within the window.

            $now      = time();

            $attempts = array_fill(0, 20, $now - 30);

            $key      = 'login_attempts_ip_' . md5('127.0.0.1');



            $this->sessionData[$key] = $attempts;



            $payload = [

                'btn_login' => '1',

                'email'     => 'anyone@example.com',

                'password'  => 'anypassword',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'A rate-limited IP must be redirected back to login. Got: ' . $response->statusCode()

            );

            self::assertFalse(

                $response->contains('dashboard'),

                'A rate-limited login attempt must never reach the dashboard.'

            );

        }
    #[Test]

    public function it_allows_login_when_previous_attempts_have_expired_from_the_window(): void

        {

            $this->__LoginSecurity_setUp();

            /* Arrange */

            // 20 attempts, all older than the 15-minute window — they should be pruned.

            $expired  = time() - (16 * 60);

            $attempts = array_fill(0, 20, $expired);

            $key      = 'login_attempts_ip_' . md5('127.0.0.1');



            $this->sessionData[$key] = $attempts;



            $payload = [

                'btn_login' => '1',

                'email'     => 'nobody@no-account.example',

                'password'  => 'irrelevant',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            // Should redirect (failed credentials), but NOT be blocked by rate limiting.

            // Both rate-limited and credential-failed paths redirect — we verify it's not a 500.

            self::assertNotSame(

                500,

                $response->statusCode(),

                'Expired rate-limit attempts must not block a login attempt or cause a server error.'

            );

            self::assertTrue(

                $response->isRedirect(),

                'A login with expired-window attempts should redirect normally (not be rate-limited). Got: ' . $response->statusCode()

            );

        }
    protected function __MailerAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_shows_the_not_configured_view_for_invoice(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->get('/mailer/invoice/' . $invoiceId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_shows_the_not_configured_view_for_quote(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $quoteId  = $this->databaseInsert('ip_quotes', [

                'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,

                'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),

                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),

                'quote_number'       => 'MAIL-Q-001', 'quote_url_key' => bin2hex(random_bytes(16)),

            ]);



            /* Act */

            $response = $this->get('/mailer/quote/' . $quoteId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_on_cancel_for_send_invoice_even_when_unconfigured(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);



            /* Act */

            $response = $this->post('/mailer/send_invoice/' . $invoiceId, ['btn_cancel' => '1']);



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_does_not_send_or_mark_an_invoice_sent_when_mailer_is_not_configured(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);



            /* Act */

            $this->post('/mailer/send_invoice/' . $invoiceId, [

                'to_email' => 'client@test.local', 'from_email' => 'admin@test.local',

                'subject'  => 'Invoice', 'body' => 'Please pay',

            ]);



            /* Assert */

            $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 1]);

        }
    #[Test]

    public function it_redirects_on_cancel_for_send_quote_even_when_unconfigured(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $quoteId  = $this->databaseInsert('ip_quotes', [

                'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 1,

                'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),

                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),

                'quote_number'       => 'MAIL-Q-CANCEL', 'quote_url_key' => bin2hex(random_bytes(16)),

            ]);



            /* Act */

            $response = $this->post('/mailer/send_quote/' . $quoteId, ['btn_cancel' => '1']);



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_does_not_send_or_mark_a_quote_sent_when_mailer_is_not_configured(): void

        {

            $this->__MailerAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $quoteId  = $this->databaseInsert('ip_quotes', [

                'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 1,

                'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),

                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),

                'quote_number'       => 'MAIL-Q-NOOP', 'quote_url_key' => bin2hex(random_bytes(16)),

            ]);



            /* Act */

            $this->post('/mailer/send_quote/' . $quoteId, [

                'to_email' => 'client@test.local', 'from_email' => 'admin@test.local',

                'subject'  => 'Quote', 'body' => 'Here is your quote',

            ]);



            /* Assert */

            $this->assertDatabaseHas('ip_quotes', ['quote_id' => $quoteId, 'quote_status_id' => 1]);

        }
    protected function __MailerController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_mailercontroller(): void

        {

            $this->__MailerController_setUp();

            /* Arrange */

            /* (authenticated admin via __MailerController_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    private const TOKEN = 'ef260948cd51e1728a24ee672433e12757465c964269fd24d692b8980ecc2cf3';
    protected function __PasswordResetTokenExpiry_setUp(): void

        {



            $this->actingAsGuest();

        }
    #[Test]

    public function it_rejects_a_password_change_when_the_reset_token_has_expired(): void

        {

            $this->__PasswordResetTokenExpiry_setUp();

            /* Arrange: a reset token whose 15-minute lifetime elapsed 5 minutes ago. */

            $userId = $this->__PasswordResetTokenExpiry_seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

            $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);



            /* Act: submit the password-change POST with the correct (but expired) token. */

            $response = $this->post('/sessions/passwordreset', [

                'btn_new_password' => '1',

                'user_id'          => $userId,

                'token'            => self::TOKEN,

                'new_password'     => 'HackedPass123!',

                'new_passwordv'    => 'HackedPass123!',

            ]);



            /* Assert: the request is rejected and the stored password is unchanged. */

            self::assertTrue(

                $response->isRedirect(),

                'An expired-token password change must redirect, not render the form.'

            );



            $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

            self::assertSame(

                $before['user_password'],

                $after['user_password'],

                'An expired reset token must NOT be able to change the password (POST-side expiry bypass).'

            );

        }
    #[Test]

    public function it_clears_the_expired_token_after_a_rejected_password_change(): void

        {

            $this->__PasswordResetTokenExpiry_setUp();

            /* Arrange */

            $userId = $this->__PasswordResetTokenExpiry_seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));



            /* Act */

            $this->post('/sessions/passwordreset', [

                'btn_new_password' => '1',

                'user_id'          => $userId,

                'token'            => self::TOKEN,

                'new_password'     => 'HackedPass123!',

                'new_passwordv'    => 'HackedPass123!',

            ]);



            /* Assert: the burnt, expired token is cleared so it cannot be retried. */

            $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

            self::assertSame(

                '',

                (string) $after['user_passwordreset_token'],

                'An expired reset token must be cleared from the user row after a rejected attempt.'

            );

        }
    #[Test]

    public function it_allows_a_password_change_with_a_valid_unexpired_token(): void

        {

            $this->__PasswordResetTokenExpiry_setUp();

            /* Arrange: a token that is valid for another 10 minutes. */

            $userId = $this->__PasswordResetTokenExpiry_seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));

            $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);



            /* Act */

            $response = $this->post('/sessions/passwordreset', [

                'btn_new_password' => '1',

                'user_id'          => $userId,

                'token'            => self::TOKEN,

                'new_password'     => 'BrandNewPass123!',

                'new_passwordv'    => 'BrandNewPass123!',

            ]);



            /* Assert: the happy path still works — the password is changed. */

            self::assertTrue($response->isRedirect(), 'A valid-token password change must redirect.');



            $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

            self::assertNotSame(

                $before['user_password'],

                $after['user_password'],

                'A valid, unexpired reset token must still be able to change the password.'

            );

        }
    #[Test]

    public function it_allows_a_password_change_when_no_expiry_is_stored(): void

        {

            $this->__PasswordResetTokenExpiry_setUp();

            /* Arrange: a legacy token issued before the expiry column existed (NULL expiry). */

            $userId = $this->__PasswordResetTokenExpiry_seedUserWithResetToken(null);

            $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);



            /* Act */

            $response = $this->post('/sessions/passwordreset', [

                'btn_new_password' => '1',

                'user_id'          => $userId,

                'token'            => self::TOKEN,

                'new_password'     => 'BrandNewPass123!',

                'new_passwordv'    => 'BrandNewPass123!',

            ]);



            /* Assert: with no stored expiry there is nothing to enforce, so it succeeds. */

            self::assertTrue($response->isRedirect(), 'A no-expiry token password change must redirect.');



            $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

            self::assertNotSame(

                $before['user_password'],

                $after['user_password'],

                'A token with no stored expiry must still be able to change the password (backward compatibility).'

            );

        }
    #[Test]

    public function it_rejects_the_reset_link_when_the_token_has_expired(): void

        {

            $this->__PasswordResetTokenExpiry_setUp();

            /* Arrange */

            $this->__PasswordResetTokenExpiry_seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));



            /* Act: open the reset link (GET) carrying the expired token. */

            $response = $this->get('/sessions/passwordreset/' . self::TOKEN);



            /* Assert: the GET flow must not render the new-password form for an expired token. */

            self::assertTrue(

                $response->isRedirect(),

                'The reset link for an expired token must redirect, not render the new-password form.'

            );

            $this->assertResponseBodyNotContains($response, 'btn_new_password');

        }



        /**

         * Seed an active user holding a password-reset token with the given expiry.

         *

         * @param string|null $expiry UTC 'Y-m-d H:i:s', or null for a legacy token with no expiry

         */
    private function __PasswordResetTokenExpiry_seedUserWithResetToken(?string $expiry): int

        {

            return $this->databaseInsert('ip_users', [

                'user_name'                       => 'resettarget_' . bin2hex(random_bytes(3)),

                'user_email'                      => 'reset+' . bin2hex(random_bytes(3)) . '@example.com',

                'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),

                'user_psalt'                      => bin2hex(random_bytes(10)),

                'user_type'                       => 1,

                'user_active'                     => 1,

                'user_passwordreset_token'        => self::TOKEN,

                'user_passwordreset_token_expiry' => $expiry,

                'user_date_created'               => date('Y-m-d H:i:s'),

                'user_date_modified'              => date('Y-m-d H:i:s'),

            ]);

        }
    protected function __ReportsController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_reportscontroller(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $this->seedClient(['client_name' => 'Report Test Client']);



            /* Act */

            $response = $this->get('/reports/sales_by_client');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_generates_an_invoices_per_client_report_for_a_date_range_without_mutating_data(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $includedClientId = $this->seedClient(['client_name' => 'Included Report Client']);

            $excludedClientId = $this->seedClient(['client_name' => 'Excluded Report Client']);

            $this->seedInvoice($includedClientId, [

                'invoice_number'       => 'INV-REPORT-IN',

                'invoice_date_created' => '2026-01-15',

            ], [

                'invoice_total' => '125.00',

            ]);

            $this->seedInvoice($excludedClientId, [

                'invoice_number'       => 'INV-REPORT-OUT',

                'invoice_date_created' => '2025-01-15',

            ], [

                'invoice_total' => '250.00',

            ]);



            /* Act */

            $response = $this->post('/reports/invoices_per_client', [

                'from_date'  => '2026-01-01',

                'to_date'    => '2026-01-31',

                'btn_submit' => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            self::assertStringStartsWith('%PDF-', $response->body());

            $this->assertDatabaseCount('ip_clients', 2);

            $this->assertDatabaseCount('ip_invoices', 2);

        }
    #[Test]

    public function it_generates_a_sales_by_client_report(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Sales By Client Report']);

            $this->seedInvoice($clientId, ['invoice_date_created' => '2026-01-15'], ['invoice_total' => '75.00']);



            /* Act */

            $response = $this->post('/reports/sales_by_client', [

                'from_date' => '2026-01-01', 'to_date' => '2026-01-31', 'btn_submit' => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertStringStartsWith('%PDF-', $response->body());

        }
    #[Test]

    public function it_generates_a_payment_history_report(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $clientId  = $this->seedClient();

            $invoiceId = $this->seedInvoice($clientId);

            $this->seedPayment($invoiceId, ['payment_date' => '2026-01-15']);



            /* Act */

            $response = $this->post('/reports/payment_history', [

                'from_date' => '2026-01-01', 'to_date' => '2026-01-31', 'btn_submit' => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertStringStartsWith('%PDF-', $response->body());

        }
    #[Test]

    public function it_generates_an_invoice_aging_report(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $this->seedInvoice($clientId, ['invoice_date_due' => date('Y-m-d', strtotime('-10 days'))], ['invoice_balance' => '50.00']);



            /* Act */

            $response = $this->post('/reports/invoice_aging', ['btn_submit' => '1']);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertStringStartsWith('%PDF-', $response->body());

        }
    #[Test]

    public function it_generates_a_sales_by_year_report(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $this->seedInvoice($clientId, ['invoice_date_created' => '2026-01-15'], ['invoice_total' => '90.00']);



            /* Act */

            $response = $this->post('/reports/sales_by_year', [

                'from_date' => '2026-01-01', 'to_date' => '2026-12-31', 'btn_submit' => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertStringStartsWith('%PDF-', $response->body());

        }
    #[Test]

    public function it_redirects_a_guest_to_login_for_reports(): void

        {

            $this->__ReportsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/reports/sales_by_client');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    protected function __SessionsFeature_setUp(): void

        {



            $this->actingAsGuest();

        }
    #[Test]

    public function it_renders_the_login_page_with_a_200_status_when_unauthenticated(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/sessions/login');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_includes_a_login_form_on_the_sessions_login_page(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/sessions/login');



            /* Assert */

            $this->assertResponseBodyContains($response, '<form');



            self::assertTrue(

                $response->contains('email') || $response->contains('password'),

                'The login page must contain an email or password input field.'

            );

        }
    #[Test]

    public function it_does_not_render_the_admin_dashboard_when_unauthenticated(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf(

                    'An unauthenticated GET /dashboard must redirect to login. Got status [%d].',

                    $response->statusCode()

                )

            );

        }
    #[Test]

    public function it_redirects_to_login_when_post_credentials_are_missing(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $payload = ['btn_login' => '1', 'email' => '', 'password' => ''];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'Submitting empty credentials must redirect back to login, not crash.'

            );

        }
    #[Test]

    public function it_redirects_to_login_with_wrong_credentials(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $payload = [

                'btn_login' => '1',

                'email'     => 'nobody@nonexistent.example',

                'password'  => 'wrongpassword',

            ];



            /* Act */

            $response = $this->post('/sessions/login', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'Invalid credentials must redirect (not 200 with error, not 500).'

            );



            self::assertFalse(

                $response->contains('dashboard'),

                'A failed login must never redirect to the dashboard.'

            );

        }
    #[Test]

    public function it_renders_the_password_reset_form_with_a_200_status(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/sessions/passwordreset');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_redirects_to_login_when_a_nonexistent_email_is_submitted_to_password_reset(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $payload = ['btn_reset' => '1', 'email' => 'nobody_exists_' . time() . '@nonexistent.example'];



            /* Act */

            $response = $this->post('/sessions/passwordreset', $payload);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'Password reset with nonexistent email must redirect (enumeration-safe response).'

            );

        }
    #[Test]

    public function it_does_not_reveal_whether_the_email_exists_in_the_reset_response(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $ts = time();



            /* Act */

            $responseReal = $this->post('/sessions/passwordreset', [

                'btn_reset' => '1',

                'email'     => 'nobody_real_' . $ts . '@nonexistent.example',

            ]);

            $responseFake = $this->post('/sessions/passwordreset', [

                'btn_reset' => '1',

                'email'     => 'nobody_fake_' . $ts . '@nonexistent.example',

            ]);



            /* Assert */

            self::assertSame(

                $responseReal->statusCode(),

                $responseFake->statusCode(),

                'Password reset must return the same HTTP status for existing and nonexistent emails (enumeration guard).'

            );

        }
    #[Test]

    public function it_rejects_a_password_reset_token_containing_non_alphanumeric_characters(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $maliciousToken = '../etc/passwd';



            /* Act */

            $response = $this->get('/sessions/passwordreset/' . rawurlencode($maliciousToken));



            /* Assert */

            self::assertThat(

                $response->statusCode(),

                self::logicalOr(

                    self::equalTo(302),

                    self::equalTo(301),

                    self::equalTo(307),

                    self::equalTo(404)

                ),

                sprintf(

                    'A non-alphanumeric reset token must be rejected with a redirect or 404. Got [%d].',

                    $response->statusCode()

                )

            );



            $this->assertResponseBodyNotContains($response, 'etc/passwd');

            $this->assertResponseBodyNotContains($response, 'root:');

        }
    #[Test]

    public function it_redirects_to_login_when_an_unknown_valid_format_token_is_used(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $unknownToken = bin2hex(random_bytes(16));



            /* Act */

            $response = $this->get('/sessions/passwordreset/' . $unknownToken);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf(

                    'An unknown but format-valid reset token must redirect to login. Got status [%d].',

                    $response->statusCode()

                )

            );

        }
    #[Test]

    public function it_destroys_the_session_and_redirects_to_login_on_logout(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */

            $this->actingAsAdmin();



            /* Act */

            $response = $this->get('/sessions/logout');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('GET /sessions/logout must redirect. Got status [%d].', $response->statusCode())

            );



            // Location header is not available in PHP CLI SAPI; verify redirect status only.

            $redirectTarget = $response->redirectUrl() ?? '';



            if ($redirectTarget !== '') {

                self::assertTrue(

                    str_contains($redirectTarget, 'sessions/login') || str_contains($redirectTarget, 'login'),

                    sprintf('Logout must redirect to the login page. Redirect URL was [%s].', $redirectTarget)

                );

            }

        }
    #[Test]

    public function it_does_not_expose_php_errors_on_the_login_page(): void

        {

            $this->__SessionsFeature_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/sessions/login');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    private StubSessionsSecurity $security;
    protected function __SessionsSecurity_setUp(): void

        {



            $this->security = new StubSessionsSecurity(baseUrl: 'https://invoiceplane.example.com/');

        }



        /**

         * @return array<string, array{0: string, 1: bool}>

         */
    public static function __SessionsSecurity_expiryFormatProvider(): array

        {

            return [

                // description => [stored expiry string, accepted?]

                'canonical timestamp'                  => ['2020-01-01 12:00:00', true],

                'canonical boundary timestamp'         => ['2099-12-31 23:59:59', true],

                'garbage time-only string'             => ['25:99:99', false],

                'out-of-range month and day'           => ['2020-13-40 00:00:00', false],

                'non-date string'                      => ['not-a-date', false],

                'non-canonical single-digit fields'    => ['2026-8-10 9:05:07', false],

                'non-canonical double space'           => ['2099-01-01  12:00:00', false],

                'zero date (right shape, unreal date)' => ['0000-00-00 00:00:00', false],

            ];

        }
    #[Test]

    public function it_allows_a_referer_from_the_same_base_url(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->getSafeReferer('https://invoiceplane.example.com/sessions/login');



            /* Assert */

            self::assertSame(

                'https://invoiceplane.example.com/sessions/login',

                $result,

                'A referer from the same domain must be returned as-is.'

            );

        }
    #[Test]

    public function it_rejects_a_referer_from_an_external_domain(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->getSafeReferer('https://evil.example.com/steal');



            /* Assert */

            self::assertSame(

                'sessions/passwordreset',

                $result,

                'An external referer must be replaced by the safe default.'

            );

        }
    #[Test]

    public function it_returns_the_safe_default_when_referer_is_empty(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->getSafeReferer('');



            /* Assert */

            self::assertSame(

                'sessions/passwordreset',

                $result,

                'An empty referer must return the safe default URL.'

            );

        }
    #[Test]

    public function it_rejects_a_referer_that_starts_with_a_double_slash(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->getSafeReferer('//evil.example.com/steal');



            /* Assert */

            self::assertSame(

                'sessions/passwordreset',

                $result,

                'A protocol-relative referer to an external domain must be rejected.'

            );

        }
    #[Test]

    public function it_accepts_an_alphanumeric_password_reset_token(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertTrue(

                $this->security->isValidTokenFormat('abc123XYZ'),

                'A purely alphanumeric token must pass format validation.'

            );

        }
    #[Test]

    public function it_accepts_a_hex_token_of_typical_length(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $token = bin2hex(random_bytes(16));



            /* Assert */

            self::assertTrue(

                $this->security->isValidTokenFormat($token),

                'A 32-character hex token (typical reset token) must pass format validation.'

            );

        }
    #[Test]

    public function it_rejects_a_token_containing_a_path_traversal_sequence(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertFalse(

                $this->security->isValidTokenFormat('../etc/passwd'),

                'A token containing [../] must fail format validation.'

            );

        }
    #[Test]

    public function it_rejects_a_token_containing_a_slash(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertFalse(

                $this->security->isValidTokenFormat('valid/invalid'),

                'A token containing a forward slash must fail format validation.'

            );

        }
    #[Test]

    public function it_rejects_a_token_containing_special_characters(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertFalse(

                $this->security->isValidTokenFormat('token<script>'),

                'A token containing special characters must fail format validation.'

            );

        }
    #[Test]

    public function it_considers_an_expired_token_as_expired(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $expiry = new DateTime('-1 minute', new DateTimeZone('UTC'));



            /* Assert */

            self::assertTrue(

                $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),

                'A token with an expiry timestamp in the past must be considered expired.'

            );

        }
    #[Test]

    public function it_considers_a_future_token_as_not_expired(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $expiry = new DateTime('+15 minutes', new DateTimeZone('UTC'));



            /* Assert */

            self::assertFalse(

                $this->security->isTokenExpired($expiry->format('Y-m-d H:i:s')),

                'A token with an expiry timestamp in the future must NOT be considered expired.'

            );

        }
    #[Test]

    public function it_enforces_the_max_expiry_minutes_cap_of_1440(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $requested = $this->security->clampExpiryMinutes(9999);



            /* Assert */

            self::assertSame(

                15,

                $requested,

                'An out-of-range PASSWORD_RESET_TOKEN_EXPIRY_MINUTES must fall back to the 15-minute default.'

            );

        }
    #[Test]

    public function it_allows_a_valid_expiry_minutes_value_within_range(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->clampExpiryMinutes(30);



            /* Assert */

            self::assertSame(

                30,

                $result,

                'A value of 30 minutes is within 1-1440 and must be returned unchanged.'

            );

        }
    #[Test]

    public function it_rejects_a_zero_expiry_minutes_and_falls_back_to_default(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $result = $this->security->clampExpiryMinutes(0);



            /* Assert */

            self::assertSame(

                15,

                $result,

                'An expiry_minutes value of 0 is invalid and must fall back to the 15-minute default.'

            );

        }
    #[Test]

    public function it_detects_curl_as_a_bot_user_agent(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertTrue(

                $this->security->isBotUserAgent('curl/7.85.0'),

                'curl must be identified as a bot/automated tool.'

            );

        }
    #[Test]

    public function it_detects_python_requests_as_a_bot_user_agent(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertTrue(

                $this->security->isBotUserAgent('python-requests/2.28.0'),

                'python-requests must be identified as a bot.'

            );

        }
    #[Test]

    public function it_detects_an_empty_user_agent_as_a_bot(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */



            /* Assert */

            self::assertTrue(

                $this->security->isBotUserAgent(''),

                'An empty user-agent must be treated as a bot/automated request.'

            );

        }
    #[Test]

    public function it_does_not_flag_a_normal_browser_user_agent_as_a_bot(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */



            /* Act */

            $browser = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36';



            /* Assert */

            self::assertFalse(

                $this->security->isBotUserAgent($browser),

                'A standard browser user-agent must NOT be flagged as a bot.'

            );

        }
    #[Test]

    public function it_removes_attempts_outside_the_rate_limit_time_window(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */

            $now        = time();

            $windowSecs = 3600;



            $attempts = [

                $now - 7200,

                $now - 5000,

                $now - 1800,

                $now - 100,

                $now - 30,

            ];



            /* Act */

            $filtered = $this->security->filterAttemptsWithinWindow($attempts, $windowSecs);



            /* Assert */

            self::assertCount(

                3,

                $filtered,

                'Only attempts within the last 3600 seconds must be retained.'

            );

        }
    #[Test]

    public function it_considers_the_ip_rate_limited_when_attempt_count_meets_the_threshold(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */

            $now      = time();

            $attempts = array_fill(0, 5, $now - 10);



            /* Act */

            $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);



            /* Assert */

            self::assertTrue(

                $isLimited,

                'Exactly 5 attempts against a max of 5 must trigger the rate limit.'

            );

        }
    #[Test]

    public function it_does_not_rate_limit_when_attempt_count_is_below_the_threshold(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */

            $now      = time();

            $attempts = array_fill(0, 4, $now - 10);



            /* Act */

            $isLimited = $this->security->isRateLimited(attempts: $attempts, maxAttempts: 5, windowSeconds: 3600);



            /* Assert */

            self::assertFalse(

                $isLimited,

                '4 attempts against a max of 5 must NOT trigger the rate limit.'

            );

        }
    #[Test]

    public function it_accepts_only_canonical_password_reset_expiry_strings(): void

        {

            $this->__SessionsSecurity_setUp();

            /* Arrange */

            foreach (self::__SessionsSecurity_expiryFormatProvider() as [$expiry, $accepted]) {

                /* Act */

                $result = $this->security->isCanonicalExpiry($expiry);



                /* Assert */

                self::assertSame(

                    $accepted,

                    $result,

                    sprintf('Expiry string "%s" must be %s.', $expiry, $accepted ? 'accepted' : 'rejected')

                );

            }

        }
    protected function __SettingsAjaxAndVersions_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_generates_a_16_character_hex_cron_key(): void

        {

            $this->__SettingsAjaxAndVersions_setUp();

            /* Arrange */

            /* Act */

            $response = $this->ajax('GET', '/settings/ajax/get_cron_key', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertMatchesRegularExpression('/^[0-9a-f]{16}$/', trim($response->body()));

        }
    #[Test]

    public function it_generates_a_different_cron_key_on_each_call(): void

        {

            $this->__SettingsAjaxAndVersions_setUp();

            /* Arrange */

            /* Act */

            $first  = trim($this->ajax('GET', '/settings/ajax/get_cron_key', [])->body());

            $second = trim($this->ajax('GET', '/settings/ajax/get_cron_key', [])->body());



            /* Assert */

            self::assertNotSame($first, $second);

        }
    #[Test]

    public function it_requires_an_ajax_request_for_get_cron_key(): void

        {

            $this->__SettingsAjaxAndVersions_setUp();

            /* Arrange */

            /* Act */

            $response = $this->get('/settings/ajax/get_cron_key');



            /* Assert */

            self::assertSame('', $response->body());

        }
    #[Test]

    public function it_lists_applied_versions(): void

        {

            $this->__SettingsAjaxAndVersions_setUp();

            /* Arrange */

            /* Act */

            $response = $this->get('/settings/versions');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_denies_versions_access_to_a_guest(): void

        {

            $this->__SettingsAjaxAndVersions_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/settings/versions');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    protected function __SettingsController_setUp(): void

        {



            $this->actingAsAdmin();

            $this->withEnvironment([

                'SETUP_COMPLETED' => 'true',

                'DISABLE_SETUP'   => 'true',

            ]);

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_settingscontroller(): void

        {

            $this->__SettingsController_setUp();

            /* Arrange */

            /* (authenticated admin via __SettingsController_setUp) */



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_settingscontroller(): void

        {

            $this->__SettingsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/settings] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_warns_admins_when_setup_security_flags_are_not_enabled(): void

        {

            $this->__SettingsController_setUp();

            /* Arrange */

            $this->withEnvironment([

                'SETUP_COMPLETED' => 'true',

                'DISABLE_SETUP'   => 'false',

            ]);



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Security Warning');

            $this->assertResponseBodyContains($response, 'DISABLE_SETUP is set to false');

            $this->assertResponseBodyContains($response, 'Please edit ipconfig.php');

        }
    #[Test]

    public function it_warns_when_a_saved_custom_invoice_template_is_missing_from_ipconfig(): void

        {

            $this->__SettingsController_setUp();

            /* Arrange */

            $this->__SettingsController_setSetting('pdf_invoice_template', 'Legacy Custom Invoice');



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Custom template configuration required');

            $this->assertResponseBodyContains($response, 'CUSTOM_INVOICE_TEMPLATES_PDF');

            $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');

            $this->assertResponseBodyContains($response, 'CUSTOM_TEMPLATES_FOLDER');

        }
    #[Test]

    public function it_does_not_warn_when_a_saved_custom_invoice_template_is_allowlisted_in_ipconfig(): void

        {

            $this->__SettingsController_setUp();

            /* Arrange */

            $this->__SettingsController_setSetting('pdf_invoice_template', 'Legacy Custom Invoice');

            $this->withEnvironment([

                'CUSTOM_INVOICE_TEMPLATES_PDF' => 'Legacy Custom Invoice',

            ]);



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyNotContains($response, 'Custom template configuration required');

            $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');

        }
    private function __SettingsController_setSetting(string $key, string $value): void

        {

            $this->databaseInsertOrIgnore('ip_settings', [

                'setting_key'   => $key,

                'setting_value' => '',

            ]);

            $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);

        }
    #[Test]

    public function it_denies_http_access_to_the_cli_controller(): void

        {

            /* Arrange */

            /* Act */

            $response = $this->get('/setup/cli/create_default_user');



            /* Assert */

            $this->assertResponseStatusCode($response, 403);

        }
    #[Test]

    public function it_creates_a_default_admin_user_when_none_exist(): void

        {

            /* Arrange */

            // The baseline seed always includes an admin user; empty it so this

            // genuinely exercises the "no users exist yet" path.

            $this->databaseTruncate('ip_users');

            $email = 'cli-default-' . bin2hex(random_bytes(4)) . '@test.local';



            /* Act */

            [$exitCode, $stdout, $stderr] = $this->__SetupCliController_runCli('setup/cli/create_default_user', [

                'DEFAULT_ADMIN_EMAIL'    => $email,

                'DEFAULT_ADMIN_PASSWORD' => 'a-fixed-test-password',

                'DEFAULT_ADMIN_NAME'     => 'CLI Test Admin',

            ]);



            /* Assert */

            self::assertSame(0, $exitCode, "CLI exited non-zero.\nSTDOUT: {$stdout}\nSTDERR: {$stderr}");

            self::assertStringContainsString('Default admin user created', $stdout);

            $this->resetDatabaseConnection();

            $this->assertDatabaseHas('ip_users', ['user_email' => $email, 'user_type' => 1]);

        }
    #[Test]

    public function it_skips_creating_a_default_admin_user_when_one_already_exists(): void

        {

            /* Arrange: the baseline seed already provides an admin user (user_id 1) */

            /* Act */

            [$exitCode, $stdout, $stderr] = $this->__SetupCliController_runCli('setup/cli/create_default_user');



            /* Assert */

            self::assertSame(0, $exitCode, "CLI exited non-zero.\nSTDOUT: {$stdout}\nSTDERR: {$stderr}");

            self::assertStringContainsString('already exist', $stdout);

            $this->assertDatabaseCount('ip_users', 1);

        }
    private function __SetupCliController_runCli(string $route, array $env = []): array

        {

            $repoRoot = dirname(__DIR__, 3);

            $command  = sprintf('%s %s %s', escapeshellarg(PHP_BINARY), escapeshellarg($repoRoot . '/public/index.php'), escapeshellarg($route));



            $process = proc_open(

                $command,

                [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],

                $pipes,

                $repoRoot,

                array_merge(['CI_ENV' => 'testing', 'PATH' => getenv('PATH') ?: '/usr/bin:/bin'], $env),

            );



            self::assertIsResource($process, 'Unable to start CLI subprocess.');



            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);

            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);

            fclose($pipes[2]);

            $exitCode = proc_close($process);



            return [$exitCode, $stdout, $stderr];

        }
    protected function __SetupController_setUp(): void

        {



        }
    #[Test]
    #[Group('smoke')]

    public function it_allows_the_setup_flow_when_setup_is_explicitly_unlocked(): void

        {

            $this->__SetupController_setUp();

            /* Arrange */

            $this->withEnvironment([

                'SETUP_COMPLETED' => 'false',

                'DISABLE_SETUP'   => 'false',

            ]);



            /* Act */

            $response = $this->get('/setup/language');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'setup');

        }
    #[Test]
    #[Group('security')]

    public function it_locks_every_http_setup_route_after_setup_is_completed(): void

        {

            $this->__SetupController_setUp();

            /* Arrange */

            $this->withEnvironment([

                'SETUP_COMPLETED' => 'true',

                'DISABLE_SETUP'   => 'false',

            ]);



            $setupRoutes = [

                '/setup',

                '/setup/language',

                '/setup/prerequisites',

                '/setup/configure_database',

                '/setup/install_tables',

                '/setup/upgrade_tables',

                '/setup/create_user',

                '/setup/calculation_info',

                '/setup/complete',

            ];



            foreach ($setupRoutes as $route) {

                /* Act */

                $response = $this->get($route);



                /* Assert */

                self::assertSame(

                    403,

                    $response->statusCode(),

                    "Completed installations must block HTTP setup route [{$route}]."

                );

            }

        }
    #[Test]

    public function it_redirects_direct_setup_steps_to_the_wizard_when_setup_is_unlocked(): void

        {

            $this->__SetupController_setUp();

            /* Arrange */

            $this->withEnvironment([

                'SETUP_COMPLETED' => 'false',

                'DISABLE_SETUP'   => 'false',

            ]);



            /* Act */

            $response = $this->get('/setup/upgrade_tables');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unlocked direct setup step should redirect into the wizard. Got [%d].', $response->statusCode())

            );

            self::assertNotSame(403, $response->statusCode());

        }
    protected function __TaxRatesController_setUp(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_tax_rates(): void

        {

            $this->__TaxRatesController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_tax_rates', [

                'tax_rate_name'    => 'Listed VAT',

                'tax_rate_percent' => '21.00',

            ]);



            /* Act */

            $response = $this->get('/tax_rates');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed VAT');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_tax_rate_form(): void

        {

            $this->__TaxRatesController_setUp();

            /* Arrange */



            /* Act */

            $response = $this->get('/tax_rates/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_creates_a_tax_rate(): void

        {

            $this->__TaxRatesController_setUp();

            /**

             * POST /tax_rates/form

             * {

             *     "tax_rate_name": "Standard VAT",

             *     "tax_rate_percent": "21.00",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/tax_rates/form', [

                'tax_rate_name'    => 'Standard VAT',

                'tax_rate_percent' => '21.00',

                'btn_submit'       => '1',

            ]);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'tax_rates');

            $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Standard VAT']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_tax_rate_name(): void

        {

            $this->__TaxRatesController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_tax_rates', [

                'tax_rate_name'    => 'Editable VAT',

                'tax_rate_percent' => '9.00',

            ]);



            /* Act */

            $response = $this->get('/tax_rates/form/' . $id);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertResponseBodyContains($response, 'Editable VAT');

        }
    #[Test]

    public function it_updates_a_tax_rate(): void

        {

            $this->__TaxRatesController_setUp();

            /**

             * POST /tax_rates/form/{id}

             * {

             *     "tax_rate_name": "Renamed VAT",

             *     "tax_rate_percent": "15.00",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_tax_rates', [

                'tax_rate_name'    => 'Original VAT',

                'tax_rate_percent' => '9.00',

            ]);



            /* Act */

            $response = $this->post('/tax_rates/form/' . $id, [

                'tax_rate_name'    => 'Renamed VAT',

                'tax_rate_percent' => '15.00',

                'btn_submit'       => '1',

            ]);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'tax_rates');

            $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Renamed VAT', 'tax_rate_percent' => '15.00']);

            $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Original VAT']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_tax_rate(): void

        {

            $this->__TaxRatesController_setUp();

            /* Arrange */

            $id = $this->databaseInsert('ip_tax_rates', [

                'tax_rate_name'    => 'Deletable VAT',

                'tax_rate_percent' => '5.00',

            ]);

            $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Deletable VAT']);



            /* Act */

            $response = $this->post('/tax_rates/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'tax_rates');

            $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Deletable VAT']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_tax_rate_name(): void

        {

            $this->__TaxRatesController_setUp();

            /**

             * POST /tax_rates/form

             * {

             *     "tax_rate_name": "",

             *     "tax_rate_percent": "21.00",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/tax_rates/form', [

                'tax_rate_name'    => '',

                'tax_rate_percent' => '21.00',

                'btn_submit'       => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseCount('ip_tax_rates', 0);

        }
    #[Test]

    public function it_fails_to_create_without_tax_rate_percent(): void

        {

            $this->__TaxRatesController_setUp();

            /**

             * POST /tax_rates/form

             * {

             *     "tax_rate_name": "Incomplete VAT",

             *     "tax_rate_percent": "",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */



            /* Act */

            $response = $this->post('/tax_rates/form', [

                'tax_rate_name'    => 'Incomplete VAT',

                'tax_rate_percent' => '',

                'btn_submit'       => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Incomplete VAT']);

        }
    #[Test]

    public function it_fails_to_update_without_tax_rate_name(): void

        {

            $this->__TaxRatesController_setUp();

            /**

             * POST /tax_rates/form/{id}

             * {

             *     "tax_rate_name": "",

             *     "tax_rate_percent": "21.00",

             *     "btn_submit": "1"

             * }.

             */



            /* Arrange */

            $id = $this->databaseInsert('ip_tax_rates', [

                'tax_rate_name'    => 'Will Not Change',

                'tax_rate_percent' => '9.00',

            ]);



            /* Act */

            $response = $this->post('/tax_rates/form/' . $id, [

                'tax_rate_name'    => '',

                'tax_rate_percent' => '21.00',

                'btn_submit'       => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Will Not Change']);

        }



        // -------------------------------------------------------------------------

        // Guest redirect — always last

        // -------------------------------------------------------------------------
    #[Test]

    public function it_redirects_a_guest_to_login_from_taxratescontroller(): void

        {

            $this->__TaxRatesController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/tax_rates');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function __TaxRatesService_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_taxratesservice(): void

        {

            $this->__TaxRatesService_setUp();

            /* Arrange */

            /* (authenticated admin via __TaxRatesService_setUp) */



            /* Act */

            $response = $this->get('/tax_rates');



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

                sprintf('[GET /tax_rates] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_from_taxratesservice(): void

        {

            $this->__TaxRatesService_setUp();

            /* Arrange */

            /* (authenticated admin via __TaxRatesService_setUp) */



            /* Act */

            $response = $this->get('/tax_rates');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_taxratesservice(): void

        {

            $this->__TaxRatesService_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/tax_rates');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/tax_rates] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __UploadController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_uploadcontroller(): void

        {

            $this->__UploadController_setUp();

            /* Arrange */

            /* (authenticated admin via __UploadController_setUp) */



            /* Act */

            $response = $this->get('/import');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_uploadcontroller(): void

        {

            $this->__UploadController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/import');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/import] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __UserClientsService_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_userclientsservice(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            /* (authenticated admin via __UserClientsService_setUp) */



            /* Act */

            $response = $this->get('/user_clients');



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

                sprintf('[GET /user_clients] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_userclientsservice(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/user_clients');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/user_clients] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_redirects_to_a_real_route_when_create_is_cancelled(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            // User_clients::create() redirects to 'user_clients/field/' . $user_id on

            // cancel, but the controller has no field() method — only user($id),

            // which is what renders the user_clients/field.php view. The test

            // harness cannot capture the Location header under CLI SAPI (see

            // SessionsFeatureTest), so this is verified at the source level: the

            // redirect target string must be a route that actually resolves.

            $controllerFile = APPPATH . 'modules/user_clients/controllers/User_clients.php';

            $content        = file_get_contents($controllerFile);



            /* Act */

            $routeStillPointsAtMissingFieldMethod = str_contains($content, "redirect('user_clients/field/");



            /* Assert */

            self::assertFalse(

                $routeStillPointsAtMissingFieldMethod,

                "create()'s cancel path must not redirect to user_clients/field/ — "

                . 'that route does not exist (the controller method is user(), not field()).'

            );

        }
    #[Test]

    public function it_shows_the_user_client_assignment_page_for_a_real_user(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $userId = $this->__UserClientsService_seedNonAdminUser();



            /* Act */

            $response = $this->get('/user_clients/user/' . $userId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_for_an_unknown_user_id(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            /* Act */

            $response = $this->get('/user_clients/user/999999');



            /* Assert */

            self::assertTrue($response->isRedirect());

        }
    #[Test]

    public function it_assigns_a_client_to_a_user(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $userId   = $this->__UserClientsService_seedNonAdminUser();

            $clientId = $this->seedClient();



            /* Act */

            $response = $this->post('/user_clients/create/' . $userId, ['user_id' => (string) $userId, 'client_id' => (string) $clientId]);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        }
    #[Test]

    public function it_fails_to_assign_a_client_without_client_id(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $userId = $this->__UserClientsService_seedNonAdminUser();



            /* Act */

            $this->post('/user_clients/create/' . $userId, ['user_id' => (string) $userId]);



            /* Assert */

            $this->assertDatabaseCount('ip_user_clients', 0);

        }
    #[Test]

    public function it_deletes_a_user_client_assignment(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $userId       = $this->__UserClientsService_seedNonAdminUser();

            $clientId     = $this->seedClient();

            $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $response = $this->post('/user_clients/delete/' . $userClientId);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $userClientId]);

        }
    #[Test]

    public function it_does_not_delete_a_user_client_assignment_on_a_non_post_request(): void

        {

            $this->__UserClientsService_setUp();

            /* Arrange */

            $userId       = $this->__UserClientsService_seedNonAdminUser();

            $clientId     = $this->seedClient();

            $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $this->get('/user_clients/delete/' . $userClientId);



            /* Assert */

            $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $userClientId]);

        }
    private function __UserClientsService_seedNonAdminUser(): int

        {

            return $this->databaseInsert('ip_users', [

                'user_name'     => 'Assignable User', 'user_email' => 'assignable-' . bin2hex(random_bytes(4)) . '@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 2, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

        }
    protected function __UsersController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_userscontroller(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_users', [

                'user_name'          => 'Alice Tester',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'alice@test.local',

                'user_type'          => 0,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $response = $this->get('/users');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Alice Tester');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_userscontroller(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/users');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/users] must redirect. Got [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_creates_a_user_with_a_hashed_password(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/users/form', [

                'user_type'      => '2',

                'user_email'     => 'new-user@test.local',

                'user_name'      => 'New User',

                'user_password'  => 'correct horse battery staple',

                'user_passwordv' => 'correct horse battery staple',

                'user_language'  => 'system',

                'user_company'   => 'Example Co',

                'btn_submit'     => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful user create must redirect.');



            $user = $this->databaseFetchOne('ip_users', ['user_email' => 'new-user@test.local']);

            self::assertNotNull($user);

            self::assertSame('2', (string) $user['user_type']);

            self::assertNotSame('correct horse battery staple', $user['user_password']);

            self::assertTrue(password_verify('correct horse battery staple', $user['user_password']));

        }
    #[Test]

    public function it_does_not_create_a_user_when_password_confirmation_does_not_match(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            /* Act */

            $response = $this->post('/users/form', [

                'user_type'      => '2',

                'user_email'     => 'mismatch@test.local',

                'user_name'      => 'Mismatch User',

                'user_password'  => 'correct horse battery staple',

                'user_passwordv' => 'different password',

                'user_language'  => 'system',

                'btn_submit'     => '1',

            ]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

            $this->assertDatabaseMissing('ip_users', ['user_email' => 'mismatch@test.local']);

        }
    #[Test]

    public function it_updates_a_user_without_mass_assigning_protected_fields(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $originalSalt = bin2hex(random_bytes(10));

            $userId       = $this->databaseInsert('ip_users', [

                'user_name'          => 'Editable User',

                'user_password'      => password_hash('existing-secret', PASSWORD_DEFAULT),

                'user_psalt'         => $originalSalt,

                'user_email'         => 'editable@test.local',

                'user_type'          => 2,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $response = $this->post('/users/form/' . $userId, [

                'user_type'     => '2',

                'user_email'    => 'renamed@test.local',

                'user_name'     => 'Renamed User',

                'user_language' => 'system',

                'user_active'   => '0',

                'user_psalt'    => 'attacker-controlled-salt',

                'btn_submit'    => '1',

            ]);



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Successful user update must redirect.');



            $user = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

            self::assertNotNull($user);

            self::assertSame('Renamed User', $user['user_name']);

            self::assertSame('renamed@test.local', $user['user_email']);

            self::assertSame('1', (string) $user['user_active']);

            self::assertSame($originalSalt, $user['user_psalt']);

        }
    #[Test]
    #[Group('security')]

    public function it_prevents_a_non_primary_admin_from_changing_another_users_password(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $attackerId = $this->databaseInsert('ip_users', [

                'user_name'          => 'Attacker Admin',

                'user_password'      => password_hash('attacker-secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'attacker-admin@test.local',

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $victimHash = password_hash('victim-secret', PASSWORD_DEFAULT);

            $victimId   = $this->databaseInsert('ip_users', [

                'user_name'          => 'Victim Admin',

                'user_password'      => $victimHash,

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'victim-admin@test.local',

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $this->actingAsAdmin($attackerId);



            /* Act */

            $response = $this->post('/users/change_password/' . $victimId, [

                'user_password'         => 'attacker-chosen-password',

                'user_password_confirm' => 'attacker-chosen-password',

                'btn_submit'            => '1',

            ]);



            /* Assert */

            self::assertSame(403, $response->statusCode());



            $victim = $this->databaseFetchOne('ip_users', ['user_id' => $victimId]);

            self::assertNotNull($victim);

            self::assertSame(

                $victimHash,

                $victim['user_password'],

                'A non-primary admin must not be able to mutate another user password hash.'

            );

        }
    #[Test]

    public function it_deletes_a_user_client_assignment_from_userscontroller(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $userId = $this->databaseInsert('ip_users', [

                'user_name'     => 'Delete Target', 'user_email' => 'delete-target@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 2, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

            $clientId     = $this->seedClient();

            $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $response = $this->post('/users/delete_user_client/' . $userId . '/' . $userClientId);



            /* Assert */

            self::assertTrue($response->isRedirect());

            $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $userClientId]);

        }
    #[Test]

    public function it_does_not_delete_a_user_client_assignment_on_a_non_post_request_from_userscontroller(): void

        {

            $this->__UsersController_setUp();

            /* Arrange */

            $userId = $this->databaseInsert('ip_users', [

                'user_name'     => 'Delete Target Get', 'user_email' => 'delete-target-get@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 2, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

            $clientId     = $this->seedClient();

            $userClientId = $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $this->get('/users/delete_user_client/' . $userId . '/' . $userClientId);



            /* Assert */

            $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $userClientId]);

        }
    #[Test]

    public function it_renders_the_edit_form_for_a_user_with_assigned_clients(): void

        {

            $this->__UsersController_setUp();

            /* Arrange: form.php's 'user_clients' layout data is consumed by a

             * separate AJAX-loaded tab (users/ajax/load_user_client_table), not

             * rendered inline — this just proves the initial page load itself

             * doesn't crash while that data is being built (it did before the

             * user_clients/mdl_user_clients load-path fixes above). */

            $userId = $this->databaseInsert('ip_users', [

                'user_name'     => 'Form Client Owner', 'user_email' => 'form-client-owner@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 2, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

            $clientId = $this->seedClient(['client_name' => 'Form Assigned Client Marker']);

            $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $response = $this->get('/users/form/' . $userId);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    protected function __UsersService_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_usersservice(): void

        {

            $this->__UsersService_setUp();

            /* Arrange */

            /* (authenticated admin via __UsersService_setUp) */



            /* Act */

            $response = $this->get('/users');



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

                sprintf('[GET /users] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_from_usersservice(): void

        {

            $this->__UsersService_setUp();

            /* Arrange */

            /* (authenticated admin via __UsersService_setUp) */



            /* Act */

            $response = $this->get('/users');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_usersservice(): void

        {

            $this->__UsersService_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/users');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/users] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __VersionsController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_versionscontroller(): void

        {

            $this->__VersionsController_setUp();

            /* Arrange */

            /* (authenticated admin via __VersionsController_setUp) */



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<form');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_versionscontroller(): void

        {

            $this->__VersionsController_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/settings');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/settings] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __ViewTemplateSystem_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_viewtemplatesystem(): void

        {

            $this->__ViewTemplateSystem_setUp();

            /* Arrange */

            /* (authenticated admin via __ViewTemplateSystem_setUp) */



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, '<html');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_viewtemplatesystem(): void

        {

            $this->__ViewTemplateSystem_setUp();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/dashboard');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/dashboard] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function __WelcomeController_setUp(): void

        {



        }
    #[Test]

    public function it_displays_welcome_page(): void

        {

            $this->__WelcomeController_setUp();

            /* Arrange */

            /* (no setup needed) */



            /* Act */

            $response = $this->get('/welcome');



            /* Assert */

            $this->assertResponseOk($response);

            $this->assertResponseBodyContains($response, '<html');

        }
    protected function __CustomFieldEntityModels_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_loads_allowed_custom_field_tables_and_positions_for_the_form(): void

        {

            $this->__CustomFieldEntityModels_setUp();

            /* Arrange */

            /* Act */

            $response = $this->get('/custom_fields/form');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            foreach (['ip_client_custom', 'ip_invoice_custom', 'ip_payment_custom', 'ip_quote_custom', 'ip_user_custom'] as $table) {

                $this->assertResponseBodyContains($response, $table);

            }

            foreach (['Account Information', 'Contact Information', 'Properties', 'Taxes Information'] as $position) {

                $this->assertResponseBodyContains($response, $position);

            }

        }
    private string $uploadDir;
    protected function __MdlUploads_setUp(): void

        {



            $this->actingAsAdmin();



            $this->uploadDir = dirname(__DIR__, 3) . '/uploads/customer_files';

            if ( ! is_dir($this->uploadDir)) {

                mkdir($this->uploadDir, 0777, true);

            }

        }
    protected function __MdlUploads_tearDown(): void

        {

            foreach (glob($this->uploadDir . '/testkey_*') ?: [] as $file) {

                if (is_file($file)) {

                    unlink($file);

                }

            }





        }
    #[Test]

    public function it_lists_upload_metadata_for_existing_files(): void

        {

            $this->__MdlUploads_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Upload Metadata Client']);

            file_put_contents($this->uploadDir . '/testkey_invoice.pdf', 'pdf-bytes');

            $this->databaseInsert('ip_uploads', [

                'client_id'          => $clientId,

                'url_key'            => 'testkey',

                'file_name_original' => 'invoice.pdf',

                'file_name_new'      => 'testkey_invoice.pdf',

                'uploaded_date'      => date('Y-m-d'),

            ]);



            /* Act */

            $response = $this->get('/upload/show_files/testkey');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            self::assertSame([['name' => 'invoice.pdf', 'size' => 9]], $payload);

        }
    #[Test]

    public function it_skips_upload_rows_with_unsafe_stored_paths(): void

        {

            $this->__MdlUploads_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Unsafe Upload Client']);

            file_put_contents($this->uploadDir . '/testkey_safe.pdf', 'safe');

            $this->databaseInsert('ip_uploads', [

                'client_id'          => $clientId,

                'url_key'            => 'testkey',

                'file_name_original' => 'safe.pdf',

                'file_name_new'      => 'testkey_safe.pdf',

                'uploaded_date'      => date('Y-m-d'),

            ]);

            $this->databaseInsert('ip_uploads', [

                'client_id'          => $clientId,

                'url_key'            => 'testkey',

                'file_name_original' => 'passwd',

                'file_name_new'      => '../../etc/passwd',

                'uploaded_date'      => date('Y-m-d'),

            ]);



            /* Act */

            $response = $this->get('/upload/show_files/testkey');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyNotContains($response, 'passwd');

            $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            self::assertSame([['name' => 'safe.pdf', 'size' => 4]], $payload);

            $this->assertDatabaseHas('ip_uploads', ['file_name_new' => '../../etc/passwd']);

        }
    protected function __UsersAjaxController_setUp(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect_from_usersajaxcontroller(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            /* (setup done in __UsersAjaxController_setUp) */



            /* Act */

            $response = $this->get('/users');



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

                sprintf('[GET /users] returned unexpected status [%d].', $response->statusCode())

            );

        }
    #[Test]

    public function it_does_not_expose_php_errors_from_usersajaxcontroller(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            /* (setup done in __UsersAjaxController_setUp) */



            /* Act */

            $response = $this->get('/users');



            /* Assert */

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]
    #[Group('security')]

    public function it_treats_name_query_input_as_a_literal_search_term(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_users', [

                'user_name'          => 'Needle User',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'needle@test.local',

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            $this->databaseInsert('ip_users', [

                'user_name'          => 'Hidden User',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'hidden@test.local',

                'user_type'          => 1,

                'user_active'        => 0,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $response = $this->request('GET', '/users/ajax/name_query/1', [

                'query' => "Needle%' OR 1=1 --",

            ], [], true);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

            $this->assertResponseBodyNotContains($response, 'Hidden User');

            $this->assertDatabaseHas('ip_users', ['user_name' => 'Needle User']);

            $this->assertDatabaseHas('ip_users', ['user_name' => 'Hidden User']);

        }
    #[Test]

    public function it_returns_latest_users_with_escaped_display_text(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $this->databaseInsert('ip_users', [

                'user_name'          => '<script>alert(1)</script>',

                'user_password'      => password_hash('secret', PASSWORD_DEFAULT),

                'user_psalt'         => bin2hex(random_bytes(10)),

                'user_email'         => 'xss-user@test.local',

                'user_type'          => 1,

                'user_active'        => 1,

                'user_date_created'  => date('Y-m-d H:i:s'),

                'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $response = $this->request('GET', '/users/ajax/get_latest', [], [], true);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyNotContains($response, '<script>alert(1)</script>');

            $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            self::assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', array_column($payload, 'text'));

        }
    #[Test]

    public function it_saves_a_valid_permissive_search_preference(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            /* Act */

            $this->request('GET', '/users/ajax/save_preference_permissive_search_users', ['permissive_search_users' => '1'], [], true);



            /* Assert */

            $this->assertDatabaseHas('ip_settings', ['setting_key' => 'enable_permissive_search_users', 'setting_value' => '1']);

        }
    #[Test]

    public function it_rejects_an_invalid_permissive_search_preference_value(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            /* Act */

            $this->request('GET', '/users/ajax/save_preference_permissive_search_users', ['permissive_search_users' => '2'], [], true);



            /* Assert */

            $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'enable_permissive_search_users']);

        }
    #[Test]

    public function it_assigns_a_client_to_an_existing_user(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient();

            $userId   = $this->databaseInsert('ip_users', [

                'user_name'     => 'Client Assign Target', 'user_email' => 'assign-' . bin2hex(random_bytes(4)) . '@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $this->ajax('POST', '/users/ajax/save_user_client', ['user_id' => (string) $userId, 'client_id' => (string) $clientId]);



            /* Assert */

            $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        }
    #[Test]

    public function it_does_not_assign_an_unknown_client(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $userId = $this->databaseInsert('ip_users', [

                'user_name'     => 'No Client Target', 'user_email' => 'noclient-' . bin2hex(random_bytes(4)) . '@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);



            /* Act */

            $this->ajax('POST', '/users/ajax/save_user_client', ['user_id' => (string) $userId, 'client_id' => '999999']);



            /* Assert */

            $this->assertDatabaseCount('ip_user_clients', 0);

        }
    #[Test]

    public function it_loads_the_user_client_table_for_an_existing_user(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $clientId = $this->seedClient(['client_name' => 'Loaded Client Marker']);

            $userId   = $this->databaseInsert('ip_users', [

                'user_name'     => 'Table Load Target', 'user_email' => 'tableload-' . bin2hex(random_bytes(4)) . '@test.local',

                'user_password' => password_hash('x', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(10)),

                'user_type'     => 1, 'user_active' => 1, 'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),

            ]);

            $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);



            /* Act */

            $response = $this->ajax('POST', '/users/ajax/load_user_client_table', ['user_id' => (string) $userId]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Loaded Client Marker');

        }
    #[Test]

    public function it_renders_the_add_user_client_modal(): void

        {

            $this->__UsersAjaxController_setUp();

            /* Arrange */

            $this->seedClient();



            /* Act */

            $response = $this->ajax('POST', '/users/ajax/modal_add_user_client', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    private const DELETE_ENDPOINTS = [

            'projects/delete',

            'tasks/delete',

            'users/delete',

            'invoice_groups/delete',

            'payment_methods/delete',

            'custom_fields/delete',

            'units/delete',

            'tax_rates/delete',

            'custom_values/delete',

            'clients/delete',

            'products/delete',

            'settings/remove_logo',

        ];
    #[Test]

    public function it_requires_post_validation_for_delete_endpoints(): void

        {

            /* Arrange */

            $failures = [];



            /* Act */

            foreach (self::DELETE_ENDPOINTS as $endpoint) {

                [$module, $action] = explode('/', $endpoint);

                $controllerFile    = $this->__CsrfDeleteSecurity_findControllerFile($module, $action);



                if ($controllerFile === null) {

                    $failures[] = "{$module}/{$action}: controller not found";

                    continue;

                }



                $content = (string) file_get_contents($controllerFile);

                if (

                    preg_match(

                        '/public\s+function\s+' . preg_quote($action, '/') . '\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{[^}]*ensure_valid_post_request/s',

                        $content

                    ) !== 1

                ) {

                    $failures[] = "{$module}/{$action}: missing ensure_valid_post_request() call";

                }

            }



            /* Assert */

            self::assertSame([], $failures, implode(PHP_EOL, $failures));

        }
    #[Test]

    public function it_does_not_link_to_delete_endpoints_with_get_anchors(): void

        {

            /* Arrange */

            $violations = [];



            /* Act */

            foreach ($this->__CsrfDeleteSecurity_moduleViewFiles() as $file) {

                $content = (string) file_get_contents($file);



                if (preg_match_all('/anchor\s*\(\s*[\'"]([^\'"]*\/delete[^\'"]*)[\'"]/', $content, $matches)) {

                    $violations[] = basename($file) . ' has anchor() link to delete: ' . implode(', ', $matches[1]);

                }

            }



            /* Assert */

            self::assertSame([], $violations, implode(PHP_EOL, $violations));

        }
    #[Test]

    public function it_includes_csrf_tokens_in_post_forms(): void

        {

            /* Arrange */

            $failures      = [];

            $postFormCount = 0;



            /* Act */

            foreach ($this->__CsrfDeleteSecurity_moduleViewFiles() as $file) {

                $content = (string) file_get_contents($file);



                if (preg_match_all('/method\s*=\s*["\']POST["\']/i', $content) === 0) {

                    continue;

                }



                $postFormCount++;

                if (preg_match('/_csrf_field\s*\(\s*\)/', $content) !== 1) {

                    $failures[] = basename($file);

                }

            }



            /* Assert */

            self::assertGreaterThan(0, $postFormCount);

            self::assertSame([], $failures, implode(PHP_EOL, $failures));

        }
    private function __CsrfDeleteSecurity_findControllerFile(string $module, string $action): ?string

        {

            $controllerDir = $this->__CsrfDeleteSecurity_moduleDir() . '/' . $module . '/controllers';

            if ( ! is_dir($controllerDir)) {

                return null;

            }



            foreach (glob($controllerDir . '/*.php') ?: [] as $file) {

                $content = (string) file_get_contents($file);

                if (

                    preg_match('/class\s+\w+\s+extends/', $content) === 1

                    && preg_match('/public\s+function\s+' . preg_quote($action, '/') . '\s*\(/', $content) === 1

                ) {

                    return $file;

                }

            }



            return null;

        }



        /**

         * @return array<int, string>

         */
    private function __CsrfDeleteSecurity_moduleViewFiles(): array

        {

            $files = [];



            foreach (glob($this->__CsrfDeleteSecurity_moduleDir() . '/*/views/*.php') ?: [] as $file) {

                $files[] = $file;

            }



            sort($files);



            return $files;

        }
    private function __CsrfDeleteSecurity_moduleDir(): string

        {

            return dirname(__DIR__, 3) . '/application/modules';

        }
    #[Test]

    public function it_defines_the_sumex_storage_folder_outside_the_public_web_root(): void

        {

            /* Arrange */

            require_once dirname(__DIR__, 3) . '/bootstrap/kernel.php';



            /* Act */

            $storageTempFolder = STORAGE_TEMP_FOLDER;



            /* Assert */

            self::assertTrue(defined('STORAGE_TEMP_FOLDER'), 'STORAGE_TEMP_FOLDER must be defined by bootstrap/kernel.php.');

            self::assertTrue(defined('FCPATH'), 'FCPATH must be defined by bootstrap/kernel.php.');



            self::assertStringStartsNotWith(

                FCPATH,

                $storageTempFolder,

                'STORAGE_TEMP_FOLDER must live outside the public web root (FCPATH), '

                . 'otherwise SUMEX XML files are directly downloadable by an unauthenticated attacker.'

            );

        }
    #[Test]

    public function it_writes_the_sumex_xml_to_the_non_web_accessible_storage_folder(): void

        {

            /* Arrange */

            $sumexFile = APPPATH . 'libraries/Sumex.php';



            /* Act */

            $content = file_get_contents($sumexFile);



            /* Assert */

            self::assertStringContainsString(

                'STORAGE_TEMP_FOLDER . $filename',

                $content,

                'Sumex::pdf() must write the invoice XML into STORAGE_TEMP_FOLDER. '

                . 'Writing it back under UPLOADS_TEMP_FOLDER (or any path under the public '

                . 'web root) would reintroduce the unauthenticated information-disclosure vulnerability.'

            );

            self::assertStringNotContainsString(

                'UPLOADS_TEMP_FOLDER . $filename',

                $content,

                'Sumex::pdf() must not write the invoice XML under the web-accessible uploads/temp folder.'

            );

        }



        // -----------------------------------------------------------------------

        // uploads/import access control (4add6c1)

        // -----------------------------------------------------------------------
    #[Test]

    public function it_denies_direct_web_access_to_the_uploads_import_directory(): void

        {

            /* Arrange */

            $htaccessPath = dirname(APPPATH) . '/uploads/import/.htaccess';



            /* Act */

            $rules = file_get_contents($htaccessPath);



            /* Assert */

            self::assertFileExists(

                $htaccessPath,

                'uploads/import/.htaccess must exist — it is the only barrier between the '

                . 'internet and in-progress CSV imports (invoices.csv, payments.csv, ...) '

                . 'which are plaintext and contain client/invoice financial data.'

            );

            self::assertStringContainsString(

                'Deny from all',

                $rules,

                'uploads/import/.htaccess must deny all direct web access.'

            );

        }
    #[Test]

    public function it_rejects_a_path_traversal_payload_in_the_file_download_endpoint(): void

        {

            /* Arrange */

            $this->actingAsAdmin();



            // Classic traversal patterns: the real_filename part (after the first _) must

            // fail validate_safe_filename() and never reach readfile().

            $traversalPayloads = [

                'key_..%2F..%2F..%2Fetc%2Fpasswd',   // URL-encoded slashes

                'key_....//....//etc//passwd',          // doubled slashes obfuscation

                'key_%2e%2e%2fetc%2fpasswd',           // fully encoded

            ];



            foreach ($traversalPayloads as $payload) {

                /* Act */

                $response = $this->get('/upload/get_file/' . $payload);



                /* Assert */

                self::assertNotSame(

                    200,

                    $response->statusCode(),

                    "Traversal payload [{$payload}] must not yield a 200 response."

                );



                self::assertStringNotContainsString(

                    'root:',

                    $response->body(),

                    "Traversal payload [{$payload}] must not return /etc/passwd content."

                );

            }

        }
    #[Test]

    public function it_rejects_null_byte_injection_in_the_file_download_endpoint(): void

        {

            /* Arrange */

            $this->actingAsAdmin();



            // Null bytes in filenames can truncate paths in older C-library calls.

            // validate_safe_filename() explicitly checks for them.

            $payload = 'key_legitimate.pdf' . "\0" . '../../../etc/passwd';



            /* Act */

            $response = $this->get('/upload/get_file/' . rawurlencode($payload));



            /* Assert */

            self::assertNotSame(200, $response->statusCode());

            self::assertStringNotContainsString('root:', $response->body());

        }
    #[Test]

    public function it_rejects_a_path_traversal_payload_in_the_file_delete_endpoint(): void

        {

            /* Arrange */

            $this->actingAsAdmin();



            // Before the fix, delete_file() accepted unvalidated filenames and could

            // delete arbitrary files via the POST name parameter.

            $traversalName = '../../../bootstrap/kernel.php';



            /* Act */

            // Attempt to delete a file outside the uploads directory.

            $response = $this->post('/upload/delete_file/testkey', [

                'name' => $traversalName,

            ]);



            /* Assert */

            // sanitize_file_name() reduces the payload to a bare basename, so it can never escape

            // the uploads directory. delete_file() then treats an already-absent target as an

            // idempotent success (200), so the security guarantee here is not the status code but

            // that the out-of-directory file is never deleted — the request must at least be

            // handled cleanly (no 5xx).

            self::assertLessThan(

                500,

                $response->statusCode(),

                'A path traversal name in delete_file must be handled cleanly, not error out.'

            );



            // Verify the targeted file was not deleted (the actual path-traversal guarantee).

            self::assertFileExists(

                dirname(__DIR__, 3) . '/bootstrap/kernel.php',

                'bootstrap/kernel.php must survive a path-traversal delete attempt.'

            );

        }



        // -----------------------------------------------------------------------

        // 3. SQL injection via setting_key

        // -----------------------------------------------------------------------
    #[Test]

    public function it_stores_a_crafted_setting_key_safely_without_breaking_the_table(): void

        {

            /* Arrange */

            // A classic SQL injection payload embedded in the key name.

            // Before parameterised queries, this would terminate the WHERE clause

            // and run a DROP TABLE.

            $this->actingAsAdmin();



            $maliciousKey = "legit_key'; DROP TABLE ip_settings; --";



            /* Act */

            $response = $this->post('/settings', [

                'settings' => [$maliciousKey => 'any_value'],

            ]);



            /* Assert */

            // The request must not crash (5xx) — CI3's query builder parameterises the key.

            self::assertNotSame(500, $response->statusCode());



            // The settings table must still exist and still hold the seed rows.

            $this->assertDatabaseHas('ip_settings', ['setting_key' => 'disable_setup']);



            // Clean up the injected key if it was actually stored.

            $this->databaseDelete('ip_settings', ['setting_key' => $maliciousKey]);

        }
    #[Test]

    public function it_stores_a_setting_key_with_html_characters_as_literal_text(): void

        {

            /* Arrange */

            // Stored XSS: if setting_key is rendered unescaped in an admin view,

            // a <script> tag in the key would execute in the browser.

            // This test documents that the key is stored as a plain string —

            // the view layer is responsible for escaping on output.

            $this->actingAsAdmin();



            $xssKey = '<script>alert(1)</script>';



            /* Act */

            $response = $this->post('/settings', [

                'settings' => [$xssKey => 'xss_probe'],

            ]);



            /* Assert */

            self::assertNotSame(500, $response->statusCode());



            // The table must be intact.

            $this->assertDatabaseHas('ip_settings', ['setting_key' => 'disable_setup']);



            // Clean up.

            $this->databaseDelete('ip_settings', ['setting_key' => $xssKey]);

        }



        // -----------------------------------------------------------------------

        // 4. Config injection via logo setting (path traversal in setting value)

        // -----------------------------------------------------------------------
    #[Test]

    public function it_rejects_a_path_traversal_value_for_the_login_logo_setting(): void

        {

            /* Arrange */

            $this->actingAsAdmin();



            $originalLogo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);



            /* Act */

            $response = $this->post('/settings', [

                'settings' => [

                    'login_logo' => '..\\..\\application\\config\\database.php',

                ],

            ]);



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                'A path-traversal login_logo value must cause a redirect (validation rejection), not a 200.'

            );



            $stored      = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);

            $storedValue = $stored['setting_value'] ?? ($originalLogo['setting_value'] ?? '');



            self::assertStringNotContainsString(

                '..',

                (string) $storedValue,

                'A traversal path must never be persisted as the login_logo setting value.'

            );

        }



        // -----------------------------------------------------------------------

        // 5. RCE — PDF template whitelist (CVE-pending, PR #1505, CVSS 9.9)

        //

        // select_pdf_invoice_template() / validate_template_name() must reject any

        // template name that isn't in Mdl_Templates::ALLOWED_INVOICE_TEMPLATES,

        // no matter what an attacker supplies via the generate_pdf() URL segment,

        // and fall back to the safe default instead of including an arbitrary file.

        // -----------------------------------------------------------------------
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
}

class StubSessionsSecurity
{
    private const MAX_EXPIRY_MINUTES = 1440;

    private const BOT_SIGNATURES = [
        'curl', 'wget', 'python-requests', 'go-http-client',
        'java/', 'apache-httpclient', 'okhttp', 'httpclient',
        'bot', 'spider', 'crawler', 'scraper',
        'postman', 'insomnia', 'paw/',
    ];

    public function __construct(private readonly string $baseUrl) {}

    public function getSafeReferer(string $referer): string
    {
        if (empty($referer)) {
            return 'sessions/passwordreset';
        }

        if (str_starts_with($referer, $this->baseUrl)) {
            return $referer;
        }

        return 'sessions/passwordreset';
    }

    public function isValidTokenFormat(string $token): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-_]+$/', $token);
    }

    public function isTokenExpired(string $expiryTimestamp): bool
    {
        $utc    = new DateTimeZone('UTC');
        $expiry = new DateTime($expiryTimestamp, $utc);
        $now    = new DateTime('now', $utc);

        return $now > $expiry;
    }

    /**
     * Strict expiry parsing, mirroring Sessions::_reject_expired_password_reset_token().
     *
     * A stored expiry is accepted only when it matches the exact, anchored canonical
     * Y-m-d H:i:s shape and createFromFormat() parses it with no warnings/errors. This
     * rejects out-of-range values (25:99:99), rolled-over dates (2020-13-40 00:00:00), and
     * non-canonical spacing/single-digit fields (2026-8-10 9:05:07) that createFromFormat()
     * would otherwise normalize silently.
     */
    public function isCanonicalExpiry(string $raw): bool
    {
        if ( ! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $raw)) {
            return false;
        }

        $utc          = new DateTimeZone('UTC');
        $parsed       = DateTime::createFromFormat('!Y-m-d H:i:s', $raw, $utc);
        $parse_errors = DateTime::getLastErrors();

        if ($parsed === false) {
            return false;
        }

        return ! ($parse_errors !== false
            && ($parse_errors['warning_count'] > 0 || $parse_errors['error_count'] > 0));
    }

    public function clampExpiryMinutes(int $minutes): int
    {
        if ($minutes < 1 || $minutes > self::MAX_EXPIRY_MINUTES) {
            return 15;
        }

        return $minutes;
    }

    public function isBotUserAgent(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        $lower = mb_strtolower($userAgent);

        foreach (self::BOT_SIGNATURES as $sig) {
            if (str_contains($lower, $sig)) {
                return true;
            }
        }

        return false;
    }

    public function filterAttemptsWithinWindow(array $timestamps, int $windowSeconds): array
    {
        $cutoff = time() - $windowSeconds;

        return array_values(array_filter($timestamps, fn (int $ts): bool => $ts > $cutoff));
    }

    public function isRateLimited(array $attempts, int $maxAttempts, int $windowSeconds): bool
    {
        $active = $this->filterAttemptsWithinWindow($attempts, $windowSeconds);

        return count($active) >= $maxAttempts;
    }
}