<?php

namespace Tests\Feature\Core;

use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

class EmailVerificationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $this->markTestIncomplete('weak test');
        $user = $this->seedModel('User');

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->seedModel('User');

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $this->markTestIncomplete('weak test');
        $user = $this->seedModel('User');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
