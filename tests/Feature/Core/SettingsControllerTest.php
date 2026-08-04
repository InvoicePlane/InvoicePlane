<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class SettingsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpDatabase();
        parent::setUp();
        $this->actingAsAdmin();
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'true',
            'DISABLE_SETUP'   => 'true',
        ]);
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/settings] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_warns_admins_when_setup_security_flags_are_not_enabled(): void
    {
        /* Arrange */
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'true',
            'DISABLE_SETUP'   => 'false',
        ]);

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Security Warning');
        $this->assertResponseBodyContains($response, 'DISABLE_SETUP is set to false');
        $this->assertResponseBodyContains($response, 'Please edit ipconfig.php');
    }

    #[Test]
    public function it_warns_when_a_saved_custom_invoice_template_is_missing_from_ipconfig(): void
    {
        /* Arrange */
        $this->setSetting('pdf_invoice_template', 'Legacy Custom Invoice');

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Custom template configuration required');
        $this->assertResponseBodyContains($response, 'CUSTOM_INVOICE_TEMPLATES_PDF');
        $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');
        $this->assertResponseBodyContains($response, 'CUSTOM_TEMPLATES_FOLDER');
    }

    #[Test]
    public function it_does_not_warn_when_a_saved_custom_invoice_template_is_allowlisted_in_ipconfig(): void
    {
        /* Arrange */
        $this->setSetting('pdf_invoice_template', 'Legacy Custom Invoice');
        $this->withEnvironment([
            'CUSTOM_INVOICE_TEMPLATES_PDF' => 'Legacy Custom Invoice',
        ]);

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyNotContains($response, 'Custom template configuration required');
        $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key'   => $key,
            'setting_value' => '',
        ]);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
