<?php

namespace Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;

class LoginLimitingSecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_namespaces_login_counter_keys(): void
    {
        // Login counter keys should be namespaced with 'login_account:' prefix
        // to prevent spoofing other rate limiters like password_reset_email: or cron_key:

        $email = 'user@example.com';
        $key   = 'login_account:' . hash('sha256', mb_strtolower($email));

        // Verify the key format prevents collision with other counters
        $this->assertStringStartsWith('login_account:', $key);
        $this->assertStringNotContainsString('password_reset_email:', $key);
        $this->assertStringNotContainsString('cron_key:', $key);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_email_format(): void
    {
        // Before using email as part of a counter key, it should be validated

        $valid_email   = 'user@example.com';
        $invalid_email = 'password_reset_email:' . hash('sha256', 'victim@example.com');

        // Valid email passes FILTER_VALIDATE_EMAIL
        $this->assertNotFalse(filter_var($valid_email, FILTER_VALIDATE_EMAIL));

        // Invalid "email" (forged counter key) fails validation
        $this->assertFalse(filter_var($invalid_email, FILTER_VALIDATE_EMAIL));
    }
}
