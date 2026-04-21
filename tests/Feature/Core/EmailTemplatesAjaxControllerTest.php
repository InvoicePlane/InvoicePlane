<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

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
