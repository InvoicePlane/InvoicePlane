<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

class EmailVerificationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_renders_email_verification_screen(): void
    {
        $user = $this->seedModel('User');

        $this->actingAs($user);
        $response = $this->get('/verify-email');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_verifies_email_successfully(): void
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
    public function it_rejects_verification_with_invalid_hash(): void
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
