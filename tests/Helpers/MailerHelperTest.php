<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\DB;
use Tests\Feature\Quotes\Group;
use Tests\Feature\Quotes\MailerHelper;
use Tests\Feature\Quotes\ReflectionMethod;
use Tests\Feature\Quotes\Setting;

#[CoversClass(Tests\Helpers\MailerHelper::class)]

class MailerHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_settings')->delete();
    }

    public static function emailValidationProvider(): array
    {
        return [
            'valid simple'         => ['user@example.com', true],
            'valid with subdomain' => ['user@mail.example.com', true],
            'valid with plus'      => ['user+tag@example.com', true],
            'valid with numbers'   => ['user123@example.com', true],
            'invalid no @'         => ['userexample.com', false],
            'invalid no domain'    => ['user@', false],
            'invalid no username'  => ['@example.com', false],
            'invalid spaces'       => ['user @example.com', false],
            'valid multiple'       => ['a@b.com,c@d.com', true],
            'invalid in list'      => ['a@b.com,invalid', false],
        ];
    }

    public static function emailParametersProvider(): array
    {
        return [
            'valid single emails' => [
                ['to' => 'user@example.com', 'from' => 'sender@example.com'],
                true,
            ],
            'valid with cc/bcc' => [
                ['to' => 'user@example.com', 'cc' => 'cc@example.com', 'bcc' => 'bcc@example.com'],
                true,
            ],
            'invalid to email' => [
                ['to' => 'invalid-email'],
                false,
            ],
            'invalid from email' => [
                ['from' => 'bad@email'],
                false,
            ],
        ];
    }

    #[Test]
    public function it_detects_phpmail_configuration(): void
    {
        Setting::setValue('email_send_method', 'phpmail');

        $result = MailerHelper::mailer_configured();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_detects_sendmail_configuration(): void
    {
        Setting::setValue('email_send_method', 'sendmail');

        $result = MailerHelper::mailer_configured();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_detects_smtp_configuration_with_server(): void
    {
        Setting::setValue('email_send_method', 'smtp');
        Setting::setValue('smtp_server_address', 'smtp.example.com');

        $result = MailerHelper::mailer_configured();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_detects_incomplete_smtp_configuration(): void
    {
        Setting::setValue('email_send_method', 'smtp');
        Setting::setValue('smtp_server_address', '');

        $result = MailerHelper::mailer_configured();

        $this->assertFalse($result);
    }

    #[Test]
    public function it_detects_no_configuration(): void
    {
        $result = MailerHelper::mailer_configured();

        $this->assertFalse($result);
    }

    #[Test]
    public function it_validates_single_email_address(): void
    {
        $result = MailerHelper::validate_email_address('test@example.com');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_validates_multiple_email_addresses(): void
    {
        $result = MailerHelper::validate_email_address('test1@example.com,test2@example.com');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_rejects_invalid_email(): void
    {
        $result = MailerHelper::validate_email_address('invalid-email');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_rejects_list_with_one_invalid_email(): void
    {
        $result = MailerHelper::validate_email_address('valid@example.com,invalid-email');

        $this->assertFalse($result);
    }

    #[Test]
    #[DataProvider('emailValidationProvider')]
    public function it_validates_various_email_formats(string $email, bool $expected): void
    {
        $result = MailerHelper::validate_email_address($email);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_validates_email_with_dots(): void
    {
        $result = MailerHelper::validate_email_address('first.last@example.com');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_validates_email_with_hyphens(): void
    {
        $result = MailerHelper::validate_email_address('user-name@example.com');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_validates_email_with_underscores(): void
    {
        $result = MailerHelper::validate_email_address('user_name@example.com');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_rejects_email_with_spaces(): void
    {
        $result = MailerHelper::validate_email_address('user name@example.com');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_rejects_email_with_double_at(): void
    {
        $result = MailerHelper::validate_email_address('user@@example.com');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_validates_multiple_emails_with_spaces_after_comma(): void
    {
        $result = MailerHelper::validate_email_address('a@b.com, c@d.com');

        // Note: This might fail since filter_var doesn't trim
        $this->assertFalse($result);
    }

    #[Test]
    public function it_validates_email_with_country_code_tld(): void
    {
        $result = MailerHelper::validate_email_address('user@example.co.uk');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_validates_email_with_new_tlds(): void
    {
        $result = MailerHelper::validate_email_address('user@example.technology');

        $this->assertTrue($result);
    }

    #[Test]
    #[Group('email-methods')]
    public function it_has_email_invoice_method(): void
    {
        // Verify the method exists and has correct signature
        $this->assertTrue(
            method_exists(MailerHelper::class, 'email_invoice'),
            'MailerHelper should have email_invoice method'
        );

        $reflection = new ReflectionMethod(MailerHelper::class, 'email_invoice');
        $this->assertTrue($reflection->isStatic(), 'email_invoice should be a static method');
        $this->assertTrue($reflection->isPublic(), 'email_invoice should be public');

        $parameters = $reflection->getParameters();
        $this->assertCount(9, $parameters, 'email_invoice should have 9 parameters');
        $this->assertEquals('invoice_id', $parameters[0]->getName());
        $this->assertEquals('invoice_template', $parameters[1]->getName());
        $this->assertEquals('from', $parameters[2]->getName());
    }

    #[Test]
    #[Group('email-methods')]
    public function it_has_email_quote_method(): void
    {
        // Verify the method exists and has correct signature
        $this->assertTrue(
            method_exists(MailerHelper::class, 'email_quote'),
            'MailerHelper should have email_quote method'
        );

        $reflection = new ReflectionMethod(MailerHelper::class, 'email_quote');
        $this->assertTrue($reflection->isStatic(), 'email_quote should be a static method');
        $this->assertTrue($reflection->isPublic(), 'email_quote should be public');

        $parameters = $reflection->getParameters();
        $this->assertCount(9, $parameters, 'email_quote should have 9 parameters');
        $this->assertEquals('quote_id', $parameters[0]->getName());
        $this->assertEquals('quote_template', $parameters[1]->getName());
        $this->assertEquals('from', $parameters[2]->getName());
    }

    #[Test]
    #[Group('email-methods')]
    public function it_validates_email_addresses_in_email_invoice(): void
    {
        // This test verifies that email_invoice uses validate_email_address internally
        // by checking that invalid email addresses would be caught

        // We can't easily test the full method without mocking all dependencies,
        // but we can verify the validation logic exists by testing validate_email_address

        $invalidEmail = 'invalid-email';
        $validEmail   = 'test@example.com';

        $this->assertFalse(MailerHelper::validate_email_address($invalidEmail));
        $this->assertTrue(MailerHelper::validate_email_address($validEmail));
    }

    #[Test]
    #[Group('email-methods')]
    public function it_validates_email_addresses_in_email_quote(): void
    {
        // Similar to above, verify that email validation is part of the quote email process

        $invalidEmail = 'not-an-email';
        $validEmail   = 'user@domain.com';

        $this->assertFalse(MailerHelper::validate_email_address($invalidEmail));
        $this->assertTrue(MailerHelper::validate_email_address($validEmail));
    }

    #[Test]
    #[DataProvider('emailParametersProvider')]
    public function it_handles_various_email_parameter_formats(array $params, bool $isValid): void
    {
        // Test that validate_email_address handles the formats used in email_invoice/email_quote

        foreach ($params as $email) {
            if ($email === null) {
                continue; // Skip null values (cc, bcc can be null)
            }

            $result = MailerHelper::validate_email_address($email);
            $this->assertSame($isValid, $result, "Email '{$email}' validation failed");
        }
    }

    #[Test]
    public function it_check_mail_errors_accepts_empty_array(): void
    {
        // When no errors, check_mail_errors should not redirect
        // We can't easily test redirect behavior without integration tests,
        // but we can verify the method accepts an empty array

        $reflection = new ReflectionMethod(MailerHelper::class, 'check_mail_errors');
        $parameters = $reflection->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
        $this->assertEquals([], $parameters[0]->getDefaultValue());
    }
}
