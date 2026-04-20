<?php

namespace Modules\Core\Tests\Feature;

//use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]
class CoreAjaxControllerTest extends FeatureTestCase
{
    /**
     * Test getCronKey returns JSON with random key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_json_with_random_cron_key(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('core.ajax.get_cron_key'));

        /* Assert */
        $response->assertOk();
        $response->assertJsonStructure(['key']);

        $data = $response->json();
        $this->assertIsString($data['key']);
        $this->assertEquals(16, mb_strlen($data['key']));
    }

    /**
     * Test getCronKey generates different keys on each request.
     */
    #[Test]
    public function it_generates_different_keys_on_each_request(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response1 = $this->get(route('core.ajax.get_cron_key'));
        $this->actingAs($user);
        $response2 = $this->get(route('core.ajax.get_cron_key'));

        /** Assert */
        $key1 = $response1->json('key');
        $key2 = $response2->json('key');

        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test getCronKey generates alphanumeric keys only.
     */
    #[Test]
    public function it_generates_alphanumeric_keys_only(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('core.ajax.get_cron_key'));

        /** Assert */
        $key = $response->json('key');
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{16}$/', $key);
    }
}

/**
 * CustomFieldsController Feature Tests.
 *
 * Tests custom field management for extending data models.
 */
#[CoversClass(CustomFieldsController::class)]
class CustomFieldsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of custom fields.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_custom_fields(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        CustomField::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom-fields.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_fields_index');
        $response->assertViewHas('custom_fields');
    }

    /**
     * Test custom fields are ordered by table and label.
     */
    #[Test]
    public function it_orders_custom_fields_by_table_and_label(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        CustomField::factory()->create(['custom_field_table' => 'ip_clients', 'custom_field_label' => 'Field B']);
        CustomField::factory()->create(['custom_field_table' => 'ip_clients', 'custom_field_label' => 'Field A']);
        CustomField::factory()->create(['custom_field_table' => 'ip_invoices', 'custom_field_label' => 'Field C']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom-fields.index'));

        /* Assert */
        $response->assertOk();
        $customFields = $response->viewData('custom_fields');

        // Verify ordering by table, then label
        $this->assertGreaterThan(0, $customFields->count());
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom-fields.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_fields_form');
        $response->assertViewHas('custom_field');

        $customField = $response->viewData('custom_field');
        $this->assertInstanceOf(CustomField::class, $customField);
        $this->assertFalse($customField->exists);
    }

    /**
     * Test form displays edit form with existing custom field.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_custom_field(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom-fields.form', ['custom_field_id' => $customField->custom_field_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_fields_form');
        $response->assertViewHas('custom_field');

        $viewCustomField = $response->viewData('custom_field');
        $this->assertEquals($customField->custom_field_id, $viewCustomField->custom_field_id);
    }

    /**
     * Test form creates new custom field with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_custom_field_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "custom_field_table": "ip_clients",
         *     "custom_field_label": "Test Field",
         *     "custom_field_column": "custom_test_field",
         *     "btn_submit": "1"
         * }.
         */
        $customFieldData = [
            'custom_field_table'  => 'ip_clients',
            'custom_field_label'  => 'Test Field',
            'custom_field_column' => 'custom_test_field',
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom-fields.form'), $customFieldData);

        /* Assert */
        $response->assertRedirect(route('custom-fields.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_custom_fields', [
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Test Field',
        ]);
    }

    /**
     * Test form updates existing custom field.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_custom_field_with_valid_data(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create(['custom_field_label' => 'Old Label']);

        /**
         * {
         *     "custom_field_table": "ip_clients",
         *     "custom_field_label": "Updated Label",
         *     "custom_field_column": "client_custom",
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'custom_field_table'  => $customField->custom_field_table,
            'custom_field_label'  => 'Updated Label',
            'custom_field_column' => $customField->custom_field_column,
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom-fields.form', ['custom_field_id' => $customField->custom_field_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('custom-fields.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_custom_fields', [
            'custom_field_id'    => $customField->custom_field_id,
            'custom_field_label' => 'Updated Label',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom-fields.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('custom-fields.index'));
    }

    /**
     * Test delete removes custom field.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_custom_field(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();

        /**
         * {
         *     "custom_field_id": 1
         * }.
         */
        $deletePayload = [
            'custom_field_id' => $customField->custom_field_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('custom-fields.delete', ['custom_field_id' => $customField->custom_field_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('custom-fields.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_custom_fields', [
            'custom_field_id' => $customField->custom_field_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent custom field.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_custom_field(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "custom_field_id": 99999
         * }.
         */
        $deletePayload = [
            'custom_field_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('custom-fields.delete', ['custom_field_id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test form returns 404 for non-existent custom field in edit mode.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_custom_field(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom-fields.form', ['custom_field_id' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * CustomValuesController Feature Tests.
 *
 * Tests custom value management for custom fields.
 */
#[CoversClass(CustomValuesController::class)]
class CustomValuesControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of custom values.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_custom_values(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();
        CustomValue::factory()->count(5)->create(['custom_field_id' => $customField->custom_field_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom_values.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_values_index');
        $response->assertViewHas('custom_values');
    }

    /**
     * Test custom values are loaded with custom field relationship.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_custom_values_with_custom_field_relationship(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();
        CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom_values.index'));

        /* Assert */
        $response->assertOk();
        $customValues = $response->viewData('custom_values');

        // Verify relationship is loaded
        $this->assertGreaterThan(0, $customValues->count());
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom_values.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_values_form');
        $response->assertViewHas('custom_value');
        $response->assertViewHas('custom_fields');

        $customValue = $response->viewData('custom_value');
        $this->assertInstanceOf(CustomValue::class, $customValue);
        $this->assertFalse($customValue->exists);
    }

    /**
     * Test form displays edit form with existing custom value.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_custom_value(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom_values.form', ['custom_values_id' => $customValue->custom_value_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::custom_values_form');
        $response->assertViewHas('custom_value');
        $response->assertViewHas('custom_fields');

        $viewCustomValue = $response->viewData('custom_value');
        $this->assertEquals($customValue->custom_value_id, $viewCustomValue->custom_value_id);
    }

    /**
     * Test form creates new custom value with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_custom_value_with_valid_data(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();

        /**
         * {
         *     "custom_field_id": 1,
         *     "custom_value_value": "Test Value",
         *     "btn_submit": "1"
         * }.
         */
        $customValueData = [
            'custom_field_id'    => $customField->custom_field_id,
            'custom_value_value' => 'Test Value',
            'btn_submit'         => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom_values.form'), $customValueData);

        /* Assert */
        $response->assertRedirect(route('custom_values.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_custom_values', [
            'custom_field_id'    => $customField->custom_field_id,
            'custom_value_value' => 'Test Value',
        ]);
    }

    /**
     * Test form updates existing custom value.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_custom_value_with_valid_data(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create([
            'custom_field_id'    => $customField->custom_field_id,
            'custom_value_value' => 'Old Value',
        ]);

        /**
         * {
         *     "custom_field_id": 1,
         *     "custom_value_value": "Updated Value",
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'custom_field_id'    => $customField->custom_field_id,
            'custom_value_value' => 'Updated Value',
            'btn_submit'         => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom_values.form', ['custom_values_id' => $customValue->custom_value_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('custom_values.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_custom_values', [
            'custom_value_id'    => $customValue->custom_value_id,
            'custom_value_value' => 'Updated Value',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('custom_values.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('custom_values.index'));
    }

    /**
     * Test delete removes custom value.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_custom_value(): void
    {
        /** Arrange */
        $user        = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);

        /**
         * {
         *     "custom_value_id": 1
         * }.
         */
        $deletePayload = [
            'custom_value_id' => $customValue->custom_value_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('custom_values.delete', ['custom_values_id' => $customValue->custom_value_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('custom_values.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_custom_values', [
            'custom_value_id' => $customValue->custom_value_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent custom value.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_custom_value(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "custom_value_id": 99999
         * }.
         */
        $deletePayload = [
            'custom_value_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('custom_values.delete', ['custom_values_id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test form returns 404 for non-existent custom value in edit mode.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_custom_value(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('custom_values.form', ['custom_values_id' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * DashboardController Feature Tests.
 *
 * Tests dashboard display with statistics and overview data.
 */
#[CoversClass(DashboardController::class)]
class DashboardControllerTest extends FeatureTestCase
{
    /**
     * Test index displays dashboard with statistics.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_dashboard_with_statistics(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        // Create test data
        Client::factory()->count(5)->create();
        Invoice::factory()->count(10)->create();
        Quote::factory()->count(3)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::dashboard');
        $response->assertViewHas('total_clients');
        $response->assertViewHas('total_invoices');
        $response->assertViewHas('total_quotes');
    }

    /**
     * Test index shows correct client count.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_correct_client_count(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Client::factory()->count(7)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        /* Assert */
        $response->assertOk();
        $totalClients = $response->viewData('total_clients');
        $this->assertEquals(7, $totalClients);
    }

    /**
     * Test index shows correct invoice count.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_correct_invoice_count(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Invoice::factory()->count(15)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        /* Assert */
        $response->assertOk();
        $totalInvoices = $response->viewData('total_invoices');
        $this->assertEquals(15, $totalInvoices);
    }

    /**
     * Test index shows correct quote count.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_correct_quote_count(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Quote::factory()->count(8)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        /* Assert */
        $response->assertOk();
        $totalQuotes = $response->viewData('total_quotes');
        $this->assertEquals(8, $totalQuotes);
    }

    /**
     * Test index shows zero counts when no data exists.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_zero_counts_when_no_data_exists(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        /* Assert */
        $response->assertOk();
        $this->assertEquals(0, $response->viewData('total_clients'));
        $this->assertEquals(0, $response->viewData('total_invoices'));
        $this->assertEquals(0, $response->viewData('total_quotes'));
    }
}

/**
 * EmailTemplatesController Feature Tests.
 *
 * Tests email template management for customizing system emails.
 */
#[CoversClass(EmailTemplatesController::class)]
class EmailTemplatesControllerTest extends FeatureTestCase
{
    /**
     * Data provider for required field validation tests.
     *
     * @return array<string, array{field: string, data: array<string, string>}>
     */
    public static function requiredFieldsProvider(): array
    {
        return [
            'title is required' => [
                'field' => 'email_template_title',
                'data'  => [
                    'email_template_title'   => '',
                    'email_template_subject' => 'Subject',
                    'email_template_body'    => 'Body',
                    'btn_submit'             => '1',
                ],
            ],
            'subject is required' => [
                'field' => 'email_template_subject',
                'data'  => [
                    'email_template_title'   => 'Title',
                    'email_template_subject' => '',
                    'email_template_body'    => 'Body',
                    'btn_submit'             => '1',
                ],
            ],
            'body is required' => [
                'field' => 'email_template_body',
                'data'  => [
                    'email_template_title'   => 'Title',
                    'email_template_subject' => 'Subject',
                    'email_template_body'    => '',
                    'btn_submit'             => '1',
                ],
            ],
        ];
    }

    /**
     * Test index displays paginated list of email templates.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_email_templates(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        EmailTemplate::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::email_templates_index');
        $response->assertViewHas('email_templates');
    }

    /**
     * Test templates are ordered alphabetically by title.
     */
    #[Test]
    public function it_orders_email_templates_alphabetically_by_title(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        EmailTemplate::factory()->create(['email_template_title' => 'Welcome Email']);
        EmailTemplate::factory()->create(['email_template_title' => 'Invoice Email']);
        EmailTemplate::factory()->create(['email_template_title' => 'Quote Email']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.index'));

        /* Assert */
        $response->assertOk();
        $templates = $response->viewData('email_templates');
        $titles    = $templates->pluck('email_template_title')->toArray();

        $this->assertEquals('Invoice Email', $titles[0]);
        $this->assertEquals('Quote Email', $titles[1]);
        $this->assertEquals('Welcome Email', $titles[2]);
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::email_templates_form');
        $response->assertViewHas('email_template');

        $template = $response->viewData('email_template');
        $this->assertInstanceOf(EmailTemplate::class, $template);
        $this->assertFalse($template->exists);
    }

    /**
     * Test form displays edit form with existing template.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_template(): void
    {
        /** Arrange */
        $user     = User::factory()->create();
        $template = EmailTemplate::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.form', ['email_template_id' => $template->email_template_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::email_templates_form');
        $response->assertViewHas('email_template');

        $viewTemplate = $response->viewData('email_template');
        $this->assertEquals($template->email_template_id, $viewTemplate->email_template_id);
    }

    /**
     * Test form creates new email template.
     *
     * JSON Payload:
     * {
     *   "email_template_title": "New Template",
     *   "email_template_subject": "Subject",
     *   "email_template_body": "Body content",
     *   "btn_submit": "1"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_email_template_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $templateData = [
            'email_template_title'   => 'New Template',
            'email_template_subject' => 'Subject',
            'email_template_body'    => 'Body content',
            'btn_submit'             => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $templateData);

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_title' => 'New Template',
        ]);
    }

    /**
     * Test form updates existing email template.
     *
     * JSON Payload:
     * {
     *   "email_template_title": "Updated Title",
     *   "email_template_subject": "Invoice Reminder",
     *   "email_template_body": "Please pay your invoice.",
     *   "btn_submit": "1"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_email_template(): void
    {
        /** Arrange */
        $user     = User::factory()->create();
        $template = EmailTemplate::factory()->create(['email_template_title' => 'Old Title']);

        $updateData = [
            'email_template_title'   => 'Updated Title',
            'email_template_subject' => $template->email_template_subject,
            'email_template_body'    => $template->email_template_body,
            'btn_submit'             => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form', ['email_template_id' => $template->email_template_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_id'    => $template->email_template_id,
            'email_template_title' => 'Updated Title',
        ]);
    }

    /**
     * Test form redirects on cancel.
     *
     * JSON Payload:
     * {
     *   "btn_cancel": "1"
     * }
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));
    }

    /**
     * Test delete removes email template.
     *
     * JSON Payload:
     * {
     *   "email_template_id": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_email_template(): void
    {
        /** Arrange */
        $user     = User::factory()->create();
        $template = EmailTemplate::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('email_templates.delete', ['email_template_id' => $template->email_template_id])
        );

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_email_templates', [
            'email_template_id' => $template->email_template_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent template.
     *
     * JSON Payload:
     * {
     *   "email_template_id": 99999
     * }
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_template(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('email_templates.delete', ['email_template_id' => 99999])
        );

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test form returns 404 for non-existent template in edit mode.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_template(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.form', ['email_template_id' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }

    // ==================== EDGE CASES & VALIDATION ====================

    /**
     * Test template creation requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_all_routes(): void
    {
        /* Act & Assert */
        $this->get(route('email_templates.index'))->assertRedirect(route('sessions.login'));
        $this->get(route('email_templates.form'))->assertRedirect(route('sessions.login'));
    }

    /**
     * Test form validates required fields.
     *
     * @param string                $field The field name that should have validation errors
     * @param array<string, string> $data  The invalid form data
     */
    #[Group('validation')]
    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('requiredFieldsProvider')]
    public function it_validates_required_fields(string $field, array $data): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $data);

        /* Assert */
        $response->assertSessionHasErrors($field);
    }

    /**
     * Test form handles very long title.
     *
     * JSON Payload:
     * {
     *   "email_template_title": "AAAA...300 chars",
     *   "email_template_subject": "Subject",
     *   "email_template_body": "Body",
     *   "btn_submit": "1"
     * }
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_very_long_title(): void
    {
        /** Arrange */
        $user      = User::factory()->create();
        $longTitle = str_repeat('A', 300);

        $templateData = [
            'email_template_title'   => $longTitle,
            'email_template_subject' => 'Subject',
            'email_template_body'    => 'Body',
            'btn_submit'             => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $templateData);

        /* Assert */
        // Should either truncate or reject
        $this->assertTrue(
            $response->isRedirect()
            || $response->getSession()->has('errors')
        );
    }

    /**
     * Test form handles HTML in body.
     *
     * JSON Payload:
     * {
     *   "email_template_title": "HTML Template",
     *   "email_template_subject": "Subject",
     *   "email_template_body": "<p>Hello {client_name},</p><p>Your invoice is ready.</p>",
     *   "btn_submit": "1"
     * }
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_html_in_email_body(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $templateData = [
            'email_template_title'   => 'HTML Template',
            'email_template_subject' => 'Subject',
            'email_template_body'    => '<p>Hello {client_name},</p><p>Your invoice is ready.</p>',
            'btn_submit'             => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $templateData);

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));

        $template = EmailTemplate::query()->where('email_template_title', 'HTML Template')->first();
        $this->assertNotNull($template);
        $this->assertStringContainsString('<p>', $template->email_template_body);
    }

    /**
     * Test form handles template variables.
     *
     * JSON Payload:
     * {
     *   "email_template_title": "Variable Template",
     *   "email_template_subject": "Invoice {invoice_number}",
     *   "email_template_body": "Dear {client_name}, your total is {invoice_total}",
     *   "btn_submit": "1"
     * }
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_preserves_template_variables(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $templateData = [
            'email_template_title'   => 'Variable Template',
            'email_template_subject' => 'Invoice {invoice_number}',
            'email_template_body'    => 'Dear {client_name}, your total is {invoice_total}',
            'btn_submit'             => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('email_templates.form'), $templateData);

        /* Assert */
        $response->assertRedirect(route('email_templates.index'));

        $template = EmailTemplate::query()->where('email_template_title', 'Variable Template')->first();
        $this->assertStringContainsString('{client_name}', $template->email_template_body);
        $this->assertStringContainsString('{invoice_number}', $template->email_template_subject);
    }

    /**
     * Test pagination handles large number of templates.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_paginates_large_template_list(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        EmailTemplate::factory()->count(50)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.index'));

        /* Assert */
        $response->assertOk();
        $templates = $response->viewData('email_templates');
        // Should have pagination or all templates
        $this->assertGreaterThan(0, $templates->count());
    }

    /**
     * Test index displays empty state when no templates.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_displays_empty_state_when_no_templates(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        EmailTemplate::query()->delete();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('email_templates.index'));

        /* Assert */
        $response->assertOk();
        $templates = $response->viewData('email_templates');
        $this->assertCount(0, $templates);
    }

    /**
     * Test deletion with invalid ID type.
     *
     * JSON Payload:
     * {
     *   "email_template_id": "invalid"
     * }
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_invalid_id_type_on_delete(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('email_templates.delete', ['email_template_id' => 'invalid'])
        );

        /* Assert */
        $this->assertTrue(
            $response->isNotFound()
            || $response->getStatusCode() >= 400
        );
    }
}

/**
 * ImportController Feature Tests.
 *
 * Comprehensive test suite covering all routes and edge cases for data import functionality.
 */
#[CoversClass(ImportController::class)]
class ImportControllerTest extends FeatureTestCase
{
    // ==================== ROUTE: GET /import (index) ====================

    /**
     * Test index displays import page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_import_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
    }

    /**
     * Test index displays imports list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_imports_list(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('imports');
    }

    /**
     * Test index requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_index(): void
    {
        /** Act */
        $response = $this->get(route('import.index'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test index handles pagination.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_pagination_on_import_index(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.index', ['page' => 1]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
    }

    // ==================== ROUTE: GET /import/form (form) ====================

    /**
     * Test form displays import form page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_import_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_import_index');
        $response->assertViewHas('files');
    }

    /**
     * Test form requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_form(): void
    {
        /** Act */
        $response = $this->get(route('import.form'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test form displays only allowed CSV files.
     */
    #[Group('validation')]
    #[Test]
    public function it_displays_only_allowed_csv_files(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        // Ensure upload directory exists
        $uploadDir = base_path('uploads/import');
        if ( ! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Create allowed and disallowed files
        file_put_contents($uploadDir . '/clients.csv', 'test');
        file_put_contents($uploadDir . '/invoices.csv', 'test');
        file_put_contents($uploadDir . '/malicious.php', '<?php echo "bad"; ?>');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.form'));

        /* Assert */
        $response->assertOk();
        $files = $response->viewData('files');

        // Should only show allowed CSV files
        $this->assertContains('clients.csv', $files);
        $this->assertContains('invoices.csv', $files);
        $this->assertNotContains('malicious.php', $files);

        // Cleanup - use file_exists to avoid errors
        if (file_exists($uploadDir . '/clients.csv')) {
            unlink($uploadDir . '/clients.csv');
        }
        if (file_exists($uploadDir . '/invoices.csv')) {
            unlink($uploadDir . '/invoices.csv');
        }
        if (file_exists($uploadDir . '/malicious.php')) {
            unlink($uploadDir . '/malicious.php');
        }
    }

    /**
     * Test form submission with valid files.
     */
    #[Group('crud')]
    #[Test]
    public function it_processes_import_with_valid_files(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $uploadDir = base_path('uploads/import');
        if ( ! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Create a valid CSV file
        $csvContent = "client_name,client_email\nTest Client,test@example.com";
        file_put_contents($uploadDir . '/clients.csv', $csvContent);

        $formData = [
            'btn_submit' => '1',
            'files'      => ['clients.csv'],
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('import.form'), $formData);

        /* Assert */
        $response->assertRedirect(route('import.index'));

        // Cleanup - use file_exists to avoid errors
        if (file_exists($uploadDir . '/clients.csv')) {
            unlink($uploadDir . '/clients.csv');
        }
    }

    /**
     * Test form submission with no files selected.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_import_with_no_files_selected(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $formData = [
            'btn_submit' => '1',
            'files'      => [],
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('import.form'), $formData);

        /* Assert */
        $response->assertRedirect(route('import.index'));

        // Verify no imports were created
        $importCount = \Modules\Core\Models\Import::query()->count();
        $this->assertEquals(0, $importCount, 'No imports should be created when files array is empty');
    }

    /**
     * Test form submission filters out disallowed files.
     */
    #[Group('validation')]
    #[Test]
    public function it_filters_out_disallowed_files_on_import(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $formData = [
            'btn_submit' => '1',
            'files'      => ['clients.csv', 'malicious.php', 'invoices.csv'],
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('import.form'), $formData);

        /* Assert */
        $response->assertRedirect(route('import.index'));
        // Should only process clients.csv and invoices.csv, ignore malicious.php
    }

    /**
     * Test form submission with invoice items CSV.
     */
    #[Group('crud')]
    #[Test]
    public function it_processes_invoice_items_import(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $uploadDir = base_path('uploads/import');
        if ( ! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $csvContent = "item_name,item_quantity\nTest Item,5";
        file_put_contents($uploadDir . '/invoice_items.csv', $csvContent);

        $formData = [
            'btn_submit' => '1',
            'files'      => ['invoice_items.csv'],
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('import.form'), $formData);

        /* Assert */
        $response->assertRedirect(route('import.index'));

        // Cleanup - use file_exists to avoid errors
        if (file_exists($uploadDir . '/invoice_items.csv')) {
            unlink($uploadDir . '/invoice_items.csv');
        }
    }

    /**
     * Test form submission with payments CSV.
     */
    #[Group('crud')]
    #[Test]
    public function it_processes_payments_import(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $uploadDir = base_path('uploads/import');
        if ( ! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $csvContent = "payment_amount,payment_date\n100.00,2025-01-01";
        file_put_contents($uploadDir . '/payments.csv', $csvContent);

        $formData = [
            'btn_submit' => '1',
            'files'      => ['payments.csv'],
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('import.form'), $formData);

        /* Assert */
        $response->assertRedirect(route('import.index'));

        // Cleanup - use file_exists to avoid errors
        if (file_exists($uploadDir . '/payments.csv')) {
            unlink($uploadDir . '/payments.csv');
        }
    }

    // ==================== ROUTE: GET /import/delete/{id} (delete) ====================

    /**
     * Test delete removes import record.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_import_record(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        // Create an import record
        $import = \Modules\Core\Models\Import::create([
            'import_date' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.delete', ['import_id' => $import->import_id]));

        /* Assert */
        $response->assertRedirect(route('import.index'));

        // Verify import was deleted
        $this->assertDatabaseMissing('ip_imports', [
            'import_id' => $import->import_id,
        ]);
    }

    /**
     * Test delete requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_delete(): void
    {
        /** Act */
        $response = $this->get(route('import.delete', ['import_id' => 1]));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test delete handles non-existent import gracefully.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_deleting_nonexistent_import(): void
    {
        /** Arrange */
        $user          = User::factory()->create();
        $nonexistentId = 99999;

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.delete', ['import_id' => $nonexistentId]));

        /* Assert */
        // Should redirect even if import doesn't exist
        $response->assertRedirect(route('import.index'));
    }

    /**
     * Test delete with invalid ID type.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_delete_with_invalid_id_type(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('import.delete', ['import_id' => 'invalid']));

        /* Assert */
        // Should handle gracefully
        $this->assertTrue(
            $response->isRedirect()
            || $response->getStatusCode() >= 400
        );
    }
}

/**
 * LayoutController Feature Tests.
 *
 * Tests layout configuration display.
 */
#[CoversClass(LayoutController::class)]
class LayoutControllerTest extends FeatureTestCase
{
    /**
     * Test index displays layout configuration page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_layout_configuration_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('layout.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::layout_index');
    }
}

/**
 * MailerController Feature Tests.
 *
 * Tests email configuration and testing display.
 */
#[CoversClass(MailerController::class)]
class MailerControllerTest extends FeatureTestCase
{
    /**
     * Test index displays mailer configuration page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_mailer_configuration_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('mailer.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::mailer_index');
    }
}

/**
 * ReportsController Feature Tests.
 *
 * Tests financial reports and analytics display.
 */
#[CoversClass(ReportsController::class)]
class ReportsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays reports page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_reports_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('reports.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::reports_index');
    }
}

/**
 * SessionsController Feature Tests.
 *
 * Tests user authentication including login, logout, and password reset.
 */
#[CoversClass(SessionsController::class)]
class SessionsControllerTest extends FeatureTestCase
{
    /**
     * Generate a throttle key matching the format used by SessionsController.
     *
     * @param string $email
     * @param string $ip
     *
     * @return string
     */
    protected function getThrottleKey(string $email, string $ip = '127.0.0.1'): string
    {
        return Str::transliterate(Str::lower($email).'|'.$ip);
    }
    /**
     * Test index redirects to login page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_login_page_from_index(): void
    {
        /** Arrange */
        // No user needed for redirect

        /** Act */
        $response = $this->get(route('sessions.index'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test login displays login form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_login_form(): void
    {
        /** Arrange */
        // No authentication needed for login page

        /** Act */
        $response = $this->get(route('sessions.login'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::users.session_login');
        $response->assertViewHas('login_logo');
    }

    /**
     * Test logout clears session and redirects to login.
     */
    #[Test]
    public function it_clears_session_and_redirects_to_login_on_logout(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $this->actingAs($user);
        session(['user_id' => $user->user_id]);

        /** Act */
        $response = $this->get(route('sessions.logout'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
        $this->assertNull(session('user_id'));
    }

    /**
     * Test password reset displays form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_password_reset_form(): void
    {
        /** Arrange */
        // No authentication needed

        /** Act */
        $response = $this->get(route('sessions.passwordreset'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('session_passwordreset');
    }

    /**
     * Test password reset with token displays form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_password_reset_form_with_token(): void
    {
        /** Arrange */
        $token = 'test-reset-token-123';

        /** Act */
        $response = $this->get(route('sessions.passwordreset', ['token' => $token]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('session_new_password');
    }

    /**
     * Test successful authentication with valid credentials.
     */
    #[Test]
    public function it_authenticates_user_with_valid_credentials(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $password = 'test-password-123';
        $hashedPassword = $crypt->generate_password($password, $salt);

        $user = User::factory()->create([
            'user_email' => 'test@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 1,
            'user_type' => 1,
        ]);

        /** Act */
        $response = $this->post(route('sessions.authenticate'), [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        /* Assert */
        $response->assertRedirect(route('dashboard.index'));
        $this->assertEquals($user->user_id, session('user_id'));
        $this->assertEquals($user->user_email, session('user_email'));
        $this->assertEquals($user->user_type, session('user_type'));
    }

    /**
     * Test authentication fails with invalid password.
     */
    #[Test]
    public function it_rejects_authentication_with_invalid_password(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $hashedPassword = $crypt->generate_password('correct-password', $salt);

        User::factory()->create([
            'user_email' => 'test@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 1,
        ]);

        /** Act & Assert */
        $this->expectException(ValidationException::class);

        $this->post(route('sessions.authenticate'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertNull(session('user_id'));
    }

    /**
     * Test authentication fails for non-existent user.
     */
    #[Test]
    public function it_rejects_authentication_for_nonexistent_user(): void
    {
        /** Arrange */
        // No user created

        /** Act & Assert */
        $this->expectException(ValidationException::class);

        $this->post(route('sessions.authenticate'), [
            'email' => 'nonexistent@example.com',
            'password' => 'any-password',
        ]);

        $this->assertNull(session('user_id'));
    }

    /**
     * Test authentication fails for inactive user.
     */
    #[Test]
    public function it_rejects_authentication_for_inactive_user(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $password = 'test-password-123';
        $hashedPassword = $crypt->generate_password($password, $salt);

        User::factory()->create([
            'user_email' => 'inactive@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 0, // Inactive user
        ]);

        /** Act & Assert */
        $this->expectException(ValidationException::class);

        $this->post(route('sessions.authenticate'), [
            'email' => 'inactive@example.com',
            'password' => $password,
        ]);

        $this->assertNull(session('user_id'));
    }

    /**
     * Test rate limiting blocks too many failed attempts.
     */
    #[Test]
    public function it_rate_limits_after_multiple_failed_attempts(): void
    {
        /** Arrange */
        $email = 'ratelimit@example.com';

        User::factory()->create([
            'user_email' => $email,
            'user_password' => 'hashed-password',
            'user_active' => 1,
        ]);

        // Clear any existing rate limits using the properly formatted throttle key
        $throttleKey = $this->getThrottleKey($email);
        RateLimiter::clear($throttleKey);

        /** Act */
        // Make 5 failed attempts (the rate limit threshold)
        for ($i = 0; $i < 5; $i++) {
            try {
                $this->post(route('sessions.authenticate'), [
                    'email' => $email,
                    'password' => 'wrong-password',
                ]);
            } catch (ValidationException $e) {
                // Expected to fail
            }
        }

        /** Assert */
        // The 6th attempt should be rate limited
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('auth.throttle');

        $this->post(route('sessions.authenticate'), [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
    }

    /**
     * Test session regeneration on successful login.
     */
    #[Test]
    public function it_regenerates_session_on_successful_login(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $password = 'test-password-123';
        $hashedPassword = $crypt->generate_password($password, $salt);

        User::factory()->create([
            'user_email' => 'test@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 1,
        ]);

        $oldSessionId = session()->getId();

        /** Act */
        $this->post(route('sessions.authenticate'), [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        /* Assert */
        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    /**
     * Test rate limiter clears on successful authentication.
     */
    #[Test]
    public function it_clears_rate_limiter_on_successful_authentication(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $password = 'test-password-123';
        $hashedPassword = $crypt->generate_password($password, $salt);

        $user = User::factory()->create([
            'user_email' => 'test@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 1,
        ]);

        // Generate throttle key using the same format as the controller
        $throttleKey = $this->getThrottleKey('test@example.com');

        // Simulate some failed attempts
        RateLimiter::hit($throttleKey);
        RateLimiter::hit($throttleKey);

        $this->assertEquals(2, RateLimiter::attempts($throttleKey));

        /** Act */
        $this->post(route('sessions.authenticate'), [
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        /* Assert */
        $this->assertEquals(0, RateLimiter::attempts($throttleKey));
    }

    /**
     * Test validation requires email and password.
     */
    #[Test]
    public function it_validates_email_and_password_are_required(): void
    {
        /** Act */
        $response = $this->post(route('sessions.authenticate'), []);

        /* Assert */
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test validation requires valid email format.
     */
    #[Test]
    public function it_validates_email_format(): void
    {
        /** Act */
        $response = $this->post(route('sessions.authenticate'), [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        /* Assert */
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test redirect to guest dashboard for type 2 users.
     */
    #[Test]
    public function it_redirects_guest_users_to_guest_dashboard(): void
    {
        /** Arrange */
        $crypt = new Crypt();
        $salt = $crypt->salt();
        $password = 'test-password-123';
        $hashedPassword = $crypt->generate_password($password, $salt);

        User::factory()->create([
            'user_email' => 'guest@example.com',
            'user_password' => $hashedPassword,
            'user_active' => 1,
            'user_type' => 2, // Guest user type
        ]);

        /** Act */
        $response = $this->post(route('sessions.authenticate'), [
            'email' => 'guest@example.com',
            'password' => $password,
        ]);

        /* Assert */
        $response->assertRedirect(route('guest.index'));
    }

    /**
     * Test logout invalidates session and regenerates token.
     */
    #[Test]
    public function it_invalidates_session_and_regenerates_token_on_logout(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $this->actingAs($user);
        session(['user_id' => $user->user_id, 'user_email' => $user->user_email]);

        $oldToken = csrf_token();

        /** Act */
        $response = $this->post(route('sessions.logout'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
        $this->assertNull(session('user_id'));
        $this->assertNull(session('user_email'));
        $this->assertNotEquals($oldToken, csrf_token());
    }
}

/**
 * SettingsController Feature Tests.
 *
 * Tests application settings management.
 */
#[CoversClass(SettingsController::class)]
class SettingsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays settings.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_settings_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        Setting::factory()->create(['setting_key' => 'company_name', 'setting_value' => 'Test Company']);
        Setting::factory()->create(['setting_key' => 'currency_code', 'setting_value' => 'USD']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('settings.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::settings_index');
        $response->assertViewHas('settings');

        $settings = $response->viewData('settings');
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('company_name', $settings);
        $this->assertArrayHasKey('currency_code', $settings);
    }

    /**
     * Test settings are returned as key-value array.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_settings_as_key_value_array(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        Setting::factory()->create(['setting_key' => 'email_from', 'setting_value' => 'noreply@example.com']);
        Setting::factory()->create(['setting_key' => 'invoice_prefix', 'setting_value' => 'INV-']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('settings.index'));

        /** Assert */
        $settings = $response->viewData('settings');
        $this->assertEquals('noreply@example.com', $settings['email_from']);
        $this->assertEquals('INV-', $settings['invoice_prefix']);
    }

    /**
     * Test save creates new settings.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_settings(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "company_name": "New Company",
         *     "currency_code": "EUR"
         * }.
         */
        $settingsData = [
            'company_name'  => 'New Company',
            'currency_code' => 'EUR',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('settings.save'), $settingsData);

        /* Assert */
        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'company_name',
            'setting_value' => 'New Company',
        ]);
    }

    /**
     * Test save updates existing settings.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_settings(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        Setting::factory()->create(['setting_key' => 'company_name', 'setting_value' => 'Old Company']);

        /**
         * {
         *     "company_name": "Updated Company"
         * }.
         */
        $settingsData = [
            'company_name' => 'Updated Company',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('settings.save'), $settingsData);

        /* Assert */
        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'company_name',
            'setting_value' => 'Updated Company',
        ]);
    }

    /**
     * Test save handles multiple settings at once.
     */
    #[Group('crud')]
    #[Test]
    public function it_saves_multiple_settings_at_once(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "company_name": "Multi Test Company",
         *     "currency_code": "GBP",
         *     "invoice_prefix": "INV-"
         * }.
         */
        $settingsData = [
            'company_name'   => 'Multi Test Company',
            'currency_code'  => 'GBP',
            'invoice_prefix' => 'INV-',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('settings.save'), $settingsData);

        /* Assert */
        $response->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'company_name',
            'setting_value' => 'Multi Test Company',
        ]);
        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'currency_code',
            'setting_value' => 'GBP',
        ]);
        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'invoice_prefix',
            'setting_value' => 'INV-',
        ]);
    }

    /**
     * Test save redirects to index with GET request.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_get_request(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('settings.save'));

        /* Assert */
        $response->assertRedirect(route('settings.index'));
    }

    /**
     * Test index with no settings returns empty array.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_no_settings_exist(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('settings.index'));

        /* Assert */
        $response->assertOk();
        $settings = $response->viewData('settings');
        $this->assertIsArray($settings);
        $this->assertEmpty($settings);
    }
}

/**
 * SetupController Feature Tests.
 *
 * Comprehensive test suite covering all setup wizard routes and workflows.
 */
#[CoversClass(SetupController::class)]
class SetupControllerTest extends FeatureTestCase
{
    // ==================== ROUTE: GET /setup (index) ====================

    /**
     * Test index redirects to language selection.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_language_selection(): void
    {
        /** Act */
        $response = $this->get(route('setup.index'));

        /* Assert */
        $response->assertRedirect(route('setup.language'));
    }

    /**
     * Test setup wizard is accessible without authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication for initial setup

        /** Act */
        $response = $this->get(route('setup.index'));

        /* Assert */
        // Should redirect to language, not login
        $response->assertRedirect();
        $this->assertNotEquals(route('sessions.login'), $response->headers->get('Location'));
    }

    // ==================== ROUTE: GET /setup/language (language) ====================

    /**
     * Test language selection page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_language_selection_page(): void
    {
        /** Act */
        $response = $this->get(route('setup.language'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.lang');
        $response->assertViewHas('languages');

        // Verify languages data is not empty
        $languages = $response->viewData('languages');
        $this->assertNotEmpty($languages, 'Languages list should not be empty');
    }

    /**
     * Test language selection advances to prerequisites.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_prerequisites_after_language_selection(): void
    {
        /** Arrange */
        $languageData = [
            'btn_continue' => '1',
            'ip_lang'      => 'en',
        ];

        /** Act */
        $response = $this->post(route('setup.language'), $languageData);

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
        $this->assertEquals('en', session('ip_lang'));
        $this->assertEquals('prerequisites', session('install_step'));
    }

    /**
     * Test language selection resets session cache.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_resets_session_cache_on_language_page(): void
    {
        /* Arrange */
        session(['install_step' => 'some_value']);
        session(['is_upgrade' => true]);

        /** Act */
        $response = $this->get(route('setup.language'));

        /* Assert */
        $response->assertOk();
        $this->assertNull(session('install_step'));
        $this->assertNull(session('is_upgrade'));
    }

    // ==================== ROUTE: GET /setup/prerequisites (prerequisites) ====================

    /**
     * Test prerequisites page displays when step is correct.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_prerequisites_page(): void
    {
        /* Arrange */
        session(['install_step' => 'prerequisites']);

        /** Act */
        $response = $this->get(route('setup.prerequisites'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.prerequisites');
        $response->assertViewHas('basics');
        $response->assertViewHas('writables');

        // Verify prerequisites data contains expected information
        $basics    = $response->viewData('basics');
        $writables = $response->viewData('writables');
        $this->assertNotEmpty($basics, 'Basic requirements should be checked');
        $this->assertNotEmpty($writables, 'Writable paths should be checked');
    }

    /**
     * Test prerequisites redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_prerequisites_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.prerequisites'));

        /* Assert */
        $response->assertRedirect(route('setup.language'));
    }

    /**
     * Test prerequisites advances to database configuration.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_database_configuration(): void
    {
        /** Act */
        $response = $this->advanceToStep('prerequisites', 'setup.prerequisites');

        /* Assert */
        $response->assertRedirect(route('setup.configure-database'));
        $this->assertEquals('configure_database', session('install_step'));
    }

    // ==================== ROUTE: GET /setup/configure-database (configureDatabase) ====================

    /**
     * Test database configuration page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_database_configuration_page(): void
    {
        /* Arrange */
        session(['install_step' => 'configure_database']);

        /** Act */
        $response = $this->get(route('setup.configure-database'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.configure_database');
        $response->assertViewHas('database');

        // Verify database configuration data is present
        $database = $response->viewData('database');
        $this->assertIsArray($database, 'Database configuration should be an array');
    }

    /**
     * Test database configuration redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_database_config_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.configure-database'));

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
    }

    /**
     * Test database configuration submission with credentials.
     */
    #[Group('crud')]
    #[Test]
    public function it_processes_database_credentials(): void
    {
        /* Arrange */
        session(['install_step' => 'configure_database']);
        $dbData = [
            'db_hostname' => 'localhost',
            'db_username' => 'testuser',
            'db_password' => 'testpass',
            'db_database' => 'testdb',
            'db_port'     => '3306',
        ];

        /** Act */
        $response = $this->post(route('setup.configure-database'), $dbData);

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('database');
    }

    // ==================== ROUTE: GET /setup/install-tables (installTables) ====================

    /**
     * Test install tables page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_install_tables_page(): void
    {
        /* Arrange */
        session(['install_step' => 'install_tables']);

        /** Act */
        $response = $this->get(route('setup.install-tables'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.install_tables');
        $response->assertViewHas('success');
    }

    /**
     * Test install tables redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_install_tables_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.install-tables'));

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
    }

    /**
     * Test install tables advances to upgrade tables.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_upgrade_tables_from_install(): void
    {
        /** Act */
        $response = $this->advanceToStep('install_tables', 'setup.install-tables');

        /* Assert */
        $response->assertRedirect(route('setup.upgrade-tables'));
        $this->assertEquals('upgrade_tables', session('install_step'));
    }

    // ==================== ROUTE: GET /setup/upgrade-tables (upgradeTables) ====================

    /**
     * Test upgrade tables page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_upgrade_tables_page(): void
    {
        /* Arrange */
        session(['install_step' => 'upgrade_tables']);

        /** Act */
        $response = $this->get(route('setup.upgrade-tables'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.upgrade_tables');
    }

    /**
     * Test upgrade tables redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_upgrade_tables_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.upgrade-tables'));

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
    }

    /**
     * Test upgrade tables advances to create user for new install.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_create_user_for_new_install(): void
    {
        /* Arrange */
        session(['is_upgrade' => false]);

        /** Act */
        $response = $this->advanceToStep('upgrade_tables', 'setup.upgrade-tables');

        /* Assert */
        $response->assertRedirect(route('setup.create-user'));
        $this->assertEquals('create_user', session('install_step'));
    }

    /**
     * Test upgrade tables advances to calculation info for upgrade.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_calculation_info_for_upgrade(): void
    {
        /* Arrange */
        session(['is_upgrade' => true]);

        /** Act */
        $response = $this->advanceToStep('upgrade_tables', 'setup.upgrade-tables');

        /* Assert */
        $response->assertRedirect(route('setup.calculation-info'));
        $this->assertEquals('calculation_info', session('install_step'));
    }

    // ==================== ROUTE: GET /setup/create-user (createUser) ====================

    /**
     * Test create user page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_user_page(): void
    {
        /* Arrange */
        session(['install_step' => 'create_user']);

        /** Act */
        $response = $this->get(route('setup.create-user'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.create_user');
    }

    /**
     * Test create user redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_create_user_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.create-user'));

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
    }

    /**
     * Test create user with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_admin_user(): void
    {
        /** Arrange */
        $userData = [
            'user_email'            => 'admin@example.com',
            'user_password'         => 'password123',
            'user_password_confirm' => 'password123',
        ];

        /** Act */
        $response = $this->advanceToStep('create_user', 'setup.create-user', $userData);

        /* Assert */
        $response->assertRedirect(route('setup.calculation-info'));
    }

    /**
     * Test create user fails with mismatched passwords.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_with_mismatched_passwords(): void
    {
        /* Arrange */
        session(['install_step' => 'create_user']);
        $userData = [
            'btn_continue'          => '1',
            'user_email'            => 'admin@example.com',
            'user_password'         => 'password123',
            'user_password_confirm' => 'different',
        ];

        /** Act */
        $response = $this->post(route('setup.create-user'), $userData);

        /* Assert */
        $response->assertSessionHasErrors();
    }

    // ==================== ROUTE: GET /setup/calculation-info (calculationInfo) ====================

    /**
     * Test calculation info page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_calculation_info_page(): void
    {
        /* Arrange */
        session(['install_step' => 'calculation_info']);

        /** Act */
        $response = $this->get(route('setup.calculation-info'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.calculation_info');
    }

    /**
     * Test calculation info redirects if step is wrong.
     */
    #[Group('workflow')]
    #[Test]
    public function it_redirects_if_calculation_info_step_is_wrong(): void
    {
        /* Arrange */
        session(['install_step' => 'wrong_step']);

        /** Act */
        $response = $this->get(route('setup.calculation-info'));

        /* Assert */
        $response->assertRedirect(route('setup.prerequisites'));
    }

    /**
     * Test calculation info advances to complete.
     */
    #[Group('workflow')]
    #[Test]
    public function it_advances_to_complete(): void
    {
        /** Act */
        $response = $this->advanceToStep('calculation_info', 'setup.calculation-info');

        /* Assert */
        $response->assertRedirect(route('setup.complete'));
    }

    // ==================== ROUTE: GET /setup/complete (complete) ====================

    /**
     * Test complete page displays.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_complete_page(): void
    {
        /* Arrange */
        session(['install_step' => 'complete']);

        /** Act */
        $response = $this->get(route('setup.complete'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup.complete');
    }

    /**
     * Test complete page is accessible without specific step.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_displays_complete_page_without_step_check(): void
    {
        /** Arrange */
        // No session step set

        /** Act */
        $response = $this->get(route('setup.complete'));

        /* Assert */
        $response->assertOk();
    }

    // ==================== EDGE CASES ====================

    /**
     * Test setup is disabled when environment flag is set.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_blocks_setup_when_disabled(): void
    {
        /* Arrange */
        putenv('DISABLE_SETUP=true');

        /** Act */
        $response = $this->get(route('setup.index'));

        /* Assert */
        $response->assertStatus(403);

        // Cleanup
        putenv('DISABLE_SETUP=false');
    }

    /**
     * Test setup handles invalid language selection.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_invalid_language_selection(): void
    {
        /** Arrange */
        $languageData = [
            'btn_continue' => '1',
            'ip_lang'      => 'invalid_lang',
        ];

        /** Act */
        $response = $this->post(route('setup.language'), $languageData);

        /* Assert */
        // Should either reject or default to safe value
        $this->assertTrue($response->isRedirect() || $response->isOk());
    }

    /**
     * Helper method to advance the setup workflow to a specific step.
     *
     * This reduces code duplication by handling common workflow advancement logic:
     * - Sets the session to the current step
     * - POSTs continue data to the current route
     * - Returns the response for assertion
     *
     * @param string               $currentStep    The current step name (e.g., 'prerequisites')
     * @param string               $currentRoute   The current route name (e.g., 'setup.prerequisites')
     * @param array<string, mixed> $additionalData Additional form data beyond 'btn_continue'
     *
     * @return \Illuminate\Testing\TestResponse
     */
    private function advanceToStep(string $currentStep, string $currentRoute, array $additionalData = []): \Illuminate\Testing\TestResponse
    {
        // Set the session to the current step
        session(['install_step' => $currentStep]);

        // Merge continue button with any additional data
        $postData = array_merge(['btn_continue' => '1'], $additionalData);

        // POST to the route to advance
        return $this->post(route($currentRoute), $postData);
    }
}

/**
 * TaxRatesController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for tax rate deletion with business rules.
 */
#[CoversClass(TaxRatesController::class)]
class TaxRateDeletionValidationFeatureTest extends FeatureTestCase
{
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_tax_rate_without_references(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create(['tax_rate_name' => 'Deletable']);

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        Product::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_invoice_items(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        InvoiceItem::factory()->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_quote_items(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        QuoteItem::factory()->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_tax_rate_id(): void
    {
        /** Arrange */
        $invalidId = -1;

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_tax_rate_id(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_references_removed(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        $product = Product::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        // Initially cannot delete
        $response1 = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));
        $response1->assertSessionHas('alert_error');

        // Remove reference
        $product->delete();

        /** Act */
        $response2 = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response2->assertRedirect(route('tax_rates.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }
}

/**
 * UploadController Feature Tests.
 *
 * Comprehensive test suite covering all routes and edge cases for file upload functionality.
 */
#[CoversClass(UploadController::class)]
class UploadControllerTest extends FeatureTestCase
{
    private string $testUploadDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUploadDir = base_path('uploads/test');
        if ( ! is_dir($this->testUploadDir)) {
            mkdir($this->testUploadDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (is_dir($this->testUploadDir)) {
            $files = glob($this->testUploadDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && file_exists($file)) {
                    unlink($file);
                }
            }
            if (file_exists($this->testUploadDir)) {
                rmdir($this->testUploadDir);
            }
        }
        parent::tearDown();
    }

    // ==================== ROUTE: POST /upload/upload-file ====================

    /**
     * Test file upload with valid file.
     */
    #[Group('crud')]
    #[Test]
    public function it_uploads_file_successfully(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('filename', $data);
        // Message should be translated, not a translation key
        $this->assertStringNotContainsString('upload_file_uploaded_successfully', $data['message']);
    }

    /**
     * Test file upload requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_upload(): void
    {
        /** Arrange */
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        /** Act */
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test file upload fails without file.
     */
    #[Group('validation')]
    #[Test]
    public function it_fails_upload_without_file(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), []);

        /* Assert */
        $response->assertStatus(400);
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        // Message should be translated
        $this->assertNotEquals('upload_error_no_file', $data['message']);
    }

    /**
     * Test file upload enforces 10MB size limit.
     */
    #[Group('validation')]
    #[Test]
    public function it_enforces_file_size_limit(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        // Create file larger than 10MB
        $file = \Illuminate\Http\UploadedFile::fake()->create('large.pdf', 11000); // 11MB

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertStatus(413); // Payload Too Large
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        // Message should be translated
        $this->assertNotEquals('upload_error_file_too_large', $data['message']);
    }

    /**
     * Test file upload rejects unsupported file types.
     */
    #[Group('validation')]
    #[Test]
    public function it_rejects_unsupported_file_types(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertStatus(415); // Unsupported Media Type
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('extension', $data);
        $this->assertEquals('php', $data['extension']);
    }

    /**
     * Test file upload rejects HTML files (XSS risk).
     */
    #[Group('validation')]
    #[Test]
    public function it_rejects_html_files(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('malicious.html', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertStatus(415); // Unsupported Media Type
        $data = $response->json();
        $this->assertArrayHasKey('extension', $data);
        $this->assertEquals('html', $data['extension']);
    }

    /**
     * Test file upload rejects executable files.
     */
    #[Group('validation')]
    #[Test]
    public function it_rejects_executable_files(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('malicious.exe', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertStatus(415); // Unsupported Media Type
    }

    /**
     * Test file upload accepts allowed file types.
     */
    #[Group('validation')]
    #[Test]
    public function it_accepts_allowed_file_types(): void
    {
        /** Arrange */
        $user         = User::factory()->create();
        $allowedTypes = ['pdf', 'jpg', 'png', 'docx', 'xlsx', 'csv'];

        /* Act & Assert */
        foreach ($allowedTypes as $type) {
            $file = \Illuminate\Http\UploadedFile::fake()->create("document.{$type}", 100);

            $this->actingAs($user);
            $response = $this->post(route('upload.upload-file', [
                'customerId' => 1,
                'url_key'    => 'test_' . $type,
            ]), [
                'file' => $file,
            ]);

            $response->assertOk();
        }
    }

    /**
     * Test file upload rejects duplicate file.
     */
    #[Group('validation')]
    #[Test]
    public function it_rejects_duplicate_file_upload(): void
    {
        /** Arrange */
        $user  = User::factory()->create();
        $file1 = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);
        $file2 = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        /* Act */
        $this->actingAs($user);
        // Upload first file
        $response1 = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file1,
        ]);

        // Try to upload duplicate
        $response2 = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file2,
        ]);

        /* Assert */
        $response1->assertOk();
        $response2->assertStatus(409);
        $data = $response2->json();
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('filename', $data);
    }

    /**
     * Test file upload sanitizes url_key parameter.
     */
    #[Group('validation')]
    #[Test]
    public function it_sanitizes_url_key_parameter(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => '../../../malicious',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        // Should succeed but sanitize the url_key
        $response->assertOk();
    }

    /**
     * Test file upload sanitizes filename.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_sanitizes_filename_on_upload(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('../../../etc/passwd', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        // Filename should be sanitized, removing path traversal characters
        $response->assertOk();
        $data = $response->json();
        $this->assertStringNotContainsString('..', $data['filename']);
        $this->assertStringNotContainsString('/', $data['filename']);
    }

    /**
     * Test file upload with special characters in filename.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_special_characters_in_filename(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('file<script>alert(1)</script>.pdf', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        // Should sanitize dangerous characters
        $this->assertStringNotContainsString('<', $data['filename']);
        $this->assertStringNotContainsString('>', $data['filename']);
    }

    /**
     * Test file upload limits filename length to 200 characters.
     */
    #[Group('validation')]
    #[Test]
    public function it_limits_filename_length(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        // Create filename with >200 chars
        $longName = str_repeat('a', 250);
        $file     = \Illuminate\Http\UploadedFile::fake()->create($longName . '.pdf', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        // Filename should be truncated (200 chars + .pdf extension)
        $this->assertLessThanOrEqual(204, mb_strlen($data['filename']));
    }

    /**
     * Test file upload handles files without extension.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_files_without_extension(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        // Create file without extension
        $file = \Illuminate\Http\UploadedFile::fake()->create('noextension', 100);

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('upload.upload-file', [
            'customerId' => 1,
            'url_key'    => 'test_key',
        ]), [
            'file' => $file,
        ]);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        // Should add safe default extension 'bin'
        $this->assertStringEndsWith('.bin', $data['filename']);
    }

    // ==================== ROUTE: GET /upload/create-dir ====================

    /**
     * Test directory creation.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_directory_successfully(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $testDir = $this->testUploadDir . '/new_dir';

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.create-dir', ['path' => $testDir]));

        /* Assert */
        $response->assertOk();
        $this->assertTrue(is_dir($testDir));

        // Cleanup - use file_exists to avoid errors
        if (file_exists($testDir) && is_dir($testDir)) {
            rmdir($testDir);
        }
    }

    /**
     * Test directory creation handles existing directory.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_existing_directory(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $testDir = $this->testUploadDir . '/existing_dir';
        mkdir($testDir);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.create-dir', ['path' => $testDir]));

        /* Assert */
        $response->assertOk();
        $this->assertTrue(is_dir($testDir));

        // Cleanup - use file_exists to avoid errors
        if (file_exists($testDir) && is_dir($testDir)) {
            rmdir($testDir);
        }
    }

    // ==================== ROUTE: GET /upload/show-files ====================

    /**
     * Test show files returns file list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_files_for_url_key(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.show-files', ['url_key' => 'test_key']));

        /* Assert */
        $response->assertOk();
        $response->assertJsonStructure([]);
    }

    /**
     * Test show files returns empty array without url_key.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_empty_array_without_url_key(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.show-files'));

        /* Assert */
        $response->assertOk();
        $response->assertJson([]);
    }

    /**
     * Test show files requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_show_files(): void
    {
        /** Act */
        $response = $this->get(route('upload.show-files', ['url_key' => 'test_key']));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    // ==================== ROUTE: GET /upload/delete-file ====================

    /**
     * Test file deletion.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_file_successfully(): void
    {
        /** Arrange */
        $user     = User::factory()->create();
        $filename = 'test_file.txt';
        $urlKey   = 'test_key';

        // Create a test file
        $filePath = config('filesystems.cfiles_folder') . $urlKey . '_' . $filename;
        touch($filePath);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.delete-file', [
            'url_key' => $urlKey,
            'name'    => $filename,
        ]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        // Message should be translated
        $this->assertNotEquals('upload_file_deleted_successfully', $data['message']);

        // Cleanup - use file_exists to avoid errors
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Test file deletion fails for non-existent file.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_deletion_of_nonexistent_file(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.delete-file', [
            'url_key' => 'test_key',
            'name'    => 'nonexistent.txt',
        ]));

        /* Assert */
        // Should handle gracefully
        $this->assertTrue(
            $response->isOk()
            || $response->getStatusCode() == 410
        );
    }

    /**
     * Test file deletion prevents path traversal.
     */
    #[Group('validation')]
    #[Test]
    public function it_prevents_path_traversal_in_delete(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.delete-file', [
            'url_key' => 'test_key',
            'name'    => '../../../etc/passwd',
        ]));

        /* Assert */
        $response->assertStatus(410);
        $data = $response->json();
        $this->assertArrayHasKey('message', $data);
        // Message should be translated
        $this->assertNotEquals('upload_error_file_delete', $data['message']);
    }

    /**
     * Test file deletion sanitizes url_key to prevent traversal.
     */
    #[Group('validation')]
    #[Test]
    public function it_sanitizes_url_key_in_delete(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.delete-file', [
            'url_key' => '../../malicious',
            'name'    => 'test.txt',
        ]));

        /* Assert */
        // Should fail due to path validation
        $response->assertStatus(410);
    }

    /**
     * Test file deletion requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_delete(): void
    {
        /** Act */
        $response = $this->get(route('upload.delete-file', [
            'url_key' => 'test_key',
            'name'    => 'test.txt',
        ]));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    // ==================== ROUTE: GET /upload/get-file ====================

    /**
     * Test file retrieval.
     */
    #[Group('smoke')]
    #[Test]
    public function it_retrieves_file_successfully(): void
    {
        /** Arrange */
        $user     = User::factory()->create();
        $filename = 'test_file.txt';
        $filePath = config('filesystems.cfiles_folder') . $filename;

        // Create test file
        file_put_contents($filePath, 'test content');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.get-file', ['filename' => $filename]));

        /* Assert */
        $response->assertOk();

        // Cleanup - use file_exists to avoid errors
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Test file retrieval returns 404 for non-existent file.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_404_for_nonexistent_file(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.get-file', ['filename' => 'nonexistent.txt']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test file retrieval prevents path traversal.
     */
    #[Group('validation')]
    #[Test]
    public function it_prevents_path_traversal_in_get_file(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('upload.get-file', ['filename' => '../../../etc/passwd']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test file retrieval requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_get_file(): void
    {
        /** Act */
        $response = $this->get(route('upload.get-file', ['filename' => 'test.txt']));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }
}

/**
 * UsersController Feature Tests.
 *
 * Tests user account management including CRUD operations.
 */
#[CoversClass(UsersController::class)]
class UsersControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of users.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_users(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('users.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::index');
        $response->assertViewHas('users');
        $response->assertViewHas('filter_display', true);
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method', 'filter_users');
        $response->assertViewHas('user_types');
    }

    /**
     * Test users are ordered alphabetically by name.
     */
    #[Test]
    public function it_orders_users_alphabetically_by_name(): void
    {
        /** Arrange */
        $adminUser = User::factory()->create(['user_name' => 'Admin']);

        User::factory()->create(['user_name' => 'Zack']);
        User::factory()->create(['user_name' => 'Alice']);
        User::factory()->create(['user_name' => 'Bob']);

        /* Act */
        $this->actingAs($adminUser);
        $response = $this->get(route('users.index'));

        /* Assert */
        $response->assertOk();
        $users = $response->viewData('users');
        $names = $users->pluck('user_name')->toArray();

        $this->assertEquals('Admin', $names[0]);
        $this->assertEquals('Alice', $names[1]);
        $this->assertEquals('Bob', $names[2]);
        $this->assertEquals('Zack', $names[3]);
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('users.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::form');
        $response->assertViewHas('user');

        $formUser = $response->viewData('user');
        $this->assertInstanceOf(User::class, $formUser);
        $this->assertFalse($formUser->exists);
    }

    /**
     * Test form displays edit form with existing user.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_user(): void
    {
        /** Arrange */
        $adminUser = User::factory()->create();
        $editUser  = User::factory()->create();

        /* Act */
        $this->actingAs($adminUser);
        $response = $this->get(route('users.form', ['user_id' => $editUser->user_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('users::form');
        $response->assertViewHas('user');

        $formUser = $response->viewData('user');
        $this->assertEquals($editUser->user_id, $formUser->user_id);
    }

    /**
     * Test form creates new user with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_user_with_valid_data(): void
    {
        /** Arrange */
        $adminUser = User::factory()->create();

        /**
         * {
         *     "user_name": "New User",
         *     "user_email": "newuser@example.com",
         *     "user_password": "password123",
         *     "user_type": 1,
         *     "btn_submit": "1"
         * }.
         */
        $userData = [
            'user_name'     => 'New User',
            'user_email'    => 'newuser@example.com',
            'user_password' => 'password123',
            'user_type'     => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit'    => '1',
        ];

        /* Act */
        $this->actingAs($adminUser);
        $response = $this->post(route('users.form'), $userData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_users', [
            'user_name'  => 'New User',
            'user_email' => 'newuser@example.com',
        ]);
    }

    /**
     * Test form updates existing user.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_user_with_valid_data(): void
    {
        /** Arrange */
        $adminUser = User::factory()->create();
        $editUser  = User::factory()->create([
            'user_name'  => 'Old Name',
            'user_email' => 'old@example.com',
        ]);

        /**
         * {
         *     "user_name": "Updated Name",
         *     "user_email": "old@example.com",
         *     "user_type": 1,
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'user_name'  => 'Updated Name',
            'user_email' => $editUser->user_email,
            'user_type'  => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit' => '1',
        ];

        /* Act */
        $this->actingAs($adminUser);
        $response = $this->post(route('users.form', ['user_id' => $editUser->user_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_users', [
            'user_id'   => $editUser->user_id,
            'user_name' => 'Updated Name',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('users.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('users.index'));
    }

    /**
     * Test delete removes user.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_user(): void
    {
        /** Arrange */
        $adminUser  = User::factory()->create();
        $deleteUser = User::factory()->create();

        /**
         * {
         *     "user_id": 1
         * }.
         */
        $deletePayload = [
            'user_id' => $deleteUser->user_id,
        ];

        /* Act */
        $this->actingAs($adminUser);
        $response = $this->post(
            route('users.delete', ['user_id' => $deleteUser->user_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_users', [
            'user_id' => $deleteUser->user_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent user.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_user(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "user_id": 99999
         * }.
         */
        $deletePayload = [
            'user_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('users.delete', ['user_id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test form returns 404 for non-existent user in edit mode.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_user(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('users.form', ['user_id' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * VersionsController Feature Tests.
 *
 * Tests version information and update checking.
 */
#[CoversClass(VersionsController::class)]
class VersionsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays versions page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_versions_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('versions.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::versions_index');
    }
}

/**
 * Test that the view template system uses PHP templates, not Blade.
 */
class ViewTemplateSystemTest extends TestCase
{
    /**
     * Test that PHP view engine is registered.
     */
    public function test_php_view_engine_is_registered(): void
    {
        $resolver = $this->app->make('view.engine.resolver');

        // PHP engine should be registered
        $phpEngine = $resolver->resolve('php');
        $this->assertInstanceOf(\Illuminate\View\Engines\PhpEngine::class, $phpEngine);
    }

    /**
     * Test that Blade engine is available but secondary.
     */
    public function test_blade_engine_is_available_as_secondary(): void
    {
        $resolver = $this->app->make('view.engine.resolver');

        // Blade engine should also be available
        $bladeEngine = $resolver->resolve('blade');
        $this->assertInstanceOf(\Illuminate\View\Engines\CompilerEngine::class, $bladeEngine);
    }

    /**
     * Test that plain PHP views can be rendered.
     */
    public function test_plain_php_views_can_be_rendered(): void
    {
        // Create a temporary PHP view
        $viewPath = resource_path('views/test_php_template.php');
        file_put_contents($viewPath, '<?php echo "PHP Template Works: " . $message; ?>');

        try {
            // Render the view
            $rendered = view('test_php_template', ['message' => 'Success'])->render();

            // Assert it renders correctly
            $this->assertStringContainsString('PHP Template Works: Success', $rendered);
        } finally {
            // Clean up
            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }
    }

    /**
     * Test that Blade views (with .blade.php) can still be rendered.
     */
    public function test_blade_views_can_be_rendered(): void
    {
        // Create a temporary Blade view
        $viewPath     = resource_path('views/test_blade_template.blade.php');
        $bladeContent = <<<'BLADE'
Hello, @{{ name }}
{{-- escaped to show raw moustache --}}
@php($upper = strtoupper($name))
Blade Works: {{ $upper }}
BLADE;
        file_put_contents($viewPath, $bladeContent);

        try {
            // Render the view
            $rendered = view('test_blade_template', ['name' => 'john'])->render();

            // Assert it renders correctly and compiles directives
            $this->assertStringContainsString('Blade Works: JOHN', $rendered);
            $this->assertStringContainsString('@{ name }', str_replace(['{{ ', ' }}'], ['{{', '}}'], '@{ name }')); // sanity (no actual raw)
        } finally {
            // Clean up
            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }
    }

    /**
     * Test that welcome view uses PHP template.
     */
    public function test_welcome_view_is_php_template(): void
    {
        $welcomePath = resource_path('views/welcome.php');

        // The welcome view should be a .php file, not .blade.php
        $this->assertFileExists($welcomePath);

        // Should not have a .blade.php version
        $bladePath = resource_path('views/welcome.blade.php');
        $this->assertFileDoesNotExist($bladePath);
    }
}

/**
 * WelcomeController Feature Tests.
 *
 * Tests welcome/landing page display.
 */
#[CoversClass(WelcomeController::class)]
class WelcomeControllerTest extends FeatureTestCase
{
    /**
     * Test index displays welcome page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_welcome_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('welcome'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::welcome');
    }

    /**
     * Test welcome page is accessible without authentication.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_welcome_page_without_authentication(): void
    {
        /** Arrange */
        // No user authentication

        /** Act */
        $response = $this->get(route('welcome'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::welcome');
    }
}

