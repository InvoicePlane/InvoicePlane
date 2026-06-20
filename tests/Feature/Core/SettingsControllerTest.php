<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Settings;
use Tests\AbstractTestCase;

#[CoversClass(Settings::class)]
#[CoversClass(Tests\Feature\Core\SettingsController::class)]
class SettingsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    #[Test]
    public function it_displays_settings_page_and_saves_settings(): void
    {
        /* Act */
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Settings'); // Adjust to match actual page content

        /* Arrange */
        $settings = [
            'tax_rate_decimal_places' => 2,
            'currency_symbol'         => '$',
            // add other required fields
        ];

        /* Act */
        $response = $this->post(route('settings.index'), ['settings' => $settings]);

        /* Assert */
        $this->assertDatabaseHas('ip_settings', ['key' => 'tax_rate_decimal_places', 'value' => '2']);
        $this->assertDatabaseHas('ip_settings', ['key' => 'currency_symbol', 'value' => '$']);
        $response->assertRedirect(route('settings.index'));
    }
}
