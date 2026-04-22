<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Tests\Feature\Core\EmailTemplatesAjaxController::class)]
class EmailTemplatesAjaxControllerTest extends AbstractTestCase
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
    public function it_returns_email_template_content_as_json(): void
    {
        $template = $this->seedModel('EmailTemplate', [
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
