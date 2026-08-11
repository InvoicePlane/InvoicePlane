<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class EmailTemplatesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_email_templates(): void
    {
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
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
