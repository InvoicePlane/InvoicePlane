<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_welcome_page()
    {
        // Act: visit the welcome page
        $response = $this->get(route('welcome'));

        // Assert: page is displayed successfully
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }
}
