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
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password reset.');
    }

    #[Test]
    public function it_reset_password_link_can_be_requested(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password reset.');
    }

    #[Test]
    public function it_reset_password_screen_can_be_rendered(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password reset.');
    }

    #[Test]
    public function it_password_can_be_reset_with_valid_token(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password reset.');
    }
}
