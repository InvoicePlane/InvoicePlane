<?php

namespace Tests\Feature\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Stored XSS via the system_theme setting (CWE-79, leading to CWE-116/CWE-20/CWE-693).
 *
 * system_theme was persisted with no server-side allowlist (Settings::index() only guards
 * decimal_point/currency_symbol/date_format/number_format/pdf_watermark/default_language/
 * default_country -- system_theme had no case at all) and echoed unescaped by
 * _theme_asset() (application/helpers/echo_helper.php) into a double-quoted href="..."
 * attribute on 18 views, including the unauthenticated login page -- so an attacker-controlled
 * value executes in the browser of every visitor, logged in or not.
 *
 * The attribute-breakout payload below contains no '<', so CI3's tag-context xss_clean()
 * (XSS_Protection_Trait::filter_input(), used by Admin_Controller) does not touch it either;
 * these tests therefore write the value directly to the database, as a value stored before
 * this fix existed (or hypothetically smuggled past some future change to the input filter)
 * would already be, and assert the view-layer encoding independently of the POST-time
 * allowlist added to Settings::index() -- defense in depth, per the project's own established
 * pattern (see SettingsControllerTest::it_renders_the_week_start_setting_escaped_for_the_javascript_context()).
 *
 * GHSA-hmqm-59ff-gmcw: Stored XSS on the Unauthenticated Login Page via the system_theme Setting
 */
#[Group('security')]
class ThemeAssetStoredXssTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    #[Test]
    public function it_escapes_a_malicious_system_theme_value_on_the_unauthenticated_login_page(): void
    {
        /* Arrange: the exact attribute-breakout shape from the report, trimmed to what proves
         * the escaping -- a literal '"' reaching the page unescaped is the breakout itself. */
        $this->setSettingDirectly('system_theme', 'x" onerror="alert(1)" x="');

        /* Act */
        $response = $this->get('/sessions/login');

        /* Assert: the raw payload (with its un-encoded quotes) must not appear -- htmlspecialchars()
         * with ENT_QUOTES turns '"' into '&quot;', which cannot break out of href="...". */
        $this->assertResponseOk($response);
        $this->assertResponseBodyNotContains($response, 'x" onerror="alert(1)" x="');
        $this->assertResponseBodyContains($response, 'x&quot; onerror=&quot;alert(1)&quot; x=&quot;');
    }

    #[Test]
    public function it_refuses_a_system_theme_outside_the_installed_themes_and_saves_none_of_the_batch(): void
    {
        /* Arrange: the POST-time allowlist, independent of the view-layer escaping above */
        $this->actingAsAdmin();
        $this->setSettingDirectly('system_theme', 'invoiceplane_blue');
        $this->setSettingDirectly('cron_key', 'original-cron-key');

        /* Act */
        $response = $this->post('/settings', ['settings' => ['system_theme' => 'x" onerror="alert(1)" x="', 'cron_key' => 'must-not-be-saved'], 'btn_submit' => '1']);

        /* Assert: rejected before the batch write, so the harmless sibling field is not persisted either */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'system_theme', 'setting_value' => 'invoiceplane_blue']);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'cron_key', 'setting_value' => 'original-cron-key']);
    }

    #[Test]
    public function it_accepts_an_installed_system_theme(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->setSettingDirectly('system_theme', 'invoiceplane');

        /* Act */
        $this->post('/settings', ['settings' => ['system_theme' => 'invoiceplane_blue'], 'btn_submit' => '1']);

        /* Assert: positive control, the allowlist does not block legitimate themes */
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'system_theme', 'setting_value' => 'invoiceplane_blue']);
    }

    private function setSettingDirectly(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }
}
