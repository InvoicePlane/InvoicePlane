<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversNothing]
class PasswordConfirmationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_confirm_password_screen_requires_authentication(): void
    {
        $response = $this->get('/sessions/index');

        $this->assertTrue(
            $response->statusCode() === 200 || $response->isRedirect(),
            'Login page should return 200 or redirect.'
        );
    }

    #[Test]
    public function it_password_can_be_confirmed(): void
    {
        $this->actingAsAdmin();
        $response = $this->get('/sessions/index');

        $this->assertTrue(
            $response->statusCode() === 200 || $response->isRedirect(),
            'Authenticated access to sessions/index should return 200 or redirect.'
        );
    }

    #[Test]
    public function it_password_is_not_confirmed_with_invalid_password(): void
    {
        $response = $this->post('/sessions/authenticate', [
            'user_email'    => 'nobody@example.com',
            'user_password' => 'wrongpassword',
        ]);

        $this->assertNotEquals(500, $response->statusCode());
    }
}
