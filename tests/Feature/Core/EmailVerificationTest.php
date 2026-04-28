<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

class EmailVerificationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_email_verification_screen_can_be_rendered(): void
    {
        $user = $this->seedModel('User');

        $this->actingAs($user);
        $response = $this->get('/verify-email');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_email_can_be_verified(): void
    {
        $user = $this->seedModel('User');

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user);
        $response = $this->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/dashboard' . '?verified=1');
    }

    #[Test]
    public function it_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->seedModel('User');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user);
        $this->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
