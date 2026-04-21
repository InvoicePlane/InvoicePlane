<?php

namespace Tests\Feature\Core;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
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
