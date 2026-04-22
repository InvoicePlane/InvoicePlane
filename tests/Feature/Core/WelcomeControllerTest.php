<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Feature\Core\WelcomeController::class)]
class WelcomeControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_displays_welcome_page(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get(route('welcome'));

        /* Assert */
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }
}
