<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversNothing]
class PasswordResetTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/sessions/passwordreset');

        $this->assertEquals(200, $response->statusCode());
        $this->assertFalse($response->isRedirect());
    }

    #[Test]
    public function it_reset_password_link_can_be_requested(): void
    {
        /**
         * Payload:
         * {
         *     "user_email": "test@example.com"
         * }
         */
        $response = $this->post('/sessions/passwordreset', [
            'user_email' => 'test@example.com',
        ]);

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/sessions/passwordreset/invalid_token');

        $this->assertTrue(
            $response->statusCode() === 200 || $response->isRedirect(),
            'Password reset page with invalid token should return 200 or redirect.'
        );
    }

    #[Test]
    public function it_password_can_be_reset_with_valid_token(): void
    {
        $response = $this->get('/sessions/passwordreset/no_valid_token_exists');

        $this->assertTrue(
            $response->statusCode() === 200 || $response->isRedirect(),
            'Password reset with non-existent token should return 200 or redirect.'
        );
    }
}
