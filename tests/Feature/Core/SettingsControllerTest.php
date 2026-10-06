<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Settings;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Settings controller — application/modules/settings/controllers/Settings.php.
 *
 * Settings is a single large form plus a logo-removal action. Absorbs
 * Issue1551SettingsRemoveLogoTest and Settings/SettingsRemoveLogoRegressionTest.
 */
#[Group('settings')]
#[CoversClass(Settings::class)]
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

    /** @return array<string, array{0: string, 1: string}> */
    public static function unsafeLogoNames(): array
    {
        return [
            'invoice traversal' => ['invoice_logo', '../../ipconfig.php'],
            'login traversal'   => ['login_logo', '../bootstrap/kernel.php'],
            'absolute path'     => ['invoice_logo', '/etc/passwd'],
            'windows traversal' => ['login_logo', '..\\..\\ipconfig.php'],
        ];
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

    // -------------------------------------------------------------------------
    // Save — field handling rules
    // -------------------------------------------------------------------------

    #[Test]
    public function it_stores_password_settings_encrypted_never_in_plain_text(): void
    {
        /* Act */
        $this->post('/settings', [
            'settings'   => ['smtp_password' => 'S3cret-Pa55', 'smtp_password_field_is_password' => '1'],
            'btn_submit' => '1',
        ]);

        /* Assert: stored, but not as the plain text, and the meta flag is not persisted as a setting */
        $stored = $this->databaseFetchOne('ip_settings', ['setting_key' => 'smtp_password']);
        self::assertNotNull($stored);
        self::assertNotSame('S3cret-Pa55', $stored['setting_value']);
        self::assertStringNotContainsString('S3cret-Pa55', $stored['setting_value']);
        self::assertGreaterThan(20, strlen($stored['setting_value']), 'An encrypted value is longer than the secret.');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'smtp_password_field_is_password']);
    }

    #[Test]
    public function it_keeps_the_stored_password_when_the_password_field_is_left_blank(): void
    {
        /* Arrange */
        $this->setSetting('smtp_password', 'previously-encrypted-blob');

        /* Act: the form re-posts the password input empty unless the admin retypes it */
        $this->post('/settings', [
            'settings'   => ['smtp_password' => '', 'smtp_password_field_is_password' => '1', 'cron_key' => 'touched-' . 'cron'],
            'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'smtp_password', 'setting_value' => 'previously-encrypted-blob']);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'touched-cron']);
    }

    #[Test]
    public function it_normalizes_amount_fields_using_the_configured_number_format(): void
    {
        /* Arrange: European format */
        $this->setSetting('decimal_point', ',');
        $this->setSetting('thousands_separator', '.');

        /* Act */
        $this->post('/settings', [
            'settings'   => ['default_amount_probe' => '1.234,56', 'default_amount_probe_field_is_amount' => '1'],
            'btn_submit' => '1',
        ]);

        /* Assert */
        $stored = $this->databaseFetchOne('ip_settings', ['setting_key' => 'default_amount_probe']);
        self::assertEquals(1234.56, (float) $stored['setting_value']);
    }

    #[Test]
    public function it_derives_the_separators_from_the_selected_number_format(): void
    {
        /* Act */
        $this->post('/settings', ['settings' => ['number_format' => 'number_format_european'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'decimal_point', 'setting_value' => ',']);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'thousands_separator', 'setting_value' => '.']);
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('unsafeLogoNames')]
    public function it_refuses_an_unsafe_logo_filename_and_saves_none_of_the_batch(string $key, string $value): void
    {
        /* Arrange */
        $this->setSetting('cron_key', 'original-cron-key');

        /* Act */
        $response = $this->post('/settings', ['settings' => [$key => $value, 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert: rejected before the batch write, so even the harmless sibling field is not persisted */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    public function it_blocks_svg_logo_uploads_and_records_the_attempt_in_the_log(): void
    {
        /* Arrange: SVG can carry script, so it is refused before any upload handling. The audit line is also
         * what distinguishes this guard from a generic upload failure (both redirect and save nothing). */
        $this->withFiles([
            'invoice_logo' => ['name' => 'logo.SVG', 'type' => 'image/svg+xml', 'tmp_name' => '/tmp/none', 'error' => 0, 'size' => 120],
            'login_logo'   => ['name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0],
        ]);
        $logFile = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        $offset  = is_file($logFile) ? filesize($logFile) : 0;

        /* Act */
        $response = $this->post('/settings', ['settings' => ['cron_key' => 'x'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'logo.SVG']);
        self::assertFileDoesNotExist(ROOT_PATH . '/uploads/logo.SVG');
        clearstatcache(true, $logFile);
        $logged = is_file($logFile) ? (string) file_get_contents($logFile, false, null, $offset) : '';
        self::assertStringContainsString('WARNING - ', $logged);
        self::assertStringContainsString('SVG upload attempt blocked for invoice_logo', $logged);
        self::assertStringNotContainsString('Undefined array key "WARNING"', $logged, 'CodeIgniter must know the warning level.');
    }

    // -------------------------------------------------------------------------
    // Logo removal — refusal paths
    // -------------------------------------------------------------------------

    #[Test]
    public function it_refuses_to_remove_a_logo_whose_stored_name_escapes_the_uploads_folder(): void
    {
        /* Arrange: a tampered setting pointing at a real, important file */
        $this->setSetting('invoice_logo', '../ipconfig.php');

        /* Act */
        $response = $this->post('/settings/remove_logo/invoice', []);

        /* Assert: the file survives and the setting is left for an operator to inspect */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        self::assertFileExists(ROOT_PATH . '/ipconfig.php');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => '../ipconfig.php']);
    }

    #[Test]
    public function it_deletes_the_logo_file_and_clears_the_setting(): void
    {
        /* Arrange */
        $file = ROOT_PATH . '/uploads/test-remove-logo.png';
        file_put_contents($file, 'png-bytes');
        $this->setSetting('login_logo', 'test-remove-logo.png');

        try {
            /* Act */
            $response = $this->post('/settings/remove_logo/login', []);

            /* Assert */
            $this->assertResponseRedirectsToRoute($response, 'settings');
            self::assertFileDoesNotExist($file);
            $this->assertDatabaseHas('ip_settings', ['setting_key' => 'login_logo', 'setting_value' => '']);
        } finally {
            @unlink($file);
        }
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
