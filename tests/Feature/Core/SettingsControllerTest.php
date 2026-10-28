<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /** @return array<string, array{0: string, 1: string}> */
    public static function invalidAdminSettingValues(): array
    {
        return [
            'decimal_point xss'          => ['decimal_point', '<script>'],
            'decimal_point multi-char'   => ['decimal_point', '..'],
            'decimal_point empty'        => ['decimal_point', ''],
            'thousands_separator xss'    => ['thousands_separator', '<img src=x onerror=alert(1)>'],
            'thousands_separator event'  => ['thousands_separator', 'onclick=1'],
            'currency_symbol xss'        => ['currency_symbol', '<script>alert(1)</script>'],
            'currency_symbol too long'   => ['currency_symbol', 'ABCDE'],
            'date_format xss'            => ['date_format', "d/m/Y'];alert(1);//"],
            'date_format unknown'        => ['date_format', 'not-a-real-format'],
            'number_format unknown'      => ['number_format', 'not_a_real_format'],
            'system_theme xss'           => ['system_theme', 'x" onerror="alert(1)" x="'],
            'system_theme unknown'       => ['system_theme', 'not_a_real_theme'],
            'system_theme traversal'     => ['system_theme', '../../../etc/passwd'],
            'pdf_watermark not boolean'  => ['pdf_watermark', 'true'],
            'pdf_watermark xss'          => ['pdf_watermark', '<script>alert(1)</script>'],
            'default_language traversal' => ['default_language', '../../../etc/passwd'],
            'default_country xss'        => ['default_country', '<script>alert(1)</script>'],
            'default_country unknown'    => ['default_country', 'zz'],
        ];
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function validAdminSettingValues(): array
    {
        return [
            'currency_symbol'       => ['currency_symbol', '€'],
            'date_format'           => ['date_format', 'd-m-Y'],
            'number_format'         => ['number_format', 'number_format_european'],
            'system_theme'          => ['system_theme', 'invoiceplane_blue'],
            'pdf_watermark on'      => ['pdf_watermark', '1'],
            'pdf_watermark off'     => ['pdf_watermark', '0'],
            'default_language'      => ['default_language', 'english'],
            'default_country empty' => ['default_country', ''],
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

    #[Test]
    public function it_lists_the_real_shipped_themes_in_the_theme_dropdown(): void
    {
        /*
         * Regression for THEME_FOLDER pointing at a missing directory (bootstrap/kernel.php):
         * Mdl_Settings::get_themes() calls directory_map(THEME_FOLDER, 1), which returns false for
         * a missing directory, so the <select> silently had zero <option> elements and every
         * system_theme value -- including the one already stored -- would have failed the
         * allowlist in Settings::index(). The theme descriptors are tracked sources under
         * resources/assets/ (invoiceplane/, invoiceplane_blue/, core/); the built files in
         * public/assets/ are generated and absent on a fresh checkout, so the list must not
         * depend on them.
         */

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseBodyContains($response, 'settings[system_theme]');
        $this->assertResponseBodyContains($response, 'value="invoiceplane_blue"');
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
    public function it_persists_a_valid_first_day_of_week(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/settings', ['settings' => ['first_day_of_week' => '1'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'first_day_of_week', 'setting_value' => '1']);
    }

    #[Test]
    public function it_refuses_a_first_day_of_week_xss_payload_and_saves_none_of_the_batch(): void
    {
        /*
         * Regression for GHSA-x3r7-qm3m-hc48: first_day_of_week was echoed unescaped into a
         * JavaScript string literal in head.php. A value that breaks out of the literal must
         * be rejected server-side, not merely relied on to be escaped at render time.
         */

        /* Arrange */
        $this->setSetting('cron_key', 'original-cron-key');
        $payload = "1' + window['ale'+'rt']('XSS') + '";

        /* Act */
        $response = $this->post('/settings', ['settings' => ['first_day_of_week' => $payload, 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert: rejected before the batch write, so even the harmless sibling field is not persisted */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'first_day_of_week', 'setting_value' => $payload]);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    public function it_refuses_an_out_of_range_first_day_of_week(): void
    {
        /* Arrange */
        $this->setSetting('cron_key', 'original-cron-key');

        /* Act */
        $response = $this->post('/settings', ['settings' => ['first_day_of_week' => '7', 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => 'first_day_of_week', 'setting_value' => '7']);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    #[DataProvider('invalidAdminSettingValues')]
    public function it_refuses_an_invalid_admin_setting_and_saves_none_of_the_batch(string $key, string $value): void
    {
        /*
         * Regression for upstream #1725 (Phases 1-4): every one of these settings is already
         * HTML-escaped at its output sinks, but the POST handler itself accepted any key/value
         * pair in the submitted settings array with no server-side allowlist. This is a second,
         * defense-in-depth layer: a crafted request bypassing the settings form's <select>/
         * single-character inputs entirely must still be rejected before anything is persisted.
         */

        /* Arrange */
        $this->setSetting('cron_key', 'original-cron-key');
        $this->setSetting('decimal_point', '.');
        $this->setSetting('thousands_separator', ',');

        /* Act */
        $response = $this->post('/settings', ['settings' => [$key => $value, 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert: rejected before the batch write, so even the harmless sibling field is not persisted */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseMissing('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    public function it_refuses_a_decimal_point_identical_to_the_thousands_separator(): void
    {
        /* Arrange: the two separators must remain distinguishable in a formatted amount */
        $this->setSetting('cron_key', 'original-cron-key');
        $this->setSetting('thousands_separator', '.');

        /* Act */
        $response = $this->post('/settings', ['settings' => ['decimal_point' => '.', 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    public function it_persists_a_system_theme_whose_directory_name_is_purely_numeric(): void
    {
        /*
         * array_keys() on an array with a purely-numeric string key ('42') returns the
         * PHP-cast int (42), not the original string, because PHP always normalizes a
         * numeric-string array key to int. in_array($value, array_keys($themes), true) then
         * compares the submitted string '42' against the int 42 with strict ===, which is
         * never true -- so a real, legitimately-shipped theme named e.g. "42" or "2025" could
         * never be selected. Reproduced with a real theme directory (mirroring how
         * Mdl_Settings::get_themes() discovers themes by directory scan), not a mock.
         */

        /* Arrange */
        $themeDir = THEME_FOLDER . '42';
        mkdir($themeDir);
        file_put_contents($themeDir . '/42.theme', "TITLE='Numeric Theme'\n");

        try {
            /* Act */
            $response = $this->post('/settings', ['settings' => ['system_theme' => '42'], 'btn_submit' => '1']);

            /* Assert */
            $this->assertResponseRedirectsToRoute($response, 'settings');
            $this->assertDatabaseHas('ip_settings', ['setting_key' => 'system_theme', 'setting_value' => '42']);
        } finally {
            @unlink($themeDir . '/42.theme');
            @rmdir($themeDir);
        }
    }

    #[Test]
    public function it_refuses_an_array_submitted_as_system_theme_without_a_fatal_error(): void
    {
        /* Arrange: a crafted request bypassing the <select> entirely, e.g. settings[system_theme][]=x */
        $this->setSetting('cron_key', 'original-cron-key');

        /* Act */
        $response = $this->post('/settings', ['settings' => ['system_theme' => ['x'], 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert: rejected cleanly (redirect + flash), not a 500 from an uncaught TypeError */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    #[DataProvider('validAdminSettingValues')]
    public function it_persists_a_valid_admin_setting(string $key, string $value): void
    {
        /* Arrange: $key/$value come from the data provider, each one a value the settings
         * form itself would submit. */

        /* Act */
        $response = $this->post('/settings', ['settings' => [$key => $value], 'btn_submit' => '1']);

        /* Assert: the new validation does not reject values the form itself would submit */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
    }

    #[Test]
    public function it_persists_a_valid_single_character_decimal_point(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', ',');

        /* Act */
        $response = $this->post('/settings', ['settings' => ['decimal_point' => '.'], 'btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'decimal_point', 'setting_value' => '.']);
    }

    #[Test]
    public function it_renders_the_week_start_setting_escaped_for_the_javascript_context(): void
    {
        /* Arrange: a value that cannot currently be stored (validation blocks it going forward),
         * but proves the view-layer encoding independently of the controller guard — defense in
         * depth, per the fix's own two-layer design. */
        $this->setSetting('first_day_of_week', "1' + alert(1) + '");

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $this->assertResponseBodyNotContains($response, "weekStart: '1' + alert(1) + ''");
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

    #[Test]
    public function it_renders_the_remove_logo_buttons_inside_the_settings_form_instead_of_nesting_forms(): void
    {
        /*
         * GHSA-qx8h-35wf-cgqf made logo removal a POST. The buttons were wrapped in their own <form>, but the
         * settings tabs render inside <form id="form-settings">: browsers drop a nested form tag, so the button
         * submitted the settings form and the logo could not be removed from the UI.
         */

        /* Arrange */
        $this->setSetting('login_logo', 'login-logo.png');
        $this->setSetting('invoice_logo', 'invoice-logo.png');
        $this->withEnvironment(['REMOVE_INDEXPHP' => 'true']);

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        $body      = $response->body();
        $formOpen  = strpos($body, 'id="form-settings"');
        $formClose = strpos($body, '</form>', (int) $formOpen);
        self::assertNotFalse($formOpen);
        foreach (['login', 'invoice'] as $kind) {
            $needle = 'formaction="http://localhost/settings/remove_logo/' . $kind . '"';
            $at     = strpos($body, $needle);
            self::assertNotFalse($at, "No remove-logo button for the {$kind} logo.");
            self::assertGreaterThan($formOpen, $at);
            self::assertLessThan($formClose, $at, 'The button must sit inside the settings form, which carries the CSRF field.');
        }
        self::assertStringNotContainsString('<form method="post" action="http://localhost/settings/remove_logo', $body, 'A nested form is dropped by browsers.');
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
