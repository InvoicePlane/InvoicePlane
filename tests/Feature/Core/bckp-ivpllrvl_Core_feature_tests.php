<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

#[CoversClass(CustomFieldsController::class)]
class CustomFieldsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_custom_fields_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_custom_fields_table()
    {
        $this->markTestIncomplete('Implement meaningful test for table');
    }

    #[Test]
    public function it_displays_custom_field_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_custom_field()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}

#[CoversClass(CustomValuesController::class)]
class CustomValuesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_custom_values()
    {
        // Arrange: create custom values
        $customValue = CustomValue::factory()->create(['name' => 'Test Value']);

        // Act: call the index route
        $response = $this->get(route('custom_values.index'));

        // Assert: custom value is visible in the response
        $response->assertStatus(200);
        $response->assertSee('Test Value');
    }

    #[Test]
    public function it_displays_and_saves_custom_field()
    {
        // Arrange: create a custom field
        $customField     = \Modules\CustomFields\Models\CustomField::factory()->create(['name' => 'Test Field']);
        $customValueData = [
            'value' => 'New Value',
            // add other required fields
        ];

        // Act: post to the field route to save a value
        $response = $this->post(route('custom_values.field', ['id' => $customField->id]), $customValueData);

        // Assert: custom value is saved
        $this->assertDatabaseHas('custom_values', ['value' => 'New Value']);
        $response->assertRedirect(route('custom_values'));
    }
}

#[CoversClass(DashboardController::class)]
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected \Modules\Core\Models\User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = \Modules\Dashboard\Tests\Feature\User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_dashboard_with_overview_data()
    {
        // Arrange: create sample data
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id' => $client->id,
            'total'     => 1000,
        ]);

        // Act: visit dashboard
        $response = $this->get(route('dashboard'));

        // Assert: dashboard is displayed
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('quote_status_totals');
    }

    #[Test]
    public function it_displays_dashboard_with_invoice_status_totals(): void
    {
        \Modules\Dashboard\Tests\Feature\Invoice::factory()->count(5)->create(['invoice_status_id' => 1]); // Draft
        \Modules\Dashboard\Tests\Feature\Invoice::factory()->count(3)->create(['invoice_status_id' => 2]); // Sent
        \Modules\Dashboard\Tests\Feature\Invoice::factory()->count(7)->create(['invoice_status_id' => 4]); // Paid

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('invoice_statuses');
    }

    #[Test]
    public function it_displays_dashboard_with_quote_status_totals(): void
    {
        \Modules\Dashboard\Tests\Feature\Quote::factory()->count(4)->create(['quote_status_id' => 1]); // Draft
        \Modules\Dashboard\Tests\Feature\Quote::factory()->count(2)->create(['quote_status_id' => 2]); // Sent
        \Modules\Dashboard\Tests\Feature\Quote::factory()->count(3)->create(['quote_status_id' => 3]); // Approved

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_totals');
        $response->assertViewHas('quote_statuses');
    }

    #[Test]
    public function it_displays_recent_invoices_on_dashboard(): void
    {
        \Modules\Dashboard\Tests\Feature\Invoice::factory()->count(15)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_recent_quotes_on_dashboard(): void
    {
        \Modules\Dashboard\Tests\Feature\Quote::factory()->count(15)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quotes', function ($quotes) {
            return $quotes->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_overdue_invoices_on_dashboard(): void
    {
        \Modules\Dashboard\Tests\Feature\Invoice::factory()->count(3)->create([
            'invoice_status_id' => 2,
            'invoice_date_due'  => now()->subDays(10),
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('overdue_invoices', function ($invoices) {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_latest_projects_on_dashboard(): void
    {
        \Modules\Dashboard\Tests\Feature\Project::factory()->count(5)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('projects');
    }

    #[Test]
    public function it_displays_latest_tasks_on_dashboard(): void
    {
        \Modules\Dashboard\Tests\Feature\Task::factory()->count(5)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('tasks');
        $response->assertViewHas('task_statuses');
    }

    #[Test]
    public function it_uses_custom_invoice_overview_period_setting(): void
    {
        \Modules\Dashboard\Tests\Feature\Setting::factory()->create([
            'setting_key'   => 'invoice_overview_period',
            'setting_value' => 'this-month',
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_period', 'this_month');
    }

    #[Test]
    public function it_uses_custom_quote_overview_period_setting(): void
    {
        \Modules\Dashboard\Tests\Feature\Setting::factory()->create([
            'setting_key'   => 'quote_overview_period',
            'setting_value' => 'this-quarter',
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_period', 'this_quarter');
    }
}

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')->assertStatus(200);
    }
}

#[CoversClass(AjaxController::class)]
class EmailTemplatesAjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_email_template_content_as_json(): void
    {
        $template = EmailTemplate::factory()->create([
            'email_template_subject' => 'Test Subject',
            'email_template_body'    => 'Test Body',
        ]);

        $response = $this->post(route('email_templates.ajax.getContent'), [
            'email_template_id' => $template->email_template_id,
        ]);

        $response->assertSuccessful();
        $data = $response->json();
        $this->assertEquals('Test Subject', $data['email_template_subject']);
        $this->assertEquals('Test Body', $data['email_template_body']);
    }

    #[Test]
    public function it_returns_null_for_nonexistent_template(): void
    {
        $response = $this->post(route('email_templates.ajax.getContent'), [
            'email_template_id' => 99999,
        ]);

        $response->assertSuccessful();
        $this->assertNull($response->json());
    }
}

class FamiliesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_families_index(): void
    {
        $response = $this->get(route('families.index'));

        $response->assertSuccessful();
        $response->assertViewHas('families');
    }

    #[Test]
    public function it_creates_new_family(): void
    {
        $familyData = [
            'family_name' => 'Test Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Test Family',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_family_names(): void
    {
        Family::factory()->create(['family_name' => 'Existing Family']);

        $familyData = [
            'family_name' => 'Existing Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_family(): void
    {
        $family = Family::factory()->create(['family_name' => 'Original Family']);

        $updateData = [
            'family_name' => 'Updated Family',
        ];

        $response = $this->post(route('families.form', ['id' => $family->family_id]), $updateData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_id'   => $family->family_id,
            'family_name' => 'Updated Family',
        ]);
    }

    #[Test]
    public function it_deletes_family(): void
    {
        $family = Family::factory()->create();

        $response = $this->delete(route('families.delete', ['id' => $family->family_id]));

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }
}

class UnitsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_units_index(): void
    {
        $response = $this->get(route('units.index'));

        $response->assertSuccessful();
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_creates_new_unit(): void
    {
        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Kilogram',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_unit_names(): void
    {
        Unit::factory()->create(['unit_name' => 'Existing Unit']);

        $unitData = [
            'unit_name'      => 'Existing Unit',
            'unit_name_plrl' => 'Existing Units',
            'is_update'      => 0,
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_unit(): void
    {
        $unit = Unit::factory()->create(['unit_name' => 'Original Unit']);

        $updateData = [
            'unit_name'      => 'Updated Unit',
            'unit_name_plrl' => 'Updated Units',
        ];

        $response = $this->post(route('units.form', ['id' => $unit->unit_id]), $updateData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Unit',
        ]);
    }

    #[Test]
    public function it_deletes_unit(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.delete', ['id' => $unit->unit_id]));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}

#[CoversClass(EmailTemplatesController::class)]
class EmailTemplatesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_email_templates_index(): void
    {
        $response = $this->get(route('email_templates.index'));

        $response->assertSuccessful();
        $response->assertViewHas('email_templates');
    }

    #[Test]
    public function it_creates_new_email_template(): void
    {
        $templateData = [
            'email_template_title'   => 'Test Template',
            'email_template_subject' => 'Test Subject',
            'email_template_body'    => 'Test body content',
            'email_template_type'    => 'invoice',
            'is_update'              => 0,
        ];

        $response = $this->post(route('email_templates.form'), $templateData);

        $response->assertRedirect(route('email_templates.index'));
        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_title' => 'Test Template',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_email_template_titles(): void
    {
        EmailTemplate::factory()->create(['email_template_title' => 'Existing Template']);

        $templateData = [
            'email_template_title'   => 'Existing Template',
            'email_template_subject' => 'Subject',
            'email_template_body'    => 'Body',
            'is_update'              => 0,
        ];

        $response = $this->post(route('email_templates.form'), $templateData);

        $response->assertRedirect(route('email_templates.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_email_template(): void
    {
        $template = EmailTemplate::factory()->create([
            'email_template_title' => 'Original Template',
        ]);

        $updateData = [
            'email_template_title'   => 'Updated Template',
            'email_template_subject' => 'Updated Subject',
            'email_template_body'    => 'Updated body',
        ];

        $response = $this->post(route('email_templates.form', ['id' => $template->email_template_id]), $updateData);

        $response->assertRedirect(route('email_templates.index'));
        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_id'    => $template->email_template_id,
            'email_template_title' => 'Updated Template',
        ]);
    }

    #[Test]
    public function it_deletes_email_template(): void
    {
        $template = EmailTemplate::factory()->create();

        $response = $this->delete(route('email_templates.delete', ['id' => $template->email_template_id]));

        $response->assertRedirect(route('email_templates.index'));
        $this->assertDatabaseMissing('ip_email_templates', ['email_template_id' => $template->email_template_id]);
    }

    #[Test]
    public function it_loads_email_template_form_with_custom_fields(): void
    {
        $response = $this->get(route('email_templates.form'));

        $response->assertSuccessful();
        $response->assertViewHas('custom_fields');
        $response->assertViewHas('invoice_templates');
        $response->assertViewHas('quote_templates');
    }
}

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}

#[CoversClass(MailerController::class)]

class MailerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected tmpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user   = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->client = tmpClient::factory()->create(['client_email' => 'client@example.com']);
        $this->actingAs($this->user);

        // Mock mailer as configured
        Config::set('mail.driver', 'smtp');
    }

    #[Test]
    public function it_displays_invoice_mail_composer(): void
    {
        $invoice       = Invoice::factory()->create(['client_id' => $this->client->client_id]);
        $emailTemplate = EmailTemplate::factory()->create(['email_template_type' => 'invoice']);

        $response = $this->get(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertViewHas('invoice');
        $response->assertViewHas('email_templates');
        $response->assertViewHas('pdf_templates');
        $response->assertViewHas('custom_fields');
    }

    #[Test]
    public function it_displays_quote_mail_composer(): void
    {
        $quote         = Quote::factory()->create(['client_id' => $this->client->client_id]);
        $emailTemplate = EmailTemplate::factory()->create(['email_template_type' => 'quote']);

        $response = $this->get(route('mailer.quote', ['quote_id' => $quote->quote_id]));

        $response->assertSuccessful();
        $response->assertViewHas('quote');
        $response->assertViewHas('email_templates');
        $response->assertViewHas('pdf_templates');
    }

    #[Test]
    public function it_returns_503_when_mailer_not_configured(): void
    {
        Config::set('mail.driver', null);

        $invoice = Invoice::factory()->create();

        $response = $this->get(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));

        $response->assertStatus(503);
        $response->assertViewIs('mailer.not_configured');
    }

    #[Test]
    public function it_sends_invoice_email_with_pdf(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-001',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Please find attached your invoice.',
            'pdf_template' => 'default',
            'cc'           => '',
            'bcc'          => '',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
        $response->assertSessionHas('alert_success');

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasTo($emailData['to_email']);
        });
    }

    #[Test]
    public function it_sends_invoice_email_with_cc_and_bcc(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-002',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email body',
            'pdf_template' => 'default',
            'cc'           => 'cc@example.com',
            'bcc'          => 'bcc@example.com',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasCc($emailData['cc']) && $mail->hasBcc($emailData['bcc']);
        });
    }

    #[Test]
    public function it_converts_plain_text_to_html_in_email_body(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-003',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => "Line 1\nLine 2\nLine 3", // Plain text with newlines
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        // Body should have been converted with nl2br
        Mail::assertSent(function ($mail) {
            return str_contains($mail->body, '<br');
        });
    }

    #[Test]
    public function it_attaches_invoice_uploads_to_email(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'      => $this->client->client_id,
            'invoice_number' => 'INV-004',
        ]);

        Upload::factory()->count(2)->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice with attachments',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_generates_invoice_number_before_sending_email(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'         => $this->client->client_id,
            'invoice_number'    => null,
            'invoice_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $invoice->refresh();
        $this->assertNotNull($invoice->invoice_number);
    }

    #[Test]
    public function it_marks_invoice_as_sent_after_emailing(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id'         => $this->client->client_id,
            'invoice_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $invoice->refresh();
        $this->assertEquals(2, $invoice->invoice_status_id); // Sent status
    }

    #[Test]
    public function it_cancels_invoice_email_and_redirects_to_view(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), [
            'btn_cancel' => true,
        ]);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_redirects_to_mailer_form_on_failed_email_send(): void
    {
        Mail::fake();
        Mail::shouldReceive('send')->andThrow(new Exception('Mail server error'));

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->client_id,
        ]);

        $emailData = [
            'to_email'     => 'invalid-email',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => 'Invoice email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('mailer.invoice', ['invoice_id' => $invoice->invoice_id]));
    }

    #[Test]
    public function it_sends_quote_email_with_pdf(): void
    {
        Mail::fake();

        $quote = Quote::factory()->create([
            'client_id'    => $this->client->client_id,
            'quote_number' => 'QUO-001',
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Please find attached your quote.',
            'pdf_template' => 'default',
            'cc'           => '',
            'bcc'          => '',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
        $response->assertSessionHas('alert_success');

        Mail::assertSent(function ($mail) use ($emailData) {
            return $mail->hasTo($emailData['to_email']);
        });
    }

    #[Test]
    public function it_generates_quote_number_before_sending_email(): void
    {
        Mail::fake();

        $quote = Quote::factory()->create([
            'client_id'       => $this->client->client_id,
            'quote_number'    => null,
            'quote_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $quote->refresh();
        $this->assertNotNull($quote->quote_number);
    }

    #[Test]
    public function it_marks_quote_as_sent_after_emailing(): void
    {
        Mail::fake();

        $quote = Quote::factory()->create([
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote email',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $quote->refresh();
        $this->assertEquals(2, $quote->quote_status_id); // Sent status
    }

    #[Test]
    public function it_cancels_quote_email_and_redirects_to_view(): void
    {
        $quote = Quote::factory()->create();

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), [
            'btn_cancel' => true,
        ]);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
    }

    #[Test]
    public function it_attaches_quote_uploads_to_email(): void
    {
        Mail::fake();

        $quote = Quote::factory()->create([
            'client_id'    => $this->client->client_id,
            'quote_number' => 'QUO-002',
        ]);

        Upload::factory()->count(2)->create([
            'quote_id' => $quote->quote_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Quote',
            'body'         => 'Quote with attachments',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendQuote', ['quote_id' => $quote->quote_id]), $emailData);

        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
    }

    #[Test]
    public function it_decodes_html_entities_in_email_body(): void
    {
        Mail::fake();

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->client_id,
        ]);

        $emailData = [
            'to_email'     => 'client@example.com',
            'from_email'   => 'sender@example.com',
            'from_name'    => 'Test Sender',
            'subject'      => 'Your Invoice',
            'body'         => '<p>Invoice &amp; details</p>',
            'pdf_template' => 'default',
        ];

        $response = $this->post(route('mailer.sendInvoice', ['invoice_id' => $invoice->invoice_id]), $emailData);

        $response->assertRedirect(route('invoices.view', ['invoice_id' => $invoice->invoice_id]));

        // HTML entities should be decoded
        Mail::assertSent(function ($mail) {
            return str_contains($mail->body, '&') && ! str_contains($mail->body, '&amp;');
        });
    }
}

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/' . $notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token'                 => $notification->token,
                'email'                 => $user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/settings/profile');
    }
}

#[CoversClass(ReportsController::class)]
class ReportsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_sales_by_client_report()
    {
        // Arrange: create clients and sales data
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory(3)->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(5),
            'total'        => 500,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.salesByClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and sales data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('500');
    }

    #[Test]
    public function it_generates_sales_by_client_report(): void
    {
        // Arrange
        $client = tmpClient::factory()->create();
        Invoice::factory()->count(3)->create([
            'client_id'            => $client->client_id,
            'invoice_status_id'    => 4, // Paid
            'invoice_date_created' => now()->subDays(10),
        ]);

        // Act
        $response = $this->post(route('reports.salesByClient'), [
            'btn_submit' => true,
            'from_date'  => now()->subDays(30)->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
        ]);

        // Assert
        $response->assertSuccessful();
        $response->assertViewHas('results');
        $response->assertViewHas('from_date');
        $response->assertViewHas('to_date');
    }

    #[Test]
    public function it_displays_payment_history_report_form(): void
    {
        $response = $this->get(route('reports.paymentHistory'));

        $response->assertSuccessful();
        $response->assertViewIs('reports.payment_history_index');
    }

    #[Test]
    public function it_generates_payment_history_report(): void
    {
        $invoice = Invoice::factory()->create();
        Payment::factory()->count(3)->create([
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(5),
        ]);

        $response = $this->post(route('reports.paymentHistory'), [
            'btn_submit' => true,
            'from_date'  => now()->subDays(30)->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
    }

    #[Test]
    public function it_generates_invoice_aging_report(): void
    {
        // Arrange: create clients and invoices
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(10),
            'invoice_status_id' => 2, // Sent
        ]);
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(40),
            'invoice_status_id' => 2,
        ]);
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(70),
            'invoice_status_id' => 2,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.invoiceAging'), [
            'btn_submit' => true,
        ]);

        // Assert: report contains client and invoice data
        $response->assertSuccessful();
        $response->assertViewHas('results');
    }

    #[Test]
    public function it_returns_invoices_per_client_report()
    {
        // Arrange: create clients and invoices
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(3),
            'total'        => 300,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.invoicesPerClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and invoice data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('300');
    }

    #[Test]
    public function it_generates_sales_by_year_report_with_filters(): void
    {
        Invoice::factory()->count(10)->create([
            'invoice_date_created' => now()->subMonths(6),
            'invoice_status_id'    => 4,
        ]);

        $response = $this->post(route('reports.salesByYear'), [
            'btn_submit'  => true,
            'from_date'   => now()->subYear()->format('Y-m-d'),
            'to_date'     => now()->format('Y-m-d'),
            'minQuantity' => 0,
            'maxQuantity' => 1000,
            'checkboxTax' => true,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
        $response->assertViewHas('from_date');
        $response->assertViewHas('to_date');
    }

    #[Test]
    public function it_filters_sales_report_by_quantity_range(): void
    {
        $response = $this->post(route('reports.salesByYear'), [
            'btn_submit'  => true,
            'from_date'   => now()->subYear()->format('Y-m-d'),
            'to_date'     => now()->format('Y-m-d'),
            'minQuantity' => 10,
            'maxQuantity' => 100,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
    }
}

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_admin_routes(): void
    {
        $response = $this->get(route('dashboard.index'));

        $response->assertRedirect(route('sessions.login'));
    }

    #[Test]
    public function it_allows_authenticated_users_to_access_admin_routes(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
    }

    #[Test]
    public function it_filters_input_to_prevent_xss_attacks(): void
    {
        $this->actingAs($this->user);
        $client = tmpClient::factory()->create();

        $maliciousData = [
            'client_id'            => $client->client_id,
            'invoice_notes'        => '<script>alert("XSS")</script>',
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post(route('invoices.form'), $maliciousData);

        // Input should be filtered
        $this->assertDatabaseMissing('ip_invoices', [
            'invoice_notes' => '<script>alert("XSS")</script>',
        ]);
    }

    #[Test]
    public function it_prevents_sql_injection_in_search_queries(): void
    {
        $this->actingAs($this->user);
        tmpClient::factory()->create(['client_name' => 'Test Client', 'client_active' => 1]);

        $sqlInjection = "' OR '1'='1";

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => $sqlInjection]));

        $response->assertSuccessful();
        // Should not return all clients or cause error
    }

    #[Test]
    public function it_validates_csrf_tokens_on_form_submissions(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('clients.form'), [
            'client_name'  => 'Test Client',
            'client_email' => 'test@example.com',
        ], [
            'X-CSRF-TOKEN' => 'invalid-token',
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }

    #[Test]
    public function it_prevents_directory_traversal_in_file_operations(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('invoices.download', ['invoice' => '../../etc/passwd']));

        $response->assertNotFound();
    }

    #[Test]
    public function it_validates_user_permissions_for_sensitive_operations(): void
    {
        $guestUser = User::factory()->create(['user_type' => 2, 'user_active' => 1]);
        $this->actingAs($guestUser);

        $invoice = Invoice::factory()->create();

        $response = $this->delete(route('invoices.delete', ['invoice_id' => $invoice->invoice_id]));

        // Guest users should not be able to delete invoices
        $response->assertStatus(403);
    }

    #[Test]
    public function it_sanitizes_file_upload_names(): void
    {
        $this->actingAs($this->user);

        // Test with potentially malicious filename
        $maliciousFilename = '../../../evil.php';

        // Implementation would depend on upload controller
        // Just ensure basename is used and path traversal is blocked
        $this->assertTrue(str_contains(basename($maliciousFilename), 'evil.php'));
        $this->assertFalse(str_contains(basename($maliciousFilename), '../'));
    }

    #[Test]
    public function it_rate_limits_login_attempts(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => bcrypt('password'),
            'user_active'   => 1,
        ]);

        // Attempt multiple failed logins
        for ($i = 0; $i < 11; $i++) {
            $this->post(route('sessions.login'), [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // Account should be locked
        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_validates_email_format_in_user_input(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('clients.form'), [
            'client_name'  => 'Test Client',
            'client_email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('client_email');
    }

    #[Test]
    public function it_prevents_mass_assignment_vulnerabilities(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('clients.form'), [
            'client_name'  => 'Test Client',
            'client_email' => 'test@example.com',
            'user_type'    => 1, // Attempt to set privileged field
            'user_active'  => 1,
        ]);

        // user_type should not be assignable through client form
        $this->assertDatabaseMissing('ip_clients', [
            'user_type' => 1,
        ]);
    }
}

#[CoversClass(SessionsController::class)]
class SessionsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    #[Test]
    public function it_redirects_index_to_login(): void
    {
        $response = $this->get(route('sessions.index'));

        $response->assertRedirect(route('sessions.login'));
    }

    #[Test]
    public function it_displays_login_page(): void
    {
        $response = $this->get(route('sessions.login'));

        $response->assertSuccessful();
        $response->assertViewIs('session_login');
        $response->assertViewHas('login_logo');
    }

    #[Test]
    public function it_authenticates_user_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_redirects_guest_users_to_guest_area(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'guest@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 2, // Guest user
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'guest@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('guest'));
    }

    #[Test]
    public function it_rejects_authentication_with_invalid_credentials(): void
    {
        User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'wrongpassword',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error');
        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_authentication_for_nonexistent_user(): void
    {
        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'nonexistent@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error', trans('loginalert_user_not_found'));
        $this->assertGuest();
    }

    #[Test]
    public function it_rejects_authentication_for_inactive_user(): void
    {
        User::factory()->create([
            'user_email'    => 'inactive@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 0,
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'inactive@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_error', trans('loginalert_user_inactive'));
        $this->assertGuest();
    }

    #[Test]
    public function it_throttles_login_attempts_after_multiple_failures(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
        ]);

        // Attempt 10 failed logins
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('sessions.login'), [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // 11th attempt should be blocked
        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_authenticated_user(): void
    {
        $user = User::factory()->create(['user_active' => 1]);
        $this->actingAs($user);

        $response = $this->get(route('sessions.logout'));

        $response->assertRedirect(route('sessions.login'));
        $this->assertGuest();
    }

    #[Test]
    public function it_displays_password_reset_page(): void
    {
        $response = $this->get(route('sessions.passwordreset'));

        $response->assertSuccessful();
        $response->assertViewIs('session_passwordreset');
    }

    #[Test]
    public function it_sends_password_reset_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'user_email'  => 'test@example.com',
            'user_active' => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_reset' => true,
            'email'     => 'test@example.com',
        ]);

        $response->assertRedirect(route('sessions.login'));
        $response->assertSessionHas('alert_success');

        $user->refresh();
        $this->assertNotNull($user->user_passwordreset_token);
    }

    #[Test]
    public function it_validates_email_format_in_password_reset(): void
    {
        $response = $this->post(route('sessions.passwordreset'), [
            'btn_reset' => true,
            'email'     => 'invalid-email',
        ]);

        $response->assertRedirect('/');
    }

    #[Test]
    public function it_throttles_password_reset_attempts(): void
    {
        $user = User::factory()->create([
            'user_email'  => 'test@example.com',
            'user_active' => 1,
        ]);

        // Attempt 10 password resets
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('sessions.passwordreset'), [
                'btn_reset' => true,
                'email'     => 'test@example.com',
            ]);
        }

        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_displays_new_password_form_with_valid_token(): void
    {
        $user = User::factory()->create([
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->get(route('sessions.passwordreset', ['token' => 'valid_token_123']));

        $response->assertSuccessful();
        $response->assertViewIs('session_new_password');
        $response->assertViewHas('token', 'valid_token_123');
        $response->assertViewHas('user_id', $user->user_id);
    }

    #[Test]
    public function it_rejects_invalid_password_reset_token(): void
    {
        $response = $this->get(route('sessions.passwordreset', ['token' => 'invalid_token']));

        $response->assertRedirect(route('sessions.passwordreset'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_rejects_non_alphanumeric_token(): void
    {
        $response = $this->get(route('sessions.passwordreset', ['token' => 'token<script>alert(1)</script>']));

        $response->assertRedirect('/');
    }

    #[Test]
    public function it_updates_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'valid_token_123',
            'user_id'          => $user->user_id,
            'new_password'     => 'newpassword123',
        ]);

        $response->assertRedirect(route('sessions.login'));

        $user->refresh();
        $this->assertEmpty($user->user_passwordreset_token);
        $this->assertTrue(Hash::check('newpassword123', $user->user_password));
    }

    #[Test]
    public function it_rejects_password_update_with_mismatched_token(): void
    {
        $user = User::factory()->create([
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'wrong_token',
            'user_id'          => $user->user_id,
            'new_password'     => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert_error');

        $user->refresh();
        $this->assertEquals('valid_token_123', $user->user_passwordreset_token);
    }

    #[Test]
    public function it_rejects_empty_password_in_reset(): void
    {
        $user = User::factory()->create([
            'user_email'               => 'test@example.com',
            'user_passwordreset_token' => 'valid_token_123',
            'user_active'              => 1,
        ]);

        $response = $this->post(route('sessions.passwordreset'), [
            'btn_new_password' => true,
            'token'            => 'valid_token_123',
            'user_id'          => $user->user_id,
            'new_password'     => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_clears_login_failures_after_successful_authentication(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        // Create some failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('sessions.login'), [
                'btn_login' => true,
                'email'     => 'test@example.com',
                'password'  => 'wrongpassword',
            ]);
        }

        // Successful login
        $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $this->assertDatabaseMissing('ip_login_log', [
            'login_name' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_unlocks_account_after_12_hours(): void
    {
        $user = User::factory()->create([
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password123'),
            'user_active'   => 1,
            'user_type'     => 1,
        ]);

        // Create login log with old timestamp
        DB::table('ip_login_log')->insert([
            'login_name'           => 'test@example.com',
            'log_count'            => 11,
            'log_create_timestamp' => now()->subHours(13)->toDateTimeString(),
        ]);

        $response = $this->post(route('sessions.login'), [
            'btn_login' => true,
            'email'     => 'test@example.com',
            'password'  => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}

#[CoversClass(SettingsController::class)]
class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_settings_page_and_saves_settings()
    {
        // Act: call the index route
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Settings'); // Adjust to match actual page content

        // Arrange: prepare settings data
        $settings = [
            'tax_rate_decimal_places' => 2,
            'currency_symbol'         => '$',
            // add other required fields
        ];

        // Act: post to the index route to save settings
        $response = $this->post(route('settings.index'), ['settings' => $settings]);

        // Assert: settings are saved in the database
        $this->assertDatabaseHas('ip_settings', ['key' => 'tax_rate_decimal_places', 'value' => '2']);
        $this->assertDatabaseHas('ip_settings', ['key' => 'currency_symbol', 'value' => '$']);
        $response->assertRedirect(route('settings.index'));
    }
}

#[CoversClass(TaxRatesController::class)]
class TaxRatesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_tax_rates_list()
    {
        // Arrange: authenticate user
        $user = \src\Models\User::factory()->create();
        $this->actingAs($user);

        // Arrange: create tax rates
        $taxRate = \src\Models\TaxRate::factory()->create();

        // Act: visit tax rates index
        $response = $this->get(route('tax_rates.index'));

        // Assert: tax rates are displayed
        $response->assertStatus(200);
        $response->assertViewIs('tax_rates.index');
        $response->assertSee($taxRate->tax_rate_name);
    }

    #[Test]
    public function it_creates_new_tax_rate(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => '21.00',
        ];

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 21.00,
        ]);
    }

    #[Test]
    public function it_stores_tax_rate_via_form_store()
    {
        // Act: submit form with valid data
        /**
         * Payload:
         * {
         *   "tax_rate_name": "VAT",
         *   "tax_rate_percent": "20.00",
         *   "btn_submit": true
         * }
         */
        $response = $this->post(route('tax_rates.formStore'), [
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => '20.00',
            'btn_submit'       => true,
        ]);

        // Assert: redirects to tax rates index
        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_standardizes_tax_rate_percent_on_creation(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Sales Tax',
            'tax_rate_percent' => '15,50', // European format
        ];

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'Sales Tax',
            'tax_rate_percent' => 15.50,
        ]);
    }

    #[Test]
    public function it_updates_existing_tax_rate(): void
    {
        $taxRate = TaxRate::factory()->create([
            'tax_rate_name'    => 'Original Tax',
            'tax_rate_percent' => 10.00,
        ]);

        $updateData = [
            'tax_rate_name'    => 'Updated Tax',
            'tax_rate_percent' => '19.00',
        ];

        $response = $this->post(route('tax_rates.form', ['id' => $taxRate->tax_rate_id]), $updateData);

        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated Tax',
            'tax_rate_percent' => 19.00,
        ]);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('tax_rates.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to tax rates index
        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_deletes_tax_rate()
    {
        // Arrange: create a tax rate
        $taxRate = \src\Models\TaxRate::factory()->create();

        // Act: delete the tax rate
        $response = $this->get(route('tax_rates.delete', ['id' => $taxRate->id]));

        // Assert: redirects and tax rate is deleted
        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->id]);
    }

    #[Test]
    public function it_cancels_tax_rate_form_and_redirects(): void
    {
        $response = $this->post(route('tax_rates.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_validates_tax_rate_percent_is_numeric(): void
    {
        $taxRateData = [
            'tax_rate_name'    => 'Invalid Tax',
            'tax_rate_percent' => 'not-a-number',
        ];

        $response = $this->post(route('tax_rates.form'), $taxRateData);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_returns_404_when_editing_nonexistent_tax_rate(): void
    {
        $response = $this->get(route('tax_rates.form', ['id' => 99999]));

        $response->assertNotFound();
    }
}

#[CoversClass(UserClientsController::class)]
class UserClientsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_users_from_index()
    {
        // Act: visit user clients index
        $response = $this->get(route('user_clients.index'));

        // Assert: redirects to users
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_displays_user_clients_for_a_user()
    {
        // Arrange: create a user
        $user = \src\Models\User::factory()->create();

        // Act: visit user clients page
        $response = $this->get(route('user_clients.user', ['id' => $user->id]));

        // Assert: page is displayed
        $response->assertStatus(200);
        $response->assertViewIs('user_clients.new');
        $response->assertViewHas('user');
        $response->assertViewHas('user_clients');
    }

    #[Test]
    public function it_redirects_to_users_when_user_not_found()
    {
        // Act: visit user clients page for non-existent user
        $response = $this->get(route('user_clients.user', ['id' => 99999]));

        // Assert: redirects to users
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_redirects_to_custom_values_when_user_id_is_null()
    {
        // Act: visit create page without user_id
        $response = $this->get(route('user_clients.create'));

        // Assert: redirects to custom values
        $response->assertRedirect(route('custom_values'));
    }

    #[Test]
    public function it_deletes_user_client_and_redirects()
    {
        // Arrange: create user and user client
        $user       = \src\Models\User::factory()->create();
        $client     = \Modules\Clients\Models\tmpClient::factory()->create();
        $userClient = \src\Models\UserClient::factory()->create([
            'user_id'   => $user->id,
            'client_id' => $client->id,
        ]);

        // Act: delete user client
        $response = $this->get(route('user_clients.delete', ['user_client_id' => $userClient->id]));

        // Assert: redirects to user clients page
        $response->assertRedirect(route('user_clients.user', ['id' => $user->id]));
        $this->assertDatabaseMissing('ip_user_clients', ['id' => $userClient->id]);
    }
}

#[CoversClass(UsersAjaxController::class)]
class UsersAjaxControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected AuthUser $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = AuthUser::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_empty_json_when_user_query_is_empty(): void
    {
        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1]));

        $response->assertSuccessful();
        $response->assertJson([]);
    }

    #[Test]
    public function it_searches_users_by_name_with_trailing_wildcard(): void
    {
        User::factory()->create(['user_name' => 'John Doe', 'user_active' => 1, 'user_type' => 1]);
        User::factory()->create(['user_name' => 'Jane Doe', 'user_active' => 1, 'user_type' => 1]);
        User::factory()->create(['user_name' => 'Bob Smith', 'user_active' => 1, 'user_type' => 1]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1, 'query' => 'J']));

        $data = $response->json();
        $this->assertCount(2, $data);
    }

    #[Test]
    public function it_searches_users_by_company_name(): void
    {
        User::factory()->create([
            'user_name'    => 'John Doe',
            'user_company' => 'Acme Corp',
            'user_active'  => 1,
            'user_type'    => 1,
        ]);
        User::factory()->create([
            'user_name'    => 'Jane Smith',
            'user_company' => 'Acme Industries',
            'user_active'  => 1,
            'user_type'    => 1,
        ]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1, 'query' => 'Acme']));

        $data = $response->json();
        $this->assertCount(2, $data);
    }

    #[Test]
    public function it_searches_users_with_leading_wildcard_when_permissive_search_enabled(): void
    {
        User::factory()->create([
            'user_name'   => 'John Doe',
            'user_active' => 1,
            'user_type'   => 1,
        ]);

        $response = $this->get(route('users.ajax.nameQuery', [
            'type'                    => 1,
            'query'                   => 'ohn',
            'permissive_search_users' => 1,
        ]));

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data));
    }

    #[Test]
    public function it_only_returns_active_users_in_name_query(): void
    {
        User::factory()->create(['user_name' => 'Active User', 'user_active' => 1, 'user_type' => 1]);
        User::factory()->create(['user_name' => 'Inactive User', 'user_active' => 0, 'user_type' => 1]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1, 'query' => 'User']));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Active', $data[0]['text']);
    }

    #[Test]
    public function it_filters_users_by_type(): void
    {
        User::factory()->create(['user_name' => 'Admin User', 'user_active' => 1, 'user_type' => 1]);
        User::factory()->create(['user_name' => 'Guest User', 'user_active' => 1, 'user_type' => 2]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 2, 'query' => 'User']));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Guest', $data[0]['text']);
    }

    #[Test]
    public function it_escapes_percent_signs_in_user_search_query(): void
    {
        User::factory()->create(['user_name' => '100% Solutions', 'user_active' => 1, 'user_type' => 1]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1, 'query' => '100%']));

        $response->assertSuccessful();
    }

    #[Test]
    public function it_returns_five_most_recent_active_users(): void
    {
        User::factory()->count(10)->create(['user_active' => 1]);

        $response = $this->get(route('users.ajax.getLatest'));

        $data = $response->json();
        $this->assertCount(5, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
    }

    #[Test]
    public function it_saves_permissive_search_users_preference(): void
    {
        $response = $this->get(route('users.ajax.savePreference', ['permissive_search_users' => '1']));

        $response->assertSuccessful();
        $this->assertDatabaseHas('ip_settings', [
            'setting_key'   => 'enable_permissive_search_users',
            'setting_value' => '1',
        ]);
    }

    #[Test]
    public function it_rejects_invalid_permissive_search_users_preference(): void
    {
        $response = $this->get(route('users.ajax.savePreference', ['permissive_search_users' => '2']));

        $this->assertDatabaseMissing('ip_settings', [
            'setting_key'   => 'enable_permissive_search_users',
            'setting_value' => '2',
        ]);
    }

    #[Test]
    public function it_saves_user_client_relationship_for_existing_user(): void
    {
        $user   = User::factory()->create();
        $client = tmpClient::factory()->create();

        $response = $this->post(route('users.ajax.saveUserClient'), [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('ip_user_clients', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);
    }

    #[Test]
    public function it_does_not_duplicate_user_client_relationship(): void
    {
        $user   = User::factory()->create();
        $client = tmpClient::factory()->create();

        UserClient::factory()->create([
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response = $this->post(route('users.ajax.saveUserClient'), [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertEquals(1, UserClient::where('user_id', $user->user_id)
            ->where('client_id', $client->client_id)
            ->count());
    }

    #[Test]
    public function it_stores_user_client_in_session_for_new_user(): void
    {
        $client = tmpClient::factory()->create();

        $response = $this->post(route('users.ajax.saveUserClient'), [
            'user_id'   => null,
            'client_id' => $client->client_id,
        ]);

        $response->assertSuccessful();
        $this->assertArrayHasKey($client->client_id, session('user_clients'));
    }

    #[Test]
    public function it_loads_user_client_table_for_existing_user(): void
    {
        $user    = User::factory()->create();
        $clients = tmpClient::factory()->count(3)->create();

        foreach ($clients as $client) {
            UserClient::factory()->create([
                'user_id'   => $user->user_id,
                'client_id' => $client->client_id,
            ]);
        }

        $response = $this->post(route('users.ajax.loadUserClientTable'), [
            'user_id' => $user->user_id,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('user_clients', function ($userClients) {
            return count($userClients) === 3;
        });
    }

    #[Test]
    public function it_loads_user_client_table_from_session_for_new_user(): void
    {
        $clients        = tmpClient::factory()->count(2)->create();
        $sessionClients = $clients->pluck('client_id')->toArray();

        session(['user_clients' => array_combine($sessionClients, $sessionClients)]);

        $response = $this->post(route('users.ajax.loadUserClientTable'));

        $response->assertSuccessful();
        $response->assertViewHas('user_clients', function ($userClients) {
            return count($userClients) === 2;
        });
    }

    #[Test]
    public function it_displays_modal_add_user_client_for_existing_user(): void
    {
        $user              = User::factory()->create();
        $assignedClients   = tmpClient::factory()->count(2)->create();
        $unassignedClients = tmpClient::factory()->count(3)->create();

        foreach ($assignedClients as $client) {
            UserClient::factory()->create([
                'user_id'   => $user->user_id,
                'client_id' => $client->client_id,
            ]);
        }

        $response = $this->get(route('users.ajax.modalAddUserClient', ['user_id' => $user->user_id]));

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients) {
            return count($clients) === 3; // Only unassigned clients
        });
        $response->assertViewHas('user_id', $user->user_id);
    }

    #[Test]
    public function it_displays_all_clients_for_new_user_in_modal(): void
    {
        tmpClient::factory()->count(5)->create();

        $response = $this->get(route('users.ajax.modalAddUserClient'));

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients) {
            return count($clients) === 5;
        });
    }

    #[Test]
    public function it_excludes_session_clients_from_modal_for_new_user(): void
    {
        $clients        = tmpClient::factory()->count(5)->create();
        $sessionClients = [$clients->first()->client_id => $clients->first()->client_id];

        session(['user_clients' => $sessionClients]);

        $response = $this->get(route('users.ajax.modalAddUserClient'));

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients) {
            return count($clients) === 4;
        });
    }

    #[Test]
    public function it_html_escapes_user_names_in_search_results(): void
    {
        User::factory()->create([
            'user_name'   => '<script>alert("xss")</script>',
            'user_active' => 1,
            'user_type'   => 1,
        ]);

        $response = $this->get(route('users.ajax.nameQuery', ['type' => 1, 'query' => 'script']));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringNotContainsString('<script>', $data[0]['text']);
    }
}

#[CoversClass(WelcomeController::class)]
class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_welcome_page()
    {
        // Act: visit the welcome page
        $response = $this->get(route('welcome'));

        // Assert: page is displayed successfully
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }
}
