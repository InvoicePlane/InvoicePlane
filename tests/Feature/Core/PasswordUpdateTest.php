<?php

namespace Tests\Feature\Core;

use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_allows_password_to_be_updated(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'secret',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        /* Assert */
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->user_password));
    }

    #[Test]
    public function it_requires_correct_password_to_update_password(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        /* Assert */
        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/settings/profile');
    }
}
