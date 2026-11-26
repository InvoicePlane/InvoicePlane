<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * GHSA-qx8h-35wf-cgqf: removing a logo was a state-changing GET link (settings/remove_logo/<type>),
 * so a forged image or link on another site could delete it (CSRF). It must now be a POST. The existing
 * test only covers a POST with a missing token, and a source-level check only looks for ensure_valid_post_request;
 * neither sends the GET that the advisory describes.
 */
final class SettingsRemoveLogoGetTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_does_not_clear_the_logo_setting_on_a_get_request(): void
    {
        /* Arrange: a stale logo setting (file absent), which the POST path would happily clear */
        $this->actingAsAdmin();
        $this->databaseDelete('ip_settings', ['setting_key' => 'invoice_logo']);
        $this->databaseInsert('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'removed-by-get-probe.png']);

        /* Act */
        $response = $this->get('/settings/remove_logo/invoice');

        /* Assert: redirected away and the setting survived */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 404, 'A GET must be refused, not served. Got ' . $response->statusCode());
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'removed-by-get-probe.png']);
    }

    #[Test]
    public function it_clears_a_stale_logo_setting_on_a_valid_post_request(): void
    {
        /* Arrange: same stale setting, as the positive control */
        $this->actingAsAdmin();
        $this->databaseDelete('ip_settings', ['setting_key' => 'invoice_logo']);
        $this->databaseInsert('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'removed-by-post-probe.png']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/invoice');

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => '']);
    }

    #[Test]
    public function it_rejects_an_unknown_logo_type_without_touching_any_setting(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseDelete('ip_settings', ['setting_key' => 'invoice_logo']);
        $this->databaseInsert('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'keep-me.png']);

        /* Act: a type outside the allowlist ("invoice", "login") with a valid token */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/favicon');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        self::assertSame('Invalid file path. Operation not permitted.', $response->sessionValue('alert_error'));
        self::assertNull($response->sessionValue('alert_success'));
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'keep-me.png']);
    }

    #[Test]
    public function it_clears_the_login_logo_setting_for_the_login_type_only(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        foreach (['login_logo' => 'login-stale.png', 'invoice_logo' => 'invoice-stays.png'] as $key => $value) {
            $this->databaseDelete('ip_settings', ['setting_key' => $key]);
            $this->databaseInsert('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        }

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/login');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'login_logo', 'setting_value' => '']);
        $this->assertDatabaseHas('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => 'invoice-stays.png']);
    }

    #[Test]
    public function it_reports_success_without_error_when_no_logo_is_configured(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseDelete('ip_settings', ['setting_key' => 'invoice_logo']);
        $this->databaseInsert('ip_settings', ['setting_key' => 'invoice_logo', 'setting_value' => '']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/invoice');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'settings');
        self::assertSame('Invoice logo successfully removed', $response->sessionValue('alert_success'));
        self::assertNull($response->sessionValue('alert_error'));
    }
}
