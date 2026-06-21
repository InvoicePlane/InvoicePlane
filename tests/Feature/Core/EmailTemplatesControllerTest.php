<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class EmailTemplatesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_email_templates', [
            'email_template_title' => 'Test Email Template',
            'email_template_subject' => 'Test Subject',
            'email_template_body'    => 'Test body',
            'email_template_type'    => 'invoice',
        ]);

        /* Act */
        $response = $this->get('/email_templates');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_email_templates', ['email_template_title' => 'Test Email Template']);
        $this->assertResponseBodyContains($response, '<html');
    }
}
