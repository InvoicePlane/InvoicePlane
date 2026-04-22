<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

class EmailTemplatesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
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
        $this->seedModel('EmailTemplate', ['email_template_title' => 'Existing Template']);

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
        $template = $this->seedModel('EmailTemplate', [
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
        $template = $this->seedModel('EmailTemplate');

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
