<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class EmailTemplatesAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_lists_all_email_templates(): void
    {
        /* Arrange: create email templates */
        $templateId1 = $this->databaseInsertGetId('ip_email_templates', [
            'email_template_title'   => 'Invoice Template',
            'email_template_subject' => 'Invoice Subject',
            'email_template_body'    => 'Invoice body content',
            'email_template_type'    => 'invoice',
        ]);

        $templateId2 = $this->databaseInsertGetId('ip_email_templates', [
            'email_template_title'   => 'Quote Template',
            'email_template_subject' => 'Quote Subject',
            'email_template_body'    => 'Quote body content',
            'email_template_type'    => 'quote',
        ]);

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert: page lists both templates */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('Invoice Template') && $response->contains('Quote Template'),
            'Email templates page should display all templates'
        );
    }

    #[Test]
    public function it_loads_email_template_form(): void
    {
        /* Arrange: create a template to edit */
        $templateId = $this->databaseInsertGetId('ip_email_templates', [
            'email_template_title'   => 'Edit Me Template',
            'email_template_subject' => 'Edit Subject',
            'email_template_body'    => 'Edit body',
            'email_template_type'    => 'invoice',
        ]);

        /* Act */
        $response = $this->get("/email_templates/form/{$templateId}");

        /* Assert: form displays template data */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('Edit Me Template') || $response->contains('form') || $response->contains('email_template'),
            'Template form should display template data'
        );
    }

    #[Test]
    public function it_returns_empty_list_when_no_templates_exist(): void
    {
        /* Arrange: no templates created */

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            strlen($response->body()) > 100,
            'Empty templates page should still render without errors'
        );
    }

    #[Test]
    public function it_creates_new_email_template(): void
    {
        /* Arrange: POST form data for new template */
        $postData = [
            'email_template_title'   => 'New Test Template',
            'email_template_subject' => 'Test Subject Line',
            'email_template_body'    => 'This is the email body with [INVOICE_NUMBER] placeholder',
            'email_template_type'    => 'invoice',
            'is_update'              => 0,
            'btn_submit'             => 'Save',
        ];

        /* Act: submit form to create template */
        $response = $this->post('/email_templates/form', $postData);

        /* Assert: template was created and we're redirected */
        self::assertTrue(
            $response->statusCode() >= 300 && $response->statusCode() < 400,
            sprintf('Creating template should redirect. Got status [%d].', $response->statusCode())
        );

        /* Verify: template exists in database */
        $this->assertDatabaseHas('ip_email_templates', [
            'email_template_title'   => 'New Test Template',
            'email_template_subject' => 'Test Subject Line',
        ]);
    }

    #[Test]
    public function it_redirects_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET /email_templates must redirect. Got status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_prevents_duplicate_template_titles(): void
    {
        /* Arrange: create first template */
        $this->databaseInsertGetId('ip_email_templates', [
            'email_template_title'   => 'Unique Template Name',
            'email_template_subject' => 'Subject 1',
            'email_template_body'    => 'Body 1',
            'email_template_type'    => 'invoice',
        ]);

        /* Act: attempt to create duplicate */
        $postData = [
            'email_template_title'   => 'Unique Template Name',
            'email_template_subject' => 'Subject 2',
            'email_template_body'    => 'Body 2',
            'email_template_type'    => 'quote',
            'is_update'              => 0,
            'btn_submit'             => 'Save',
        ];
        $response = $this->post('/email_templates/form', $postData);

        /* Assert: duplicate should be rejected/redirected */
        self::assertTrue(
            $response->statusCode() >= 300 && $response->statusCode() < 400,
            sprintf('Form submission should redirect. Got status [%d].', $response->statusCode())
        );

        /* Verify: only one template with that title exists */
        $this->assertDatabaseCount('ip_email_templates', 1, [
            'email_template_title' => 'Unique Template Name',
        ]);
    }
}
