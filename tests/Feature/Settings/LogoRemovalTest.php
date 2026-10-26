<?php

namespace Tests\Feature\Settings;

use Tests\AbstractTestCase;

/**
 * Logo removal functionality tests.
 *
 * Verifies that invoice and login logo removal works correctly,
 * especially on installations without URL rewriting (REMOVE_INDEXPHP=false).
 * See issue: base_url() vs site_url() bug causing 404 on logo removal.
 */

final class LogoRemovalTest extends AbstractTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_removes_invoice_logo_as_admin(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_invoice_logo.png',
        ]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/invoice');

        /* Assert */
        $this->assertEquals(302, $response->status());
        $this->assertStringContainsString('/settings', $response->header('Location'));
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => '',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_removes_login_logo_as_admin(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'login_logo',
            'setting_value' => 'test_login_logo.png',
        ]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/login');

        /* Assert */
        $this->assertEquals(302, $response->status());
        $this->assertStringContainsString('/settings', $response->header('Location'));
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'login_logo',
            'setting_value' => '',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_logo_removal_without_auth(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_invoice_logo.png',
        ]);

        /* Act */
        $response = $this->request('POST', '/settings/remove_logo/invoice');

        /* Assert */
        $this->assertEquals(307, $response->status());
        $this->assertStringContainsString('/sessions/login', $response->header('Location'));
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_invoice_logo.png',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_logo_removal_without_csrf(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_invoice_logo.png',
        ]);

        /* Act */
        $response = $this->request('POST', '/settings/remove_logo/invoice');

        /* Assert */
        $this->assertEquals(403, $response->status());
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_invoice_logo.png',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_logo_gracefully(): void
    {
        /* Arrange */
        $this->actingAsAdmin();

        /* Act */
        $response = $this->postWithValidCsrfToken('/settings/remove_logo/invoice');

        /* Assert */
        $this->assertEquals(302, $response->status());
        $this->assertStringContainsString('/settings', $response->header('Location'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_site_url_routing(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'logo.png',
        ]);

        /* Act: Render settings page and extract the removal URL from button */
        $this->withEnvironment(['REMOVE_INDEXPHP' => 'false']);
        $settingsResponse = $this->request('GET', '/settings');
        $this->assertEquals(200, $settingsResponse->status());

        preg_match(
            '/formaction="([^"]*settings\/remove_logo\/invoice[^"]*)"/',
            $settingsResponse->body(),
            $matches
        );
        $this->assertNotEmpty($matches, 'Could not find remove_logo button in settings page');
        $removalUrl = $matches[1];

        $response = $this->postWithValidCsrfToken($removalUrl);

        /* Assert */
        $this->assertEquals(302, $response->status());
        $this->assertDatabaseHas('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => '',
        ]);

        /* Also test direct route access works */
        $this->databaseInsert('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'logo2.png',
        ]);
        $directResponse = $this->postWithValidCsrfToken('/settings/remove_logo/invoice');
        $this->assertEquals(302, $directResponse->status());
    }
}
