<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Email_templates::delete() (application/modules/email_templates).
 */
#[Group('security')]
class Issue1694EmailTemplatesDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_an_email_template_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $templateId = (int) $this->seedModel('EmailTemplate')->email_template_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/email_templates/delete/' . $templateId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('email_templates/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_email_templates', ['email_template_id' => $templateId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $templateId = (int) $this->seedModel('EmailTemplate')->email_template_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/email_templates/delete/' . $templateId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_email_templates', ['email_template_id' => $templateId]);
    }
}
