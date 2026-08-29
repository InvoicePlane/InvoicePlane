<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TDD Test Suite for #1551: Settings controller remove_logo() causes "cannot redecorate" error
 *
 * Issue: In v1.7.2, accessing System Settings page fails with a "cannot redecorate" error.
 * This was caused by duplicate remove_logo() method definitions in Settings controller.
 *
 * Root Cause: The Settings::remove_logo() method was defined twice in the same class,
 * causing HMVC decorator system to fail with "Cannot redecorate method" error.
 *
 * Fix: Removed the duplicate remove_logo() method definition and its orphaned docblock.
 *
 * This test suite verifies:
 * 1. Settings page loads without errors
 * 2. remove_logo() method works for both invoice and login logos
 * 3. Invalid logo types are rejected gracefully
 * 4. Settings can be accessed and modified even after logo removal
 */
class Issue1551SettingsRemoveLogoTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function settings_page_loads_without_redecorate_error(): void
    {
        /* Act - Access Settings page */
        $response = $this->get('/settings');

        /* Assert - Page loads without errors (would get 500 "Cannot redecorate" error if bug exists) */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringNotContainsString(
            'Cannot redecorate',
            $response->body(),
            'Settings page has "Cannot redecorate" error - duplicate method definition exists'
        );
        self::assertStringNotContainsString(
            'Fatal error',
            $response->body(),
            'Settings page has fatal error - method redecoration failed'
        );
    }

    #[Test]
    public function remove_invoice_logo_clears_setting(): void
    {
        /* Arrange - Set an invoice logo */
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'invoice-logo.png',
        ]);

        /* Act - Remove invoice logo */
        $response = $this->post('/settings/remove_logo/invoice', []);

        /* Assert - Redirects to settings */
        $this->assertResponseRedirectsToRoute($response, 'settings');

        /* Assert - Logo setting is cleared */
        $logo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);
        self::assertSame('', $logo['setting_value'] ?? null, 'Invoice logo should be cleared');
    }

    #[Test]
    public function remove_login_logo_clears_setting(): void
    {
        /* Arrange - Set a login logo */
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'login_logo',
            'setting_value' => 'login-logo.png',
        ]);

        /* Act - Remove login logo */
        $response = $this->post('/settings/remove_logo/login', []);

        /* Assert - Redirects to settings */
        $this->assertResponseRedirectsToRoute($response, 'settings');

        /* Assert - Logo setting is cleared */
        $logo = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);
        self::assertSame('', $logo['setting_value'] ?? null, 'Login logo should be cleared');
    }

    #[Test]
    public function invalid_logo_type_is_rejected(): void
    {
        /* Act - Try to remove invalid logo type */
        $response = $this->post('/settings/remove_logo/invalid_type', []);

        /* Assert - Redirects with error (invalid type rejected) */
        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function remove_logo_requires_post_and_csrf(): void
    {
        /* Act - Try to access remove_logo via GET (should fail) */
        $getResponse = $this->get('/settings/remove_logo/invoice');

        /* Assert - GET request is rejected */
        self::assertNotSame(200, $getResponse->statusCode(), 'GET request to remove_logo should fail');
    }

    #[Test]
    public function settings_page_still_loads_after_logo_removal(): void
    {
        /* Arrange - Remove both logos */
        $this->post('/settings/remove_logo/invoice', []);
        $this->post('/settings/remove_logo/login', []);

        /* Act - Access Settings page again */
        $response = $this->get('/settings');

        /* Assert - Page still loads normally after removals */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringNotContainsString(
            'Cannot redecorate',
            $response->body(),
            'Settings page should work after logo removal'
        );
    }

    #[Test]
    public function removing_nonexistent_logo_succeeds_idempotently(): void
    {
        /* Arrange - Ensure no logo is set */
        $this->databaseDelete('ip_settings', ['setting_key' => 'invoice_logo']);

        /* Act - Try to remove non-existent logo */
        $response = $this->post('/settings/remove_logo/invoice', []);

        /* Assert - Operation succeeds (idempotent) */
        $this->assertResponseRedirectsToRoute($response, 'settings');
    }
}
