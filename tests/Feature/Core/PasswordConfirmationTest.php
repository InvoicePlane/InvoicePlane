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
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password confirmation.');
    }

    #[Test]
    public function it_password_can_be_confirmed(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password confirmation.');
    }

    #[Test]
    public function it_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 session-based auth, not Laravel password confirmation.');
    }
}
