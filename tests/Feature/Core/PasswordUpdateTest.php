<?php

namespace Tests\Feature\Core;

use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use InteractsWithDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = $this->seedModel('User');

        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = $this->seedModel('User');

        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/settings/profile');
    }
}
