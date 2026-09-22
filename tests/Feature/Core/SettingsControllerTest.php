<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Settings controller — application/modules/settings/controllers/Settings.php.
 *
 * Settings is a single large form plus a logo-removal action. Absorbs
 * Issue1551SettingsRemoveLogoTest and Settings/SettingsRemoveLogoRegressionTest.
 */
#[Group('settings')]
#[CoversClass(\Settings::class)]
class SettingsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'true',
            'DISABLE_SETUP'   => 'true',
        ]);
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_settings_page_with_a_stored_value(): void
    {
        /* Arrange */
        $this->setSetting('cron_key', 'visible-cron-key-42');

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseBodyContains($response, 'visible-cron-key-42');
        $this->assertResponseBodyNotContains($response, 'A PHP Error was encountered');
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

    #[Test]
    public function it_persists_a_changed_setting(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/settings', [
            'settings'   => ['cron_key' => 'abc123def456'],
            'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'abc123def456']);
    }

    // -------------------------------------------------------------------------
    // Logo removal — Settings::remove_logo (absorbed regressions)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_removes_the_invoice_logo(): void
    {
        /* Arrange */
        $this->setSetting('invoice_logo', 'invoice-logo.png');

        /* Act */
        $response = $this->post('/settings/remove_logo/invoice', []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => '']);
    }

    #[Test]
    public function it_removes_the_login_logo(): void
    {
        /* Arrange */
        $this->setSetting('login_logo', 'login-logo.png');

        /* Act */
        $response = $this->post('/settings/remove_logo/login', []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'login_logo', 'setting_value' => '']);
    }

    #[Test]
    public function it_ignores_an_unknown_logo_type(): void
    {
        /* Arrange */
        $this->setSetting('invoice_logo', 'keep-me.png');

        /* Act */
        $response = $this->post('/settings/remove_logo/not_a_real_type', []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'An unknown logo type redirects without touching any setting.');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'keep-me.png']);
    }

    #[Test]
    public function it_does_not_remove_a_logo_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $this->setSetting('invoice_logo', 'guarded.png');

        /* Act */
        $response = $this->postWithoutCsrfToken('/settings/remove_logo/invoice');

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'guarded.png']);
    }

    // -------------------------------------------------------------------------
    // Setup / custom-template warnings (retained from the old SettingsControllerTest)
    // -------------------------------------------------------------------------

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
        $this->assertResponseBodyContains($response, 'Security Warning');
        $this->assertResponseBodyContains($response, 'DISABLE_SETUP is set to false');
    }

    #[Test]
    public function it_warns_when_a_saved_custom_invoice_template_is_missing_from_ipconfig(): void
    {
        /* Arrange */
        $this->setSetting('pdf_invoice_template', 'Legacy Custom Invoice');

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Custom template configuration required');
        $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');
    }

    #[Test]
    public function it_does_not_warn_when_a_saved_custom_invoice_template_is_allowlisted_in_ipconfig(): void
    {
        /* Arrange */
        $this->setSetting('pdf_invoice_template', 'Legacy Custom Invoice');
        $this->withEnvironment(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'Legacy Custom Invoice']);

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseBodyNotContains($response, 'Custom template configuration required');
        $this->assertResponseBodyContains($response, 'Legacy Custom Invoice');
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_away_from_settings(): void
    {
        /* Arrange */
        $this->setSetting('cron_key', 'guest-must-not-see-this');
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'guest-must-not-see-this');
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
