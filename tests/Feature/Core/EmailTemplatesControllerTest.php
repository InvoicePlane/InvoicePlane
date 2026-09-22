<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Email_Templates controller — application/modules/email_templates/controllers/Email_templates.php.
 *
 * Required fields (Mdl_Email_Templates::validation_rules): email_template_title.
 * Absorbs Issue1694EmailTemplatesDeleteCsrfTest. AJAX (template body/subject
 * lookups) lives in EmailTemplatesAjaxControllerTest.
 */
#[Group('email_templates')]
#[CoversClass(\Email_Templates::class)]
class EmailTemplatesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_email_template(): void
    {
        /* Arrange */
        $this->seedTemplate(['email_template_title' => 'Invoice Reminder']);
        $this->seedTemplate(['email_template_title' => 'Quote Follow Up']);

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Invoice Reminder');
        $this->assertResponseBodyContains($response, 'Quote Follow Up');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_an_email_template(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/email_templates/form', [
            'email_template_title' => 'Payment Received',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Thank you for your payment.',
            'btn_submit'           => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'email_templates');
        $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Payment Received']);
        $this->assertDatabaseCount('ip_email_templates', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_email_template_title(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/email_templates/form', [
            'email_template_title' => '',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Body with no title.',
            'btn_submit'           => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_email_templates', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_email_template_only(): void
    {
        /* Arrange */
        $target = $this->seedTemplate(['email_template_title' => 'Editable Template']);
        $this->seedTemplate(['email_template_title' => 'Other Template']);

        /* Act */
        $response = $this->get('/email_templates/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Template');
        $this->assertResponseBodyNotContains($response, 'Other Template');
    }

    #[Test]
    public function it_updates_an_email_template(): void
    {
        /* Arrange */
        $id = $this->seedTemplate(['email_template_title' => 'Original Template']);

        /* Act */
        $response = $this->post('/email_templates/form/' . $id, [
            'email_template_title' => 'Renamed Template',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Updated body.',
            'btn_submit'           => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'email_templates');
        $this->assertDatabaseHas('ip_email_templates', ['email_template_id' => $id, 'email_template_title' => 'Renamed Template']);
        $this->assertDatabaseMissing('ip_email_templates', ['email_template_title' => 'Original Template']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_email_template_title(): void
    {
        /* Arrange */
        $id = $this->seedTemplate(['email_template_title' => 'Keep This Template']);

        /* Act */
        $response = $this->post('/email_templates/form/' . $id, [
            'email_template_title' => '',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Body.',
            'btn_submit'           => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_email_templates', ['email_template_id' => $id, 'email_template_title' => 'Keep This Template']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_email_template(): void
    {
        /* Arrange */
        $id   = $this->seedTemplate(['email_template_title' => 'Deletable Template']);
        $keep = $this->seedTemplate(['email_template_title' => 'Kept Template']);

        /* Act */
        $response = $this->post('/email_templates/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'email_templates');
        $this->assertDatabaseMissing('ip_email_templates', ['email_template_id' => $id]);
        $this->assertDatabaseHas('ip_email_templates', ['email_template_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_an_email_template_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedTemplate(['email_template_title' => 'CSRF Template']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/email_templates/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'email_templates');
        $this->assertDatabaseMissing('ip_email_templates', ['email_template_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_an_email_template_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedTemplate(['email_template_title' => 'CSRF Template Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/email_templates/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_email_templates', ['email_template_id' => $id, 'email_template_title' => 'CSRF Template Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_email_template(): void
    {
        /* Arrange */
        $this->seedTemplate(['email_template_title' => 'Secret Template']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Template');
    }

    /** @param array<string,mixed> $overrides */
    private function seedTemplate(array $overrides = []): int
    {
        return $this->databaseInsert('ip_email_templates', array_merge([
            'email_template_title' => 'Seeded Template',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Hello {{{client_name}}}',
        ], $overrides));
    }
}
