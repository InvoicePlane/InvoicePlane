<?php

namespace Tests\Feature\Core;

use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use InteractsWithDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->seedModel('User');

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = $this->seedModel('User');

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->seedModel('User');

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
