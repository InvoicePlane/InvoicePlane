<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression tests for #1551: Settings controller remove_logo() fails.
 *
 * Issue: In 1.7.2, the remove_logo() method had issues that prevented
 * logo removal functionality from working. This test verifies the fix.
 */
class SettingsRemoveLogoRegressionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_removes_an_invoice_logo(): void
    {
        /* Arrange - Seed an invoice logo setting */
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'invoice_logo',
            'setting_value' => 'test_logo.png',
        ]);

        /* Act - Remove the invoice logo */
        $response = $this->post('/settings/remove_logo/invoice', []);

        /* Assert - Should redirect to settings page */
        $this->assertResponseRedirectsToRoute($response, 'settings');

        /* Verify logo setting was cleared */
        $logoRow = $this->databaseFetchOne('ip_settings', ['setting_key' => 'invoice_logo']);
        self::assertSame('', $logoRow['setting_value'] ?? null, 'Invoice logo setting should be empty after removal');
    }

    #[Test]
    public function it_removes_a_login_logo(): void
    {
        /* Arrange - Seed a login logo setting */
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key' => 'login_logo',
            'setting_value' => 'login_logo.png',
        ]);

        /* Act - Remove the login logo */
        $response = $this->post('/settings/remove_logo/login', []);

        /* Assert - Should redirect to settings page */
        $this->assertResponseRedirectsToRoute($response, 'settings');

        /* Verify logo setting was cleared */
        $logoRow = $this->databaseFetchOne('ip_settings', ['setting_key' => 'login_logo']);
        self::assertSame('', $logoRow['setting_value'] ?? null, 'Login logo setting should be empty after removal');
    }

    #[Test]
    public function it_rejects_invalid_logo_types(): void
    {
        /* Act - Try to remove invalid logo type */
        $response = $this->post('/settings/remove_logo/invalid', []);

        /* Assert - Should redirect to settings page (error handled) */
        $this->assertResponseRedirectsToRoute($response, 'settings');
    }
}
