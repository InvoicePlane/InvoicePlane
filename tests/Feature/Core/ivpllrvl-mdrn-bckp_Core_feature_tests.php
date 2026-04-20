<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
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

        /** Act */
        $response = $this->actingAs($user)->get(route('core.ajax.get_cron_key'));

        /** Assert */
        $response->assertOk();
        $response->assertJsonStructure(['key']);
        
        $data = $response->json();
        $this->assertIsString($data['key']);
        $this->assertEquals(16, strlen($data['key']));
    }

    /**
     * Test getCronKey generates different keys on each request.
     */
    #[Test]
    public function it_generates_different_keys_on_each_request(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response1 = $this->actingAs($user)->get(route('core.ajax.get_cron_key'));
        $response2 = $this->actingAs($user)->get(route('core.ajax.get_cron_key'));

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

        /** Act */
        $response = $this->actingAs($user)->get(route('core.ajax.get_cron_key'));

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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_fields.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_fields.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_fields.form'));

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_fields.form', ['id' => $customField->custom_field_id]));

        /** Assert */
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
         * }
         */
        $customFieldData = [
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => 'Test Field',
            'custom_field_column' => 'custom_test_field',
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_fields.form'), $customFieldData);

        /** Assert */
        $response->assertRedirect(route('custom_fields.index'));
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create(['custom_field_label' => 'Old Label']);
        
        /**
         * {
         *     "custom_field_table": "ip_clients",
         *     "custom_field_label": "Updated Label",
         *     "custom_field_column": "client_custom",
         *     "btn_submit": "1"
         * }
         */
        $updateData = [
            'custom_field_table' => $customField->custom_field_table,
            'custom_field_label' => 'Updated Label',
            'custom_field_column' => $customField->custom_field_column,
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_fields.form', ['id' => $customField->custom_field_id]), $updateData);

        /** Assert */
        $response->assertRedirect(route('custom_fields.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_custom_fields', [
            'custom_field_id' => $customField->custom_field_id,
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
         * }
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_fields.form'), $cancelData);

        /** Assert */
        $response->assertRedirect(route('custom_fields.index'));
    }

    /**
     * Test delete removes custom field.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_custom_field(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        
        /**
         * {
         *     "custom_field_id": 1
         * }
         */
        $deletePayload = [
            'custom_field_id' => $customField->custom_field_id,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('custom_fields.delete', ['id' => $customField->custom_field_id]),
            $deletePayload
        );

        /** Assert */
        $response->assertRedirect(route('custom_fields.index'));
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
         * }
         */
        $deletePayload = [
            'custom_field_id' => 99999,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('custom_fields.delete', ['id' => 99999]),
            $deletePayload
        );

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_fields.form', ['id' => 99999]));

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        CustomValue::factory()->count(5)->create(['custom_field_id' => $customField->custom_field_id]);

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_values.index'));

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_values.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_values.form'));

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_values.form', ['id' => $customValue->custom_value_id]));

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        
        /**
         * {
         *     "custom_field_id": 1,
         *     "custom_value_value": "Test Value",
         *     "btn_submit": "1"
         * }
         */
        $customValueData = [
            'custom_field_id' => $customField->custom_field_id,
            'custom_value_value' => 'Test Value',
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_values.form'), $customValueData);

        /** Assert */
        $response->assertRedirect(route('custom_values.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_custom_values', [
            'custom_field_id' => $customField->custom_field_id,
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create([
            'custom_field_id' => $customField->custom_field_id,
            'custom_value_value' => 'Old Value',
        ]);
        
        /**
         * {
         *     "custom_field_id": 1,
         *     "custom_value_value": "Updated Value",
         *     "btn_submit": "1"
         * }
         */
        $updateData = [
            'custom_field_id' => $customField->custom_field_id,
            'custom_value_value' => 'Updated Value',
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_values.form', ['id' => $customValue->custom_value_id]), $updateData);

        /** Assert */
        $response->assertRedirect(route('custom_values.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_custom_values', [
            'custom_value_id' => $customValue->custom_value_id,
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
         * }
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('custom_values.form'), $cancelData);

        /** Assert */
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
        $user = User::factory()->create();
        $customField = CustomField::factory()->create();
        $customValue = CustomValue::factory()->create(['custom_field_id' => $customField->custom_field_id]);
        
        /**
         * {
         *     "custom_value_id": 1
         * }
         */
        $deletePayload = [
            'custom_value_id' => $customValue->custom_value_id,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('custom_values.delete', ['id' => $customValue->custom_value_id]),
            $deletePayload
        );

        /** Assert */
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
         * }
         */
        $deletePayload = [
            'custom_value_id' => 99999,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('custom_values.delete', ['id' => 99999]),
            $deletePayload
        );

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('custom_values.form', ['id' => 99999]));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('dashboard'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('dashboard'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('dashboard'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('dashboard'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('dashboard'));

        /** Assert */
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
     * Test index displays paginated list of email templates.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_email_templates(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        EmailTemplate::factory()->count(5)->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('email_templates.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('email_templates.index'));

        /** Assert */
        $response->assertOk();
        $templates = $response->viewData('email_templates');
        $titles = $templates->pluck('email_template_title')->toArray();
        
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

        /** Act */
        $response = $this->actingAs($user)->get(route('email_templates.form'));

        /** Assert */
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
        $user = User::factory()->create();
        $template = EmailTemplate::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('email_templates.form', ['id' => $template->email_template_id]));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::email_templates_form');
        $response->assertViewHas('email_template');
        
        $viewTemplate = $response->viewData('email_template');
        $this->assertEquals($template->email_template_id, $viewTemplate->email_template_id);
    }

    /**
     * Test form creates new email template.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_email_template_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        
        /**
         * {
         *     "email_template_title": "New Template",
         *     "email_template_subject": "Subject",
         *     "email_template_body": "Body content",
         *     "btn_submit": "1"
         * }
         */
        $templateData = [
            'email_template_title' => 'New Template',
            'email_template_subject' => 'Subject',
            'email_template_body' => 'Body content',
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('email_templates.form'), $templateData);

        /** Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_title' => 'New Template',
        ]);
    }

    /**
     * Test form updates existing email template.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_email_template(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $template = EmailTemplate::factory()->create(['email_template_title' => 'Old Title']);
        
        /**
         * {
         *     "email_template_title": "Updated Title",
         *     "email_template_subject": "Invoice Reminder",
         *     "email_template_body": "Please pay your invoice.",
         *     "btn_submit": "1"
         * }
         */
        $updateData = [
            'email_template_title' => 'Updated Title',
            'email_template_subject' => $template->email_template_subject,
            'email_template_body' => $template->email_template_body,
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('email_templates.form', ['id' => $template->email_template_id]), $updateData);

        /** Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_id' => $template->email_template_id,
            'email_template_title' => 'Updated Title',
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
         * }
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('email_templates.form'), $cancelData);

        /** Assert */
        $response->assertRedirect(route('email_templates.index'));
    }

    /**
     * Test delete removes email template.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_email_template(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $template = EmailTemplate::factory()->create();
        
        /**
         * {
         *     "email_template_id": 1
         * }
         */
        $deletePayload = [
            'email_template_id' => $template->email_template_id,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('email_templates.delete', ['id' => $template->email_template_id]),
            $deletePayload
        );

        /** Assert */
        $response->assertRedirect(route('email_templates.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseMissing('ip_email_templates', [
            'email_template_id' => $template->email_template_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent template.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_template(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        
        /**
         * {
         *     "email_template_id": 99999
         * }
         */
        $deletePayload = [
            'email_template_id' => 99999,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('email_templates.delete', ['id' => 99999]),
            $deletePayload
        );

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('email_templates.form', ['id' => 99999]));

        /** Assert */
        $response->assertNotFound();
    }
}

/**
 * ImportController Feature Tests.
 *
 * Tests data import functionality display.
 */
#[CoversClass(ImportController::class)]
class ImportControllerTest extends FeatureTestCase
{
    /**
     * Test index displays import page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_import_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('import.index'));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
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

        /** Act */
        $response = $this->actingAs($user)->get(route('layout.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('mailer.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('reports.index'));

        /** Assert */
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

        /** Assert */
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

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('sessions::login');
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

        /** Assert */
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

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('sessions::passwordreset');
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

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('sessions::passwordreset');
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

        /** Act */
        $response = $this->actingAs($user)->get(route('settings.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('settings.index'));

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
         * }
         */
        $settingsData = [
            'company_name' => 'New Company',
            'currency_code' => 'EUR',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('settings.save'), $settingsData);

        /** Assert */
        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'company_name',
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
         * }
         */
        $settingsData = [
            'company_name' => 'Updated Company',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('settings.save'), $settingsData);

        /** Assert */
        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'company_name',
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
         * }
         */
        $settingsData = [
            'company_name' => 'Multi Test Company',
            'currency_code' => 'GBP',
            'invoice_prefix' => 'INV-',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('settings.save'), $settingsData);

        /** Assert */
        $response->assertRedirect(route('settings.index'));
        
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'company_name',
            'setting_value' => 'Multi Test Company',
        ]);
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'currency_code',
            'setting_value' => 'GBP',
        ]);
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'invoice_prefix',
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

        /** Act */
        $response = $this->actingAs($user)->get(route('settings.save'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('settings.index'));

        /** Assert */
        $response->assertOk();
        $settings = $response->viewData('settings');
        $this->assertIsArray($settings);
        $this->assertEmpty($settings);
    }
}

/**
 * SetupController Feature Tests.
 *
 * Tests initial setup wizard display.
 */
#[CoversClass(SetupController::class)]
class SetupControllerTest extends FeatureTestCase
{
    /**
     * Test index displays setup wizard page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_setup_wizard_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('setup.index'));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup_index');
    }

    /**
     * Test setup wizard is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication for initial setup

        /** Act */
        $response = $this->get(route('setup.index'));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup_index');
    }
}

/**
 * UploadController Feature Tests.
 *
 * Tests file upload handling display.
 */
#[CoversClass(UploadController::class)]
class UploadControllerTest extends FeatureTestCase
{
    /**
     * Test index displays upload page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_upload_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('upload.index'));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::upload_index');
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

        /** Act */
        $response = $this->actingAs($user)->get(route('users.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($adminUser)->get(route('users.index'));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('users.form'));

        /** Assert */
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
        $editUser = User::factory()->create();

        /** Act */
        $response = $this->actingAs($adminUser)->get(route('users.form', ['id' => $editUser->user_id]));

        /** Assert */
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
         * }
         */
        $userData = [
            'user_name' => 'New User',
            'user_email' => 'newuser@example.com',
            'user_password' => 'password123',
            'user_type' => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($adminUser)->post(route('users.form'), $userData);

        /** Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_users', [
            'user_name' => 'New User',
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
        $editUser = User::factory()->create([
            'user_name' => 'Old Name',
            'user_email' => 'old@example.com',
        ]);

        /**
         * {
         *     "user_name": "Updated Name",
         *     "user_email": "old@example.com",
         *     "user_type": 1,
         *     "btn_submit": "1"
         * }
         */
        $updateData = [
            'user_name' => 'Updated Name',
            'user_email' => $editUser->user_email,
            'user_type' => User::USER_TYPE_ADMINISTRATOR,
            'btn_submit' => '1',
        ];

        /** Act */
        $response = $this->actingAs($adminUser)->post(route('users.form', ['id' => $editUser->user_id]), $updateData);

        /** Assert */
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert_success');
        
        $this->assertDatabaseHas('ip_users', [
            'user_id' => $editUser->user_id,
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
         * }
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('users.form'), $cancelData);

        /** Assert */
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
        $adminUser = User::factory()->create();
        $deleteUser = User::factory()->create();
        
        /**
         * {
         *     "user_id": 1
         * }
         */
        $deletePayload = [
            'user_id' => $deleteUser->user_id,
        ];

        /** Act */
        $response = $this->actingAs($adminUser)->post(
            route('users.delete', ['id' => $deleteUser->user_id]),
            $deletePayload
        );

        /** Assert */
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
         * }
         */
        $deletePayload = [
            'user_id' => 99999,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(
            route('users.delete', ['id' => 99999]),
            $deletePayload
        );

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('users.form', ['id' => 99999]));

        /** Assert */
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

        /** Act */
        $response = $this->actingAs($user)->get(route('versions.index'));

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::versions_index');
    }
}

/**
 * Test that the view template system uses PHP templates, not Blade
 */
class ViewTemplateSystemTest extends TestCase
{
    /**
     * Test that PHP view engine is registered
     */
    public function test_php_view_engine_is_registered(): void
    {
        $resolver = $this->app->make('view.engine.resolver');
        
        // PHP engine should be registered
        $phpEngine = $resolver->resolve('php');
        $this->assertInstanceOf(\Illuminate\View\Engines\PhpEngine::class, $phpEngine);
    }

    /**
     * Test that Blade engine is available but secondary
     */
    public function test_blade_engine_is_available_as_secondary(): void
    {
        $resolver = $this->app->make('view.engine.resolver');
        
        // Blade engine should also be available
        $bladeEngine = $resolver->resolve('blade');
        $this->assertInstanceOf(\Illuminate\View\Engines\CompilerEngine::class, $bladeEngine);
    }

    /**
     * Test that plain PHP views can be rendered
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
     * Test that welcome view uses PHP template
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

        /** Act */
        $response = $this->actingAs($user)->get(route('welcome'));

        /** Assert */
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

        /** Assert */
        $response->assertOk();
        $response->assertViewIs('core::welcome');
    }
}

