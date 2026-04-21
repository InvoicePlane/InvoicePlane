<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_settings_page_and_saves_settings()
    {
        // Act: call the index route
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Settings'); // Adjust to match actual page content

        // Arrange: prepare settings data
        $settings = [
            'tax_rate_decimal_places' => 2,
            'currency_symbol'         => '$',
            // add other required fields
        ];

        // Act: post to the index route to save settings
        $response = $this->post(route('settings.index'), ['settings' => $settings]);

        // Assert: settings are saved in the database
        $this->assertDatabaseHas('ip_settings', ['key' => 'tax_rate_decimal_places', 'value' => '2']);
        $this->assertDatabaseHas('ip_settings', ['key' => 'currency_symbol', 'value' => '$']);
        $response->assertRedirect(route('settings.index'));
    }
}
