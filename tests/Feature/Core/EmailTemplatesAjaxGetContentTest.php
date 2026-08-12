<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class EmailTemplatesAjaxGetContentTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_gets_the_content_of_an_existing_template(): void
    {
        /* Arrange */
        $templateId = $this->databaseInsert('ip_email_templates', [
            'email_template_title' => 'Ajax Get Content Template',
            'email_template_type'  => 'invoice',
            'email_template_body'  => 'Marker body content',
        ]);

        /* Act */
        $response = $this->ajax('POST', '/email_templates/ajax/get_content', ['email_template_id' => (string) $templateId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Marker body content');
    }

    #[Test]
    public function it_returns_null_for_an_unknown_template_id(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->ajax('POST', '/email_templates/ajax/get_content', ['email_template_id' => '999999']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertSame('null', trim($response->body()));
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->post('/email_templates/ajax/get_content', ['email_template_id' => '1']);

        /* Assert */
        self::assertSame('', $response->body());
    }
}
