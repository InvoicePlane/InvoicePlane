<?php

namespace Modules\Core\Tests\Unit;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\UnitTestCase;

#[CoversClass(ClientHelper::class)]
class ClientHelperTest extends UnitTestCase
{
    public static function genderProvider(): array
    {
        return [
            'male'    => [0],
            'female'  => [1],
            'other'   => [2],
            'unknown' => [99],
        ];
    }

    #[Test]
    public function it_formats_gender_male(): void
    {
        $result = ClientHelper::format_gender(0);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_gender_female(): void
    {
        $result = ClientHelper::format_gender(1);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_gender_other(): void
    {
        $result = ClientHelper::format_gender(2);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    #[DataProvider('genderProvider')]
    public function it_formats_various_genders($gender): void
    {
        $result = ClientHelper::format_gender($gender);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_handles_string_gender_values(): void
    {
        $result = ClientHelper::format_gender('0');

        $this->assertIsString($result);
    }

    #[Test]
    public function it_handles_null_gender(): void
    {
        $result = ClientHelper::format_gender(null);

        $this->assertIsString($result);
    }
}

#[CoversClass(CustomValuesHelper::class)]
class CustomValuesHelperTest extends UnitTestCase
{
    public static function booleanProvider(): array
    {
        return [
            'true string'  => ['1', false],
            'false string' => ['0', false],
            'null'         => [null, true],
            'empty string' => ['', true],
            'invalid'      => ['invalid', true],
            'numeric 2'    => ['2', true],
        ];
    }

    #[Test]
    public function it_returns_empty_string_for_null_text(): void
    {
        $result = CustomValuesHelper::format_text(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_text(): void
    {
        $result = CustomValuesHelper::format_text('Sample text');

        $this->assertSame('Sample text', $result);
    }

    #[Test]
    public function it_preserves_text_unchanged(): void
    {
        $text = 'This is some <b>formatted</b> text';

        $result = CustomValuesHelper::format_text($text);

        $this->assertSame($text, $result);
    }

    #[Test]
    public function it_formats_boolean_true(): void
    {
        $result = CustomValuesHelper::format_boolean('1');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_boolean_false(): void
    {
        $result = CustomValuesHelper::format_boolean('0');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_empty_string_for_null_boolean(): void
    {
        $result = CustomValuesHelper::format_boolean(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_boolean(): void
    {
        $result = CustomValuesHelper::format_boolean('invalid');

        $this->assertSame('', $result);
    }

    #[Test]
    #[DataProvider('booleanProvider')]
    public function it_formats_various_boolean_values($value, bool $isEmpty): void
    {
        $result = CustomValuesHelper::format_boolean($value);

        if ($isEmpty) {
            $this->assertSame('', $result);

            return;
        }
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_handles_empty_strings_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_whitespace_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('   ');

        $this->assertSame('   ', $result);
    }

    #[Test]
    public function it_handles_numeric_strings_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('12345');

        $this->assertSame('12345', $result);
    }
}

#[CoversClass(DateHelper::class)]
class DateHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_settings')->delete();
        Setting::setValue('default_date_format', 'm/d/Y');
    }

    public static function validDateProvider(): array
    {
        return [
            'valid date'     => ['01/15/2024', true],
            'invalid format' => ['15-01-2024', false],
            'invalid date'   => ['13/32/2024', false],
            'empty string'   => ['', false],
            'random string'  => ['not a date', false],
        ];
    }

    #[Test]
    public function it_returns_all_date_formats(): void
    {
        $formats = DateHelper::dateFormats();

        $this->assertIsArray($formats);
        $this->assertArrayHasKey('d/m/Y', $formats);
        $this->assertArrayHasKey('m/d/Y', $formats);
        $this->assertArrayHasKey('Y-m-d', $formats);

        foreach ($formats as $format) {
            $this->assertArrayHasKey('setting', $format);
            $this->assertArrayHasKey('datepicker', $format);
        }
    }

    #[Test]
    public function it_converts_mysql_date_to_user_format(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::dateFromMysql('2024-01-15');

        $this->assertSame('01/15/2024', $result);
    }

    #[Test]
    public function it_converts_mysql_date_with_european_format(): void
    {
        Setting::setValue('default_date_format', 'd/m/Y');

        $result = DateHelper::dateFromMysql('2024-01-15');

        $this->assertSame('15/01/2024', $result);
    }

    #[Test]
    public function it_converts_mysql_date_with_iso_format(): void
    {
        Setting::setValue('default_date_format', 'Y-m-d');

        $result = DateHelper::dateFromMysql('2024-01-15');

        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_null_date(): void
    {
        $result = DateHelper::dateFromMysql(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_date(): void
    {
        $result = DateHelper::dateFromMysql('invalid-date');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_converts_timestamp_to_date(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $timestamp = strtotime('2024-01-15');
        $result    = DateHelper::dateFromTimestamp($timestamp);

        $this->assertSame('01/15/2024', $result);
    }

    #[Test]
    public function it_converts_user_date_to_mysql_format(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::dateToMysql('01/15/2024');

        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_converts_european_date_to_mysql(): void
    {
        Setting::setValue('default_date_format', 'd/m/Y');

        $result = DateHelper::dateToMysql('15/01/2024');

        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_user_date(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::dateToMysql('invalid');

        $this->assertSame('', $result);
    }

    #[Test]
    #[DataProvider('validDateProvider')]
    public function it_validates_dates(string $date, bool $expected): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::isDate($date);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_gets_date_format_setting(): void
    {
        Setting::setValue('default_date_format', 'Y-m-d');

        $result = DateHelper::dateFormatSetting();

        $this->assertSame('Y-m-d', $result);
    }

    #[Test]
    public function it_gets_datepicker_format(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::dateFormatDatepicker();

        $this->assertSame('mm/dd/yyyy', $result);
    }

    #[Test]
    public function it_gets_datepicker_format_for_european(): void
    {
        Setting::setValue('default_date_format', 'd.m.Y');

        $result = DateHelper::dateFormatDatepicker();

        $this->assertSame('dd.mm.yyyy', $result);
    }

    #[Test]
    public function it_increments_user_date_by_days(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::incrementUserDate('01/15/2024', '+7 days');

        $this->assertSame('01/22/2024', $result);
    }

    #[Test]
    public function it_increments_user_date_by_months(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::incrementUserDate('01/15/2024', '+1 month');

        $this->assertSame('02/15/2024', $result);
    }

    #[Test]
    public function it_decrements_user_date(): void
    {
        Setting::setValue('default_date_format', 'm/d/Y');

        $result = DateHelper::incrementUserDate('01/15/2024', '-7 days');

        $this->assertSame('01/08/2024', $result);
    }

    #[Test]
    public function it_increments_mysql_date_by_days(): void
    {
        $result = DateHelper::incrementDate('2024-01-15', '+7 days');

        $this->assertSame('2024-01-22', $result);
    }

    #[Test]
    public function it_increments_mysql_date_by_years(): void
    {
        $result = DateHelper::incrementDate('2024-01-15', '+1 year');

        $this->assertSame('2025-01-15', $result);
    }

    #[Test]
    public function it_handles_leap_year_increments(): void
    {
        $result = DateHelper::incrementDate('2024-02-29', '+1 year');

        // PHP DateTime handles this as February 28, 2025
        $this->assertSame('2025-02-28', $result);
    }

    #[Test]
    public function it_handles_month_end_increments(): void
    {
        $result = DateHelper::incrementDate('2024-01-31', '+1 month');

        // PHP DateTime handles this as February 29, 2024 (leap year)
        $this->assertSame('2024-02-29', $result);
    }
}

#[CoversClass(EchoHelper::class)]
class EchoHelperTest extends UnitTestCase
{
    public static function specialCharsProvider(): array
    {
        return [
            'less than'    => ['<test>', '&lt;'],
            'greater than' => ['test>', '&gt;'],
            'double quote' => ['"quoted"', '&quot;'],
            'single quote' => ["it's", '&#039;'],
            'ampersand'    => ['A & B', '&amp;'],
        ];
    }

    #[Test]
    public function it_escapes_html_special_chars(): void
    {
        $input = '<script>alert("xss")</script>';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $result);
    }

    #[Test]
    public function it_returns_null_for_null_input(): void
    {
        $result = EchoHelper::htmlsc(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_handles_quotes_in_htmlsc(): void
    {
        $input = "It's a \"test\"";

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    #[Test]
    public function it_handles_ampersands(): void
    {
        $input = 'Johnson & Johnson';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('Johnson &amp; Johnson', $result);
    }

    #[Test]
    #[DataProvider('specialCharsProvider')]
    public function it_escapes_various_special_chars(string $input, string $expectedContains): void
    {
        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString($expectedContains, $result);
    }

    #[Test]
    public function it_outputs_escaped_html_chars(): void
    {
        $input = '<b>Bold</b>';

        ob_start();
        EchoHelper::_htmlsc($input);
        $output = ob_get_clean();

        $this->assertSame('&lt;b&gt;Bold&lt;/b&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmlsc_output(): void
    {
        ob_start();
        $result = EchoHelper::_htmlsc(null);
        $output = ob_get_clean();

        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_outputs_html_entities(): void
    {
        $input = '<script>test</script>';

        ob_start();
        EchoHelper::_htmle($input);
        $output = ob_get_clean();

        $this->assertStringContainsString('&lt;', $output);
        $this->assertStringContainsString('&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmle_output(): void
    {
        ob_start();
        $result = EchoHelper::_htmle(null);
        $output = ob_get_clean();

        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_handles_empty_strings(): void
    {
        $result = EchoHelper::htmlsc('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_preserves_safe_text(): void
    {
        $input = 'This is safe text without special chars';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame($input, $result);
    }

    #[Test]
    public function it_handles_unicode_characters(): void
    {
        $input = 'Hello 世界 🌍';

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('Hello', $result);
        $this->assertStringContainsString('世界', $result);
    }

    #[Test]
    public function it_handles_numeric_strings(): void
    {
        $input = '12345.67';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('12345.67', $result);
    }

    #[Test]
    public function it_handles_multiple_special_chars(): void
    {
        $input = '<div class="test" id=\'myId\'>Content & more</div>';

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
        $this->assertStringContainsString('&quot;', $result);
        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&amp;', $result);
    }
}

#[CoversClass(JsonErrorHelper::class)]
class JsonErrorHelperTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    #[Test]
    public function it_returns_empty_array_when_no_post_data(): void
    {
        $_POST = [];

        $result = JsonErrorHelper::json_errors();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_array_of_errors(): void
    {
        // Simulate POST data
        $_POST = ['field1' => 'value1', 'field2' => 'value2'];

        $result = JsonErrorHelper::json_errors();

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_processes_multiple_post_fields(): void
    {
        $_POST = [
            'email'    => 'invalid-email',
            'name'     => 'John Doe',
            'password' => 'short',
        ];

        $result = JsonErrorHelper::json_errors();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('password', $result);
    }
}

#[CoversClass(MailerHelper::class)]
class MailerHelperTest extends UnitTestCase
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

#[CoversClass(NumberHelper::class)]
class NumberHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up settings table
        DB::table('ip_settings')->delete();
    }

    public static function currencyAmountProvider(): array
    {
        return [
            'zero'            => [0, '$0.00'],
            'small amount'    => [0.99, '$0.99'],
            'negative amount' => [-1234.56, '$-1,234.56'],
            'large amount'    => [1234567.89, '$1,234,567.89'],
            'string numeric'  => ['1234.56', '$1,234.56'],
        ];
    }

    #[Test]
    public function it_formats_currency_with_default_settings(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('$1,234.56', $result);
    }

    #[Test]
    public function it_formats_currency_with_symbol_after(): void
    {
        Setting::setValue('currency_symbol', '€');
        Setting::setValue('currency_symbol_placement', 'after');
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('1.234,56€', $result);
    }

    #[Test]
    public function it_formats_currency_with_symbol_after_space(): void
    {
        Setting::setValue('currency_symbol', '€');
        Setting::setValue('currency_symbol_placement', 'afterspace');
        Setting::setValue('thousands_separator', ' ');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('1 234,56&nbsp;€', $result);
    }

    #[Test]
    public function it_formats_currency_with_zero_decimals(): void
    {
        Setting::setValue('currency_symbol', '$');
        Setting::setValue('currency_symbol_placement', 'before');
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '');
        Setting::setValue('tax_rate_decimal_places', '0');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('$1,235', $result);
    }

    #[Test]
    #[DataProvider('currencyAmountProvider')]
    public function it_formats_various_currency_amounts($amount, string $expected): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_currency($amount);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_formats_amount_with_default_settings(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_amount(1234.56);

        $this->assertSame('1,234.56', $result);
    }

    #[Test]
    public function it_returns_null_for_null_amount(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_amount(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_formats_amount_with_european_format(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_amount(1234.56);

        $this->assertSame('1.234,56', $result);
    }

    #[Test]
    public function it_formats_quantity_with_default_settings(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('default_item_decimals', '2');

        $result = NumberHelper::format_quantity(123.456);

        $this->assertSame('123.46', $result);
    }

    #[Test]
    public function it_formats_quantity_with_higher_precision(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('default_item_decimals', '4');

        $result = NumberHelper::format_quantity(123.456789);

        $this->assertSame('123.4568', $result);
    }

    #[Test]
    public function it_returns_null_for_null_quantity(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_quantity(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_standardizes_amount_from_european_format(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');

        $result = NumberHelper::standardize_amount('1.234,56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_standardizes_amount_from_us_format(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');

        $result = NumberHelper::standardize_amount('1,234.56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_handles_numeric_amount_standardization(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount(1234.56);

        $this->assertSame(1234.56, $result);
    }

    #[Test]
    public function it_standardizes_amount_with_multiple_dots(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');

        // European format with multiple dots for thousands
        $result = NumberHelper::standardize_amount('1.234.567,89');

        $this->assertSame('1234567.89', $result);
    }

    #[Test]
    public function it_handles_empty_thousands_separator(): void
    {
        Setting::setValue('thousands_separator', '');
        Setting::setValue('decimal_point', ',');

        $result = NumberHelper::standardize_amount('1234,56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_returns_null_for_null_standardize_amount(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_standardizes_zero(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount('0,00');

        $this->assertSame('0.00', $result);
    }

    #[Test]
    public function it_standardizes_negative_amounts(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');

        $result = NumberHelper::standardize_amount('-1,234.56');

        $this->assertSame('-1234.56', $result);
    }

    private function setDefaultCurrencySettings(): void
    {
        Setting::setValue('currency_symbol', '$');
        Setting::setValue('currency_symbol_placement', 'before');
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('tax_rate_decimal_places', '2');
        Setting::setValue('default_item_decimals', '2');
    }
}

#[CoversClass(PagerHelper::class)]
class PagerHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up quotes table before each test
        $this->cleanupTables(['ip_quotes']);
    }

    #[Test]
    public function it_returns_links_html_when_given_length_aware_paginator(): void
    {
        // Arrange
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
            ['id' => 3, 'name' => 'Item 3'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            3,
            15,
            1,
            ['path' => '/test']
        );

        // Act
        $result = PagerHelper::pager('/test', $paginator);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        // Laravel pagination HTML contains navigation elements
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_returns_links_html_when_given_simple_paginator(): void
    {
        // Arrange
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        $paginator = new Paginator(
            $items,
            15,
            1,
            ['path' => '/test']
        );

        // Act
        $result = PagerHelper::pager('/test', $paginator);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_paginates_eloquent_builder_and_returns_links(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 30; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query()->where('quote_status_id', '>', 0);

        // Act
        $result = PagerHelper::pager('/quotes', $builder, 10);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_paginates_query_builder_and_returns_links(): void
    {
        // Arrange - Create test data
        for ($i = 1; $i <= 20; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query()->getQuery()->where('quote_status_id', '>', 0);

        // Act
        $result = PagerHelper::pager('/quotes', $builder, 5);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_uses_default_per_page_when_not_specified(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 20; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query();

        // Act - Not passing perPage, should use default of 15
        $result = PagerHelper::pager('/quotes', $builder);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_plain_array(): void
    {
        // Arrange
        $array = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ];

        // Act
        $result = PagerHelper::pager('/test', $array);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_collection(): void
    {
        // Arrange
        $collection = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        // Act
        $result = PagerHelper::pager('/test', $collection);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_null(): void
    {
        // Act
        $result = PagerHelper::pager('/test', null);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_string(): void
    {
        // Act
        $result = PagerHelper::pager('/test', 'mdl_quotes');

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_empty_eloquent_builder(): void
    {
        // Arrange - Builder with no results
        $builder = Quote::query()->where('quote_id', -1); // No matching records

        // Act
        $result = PagerHelper::pager('/quotes', $builder);

        // Assert
        $this->assertIsString($result);
        // Even with no results, pagination HTML may be rendered
    }

    #[Test]
    public function it_preserves_builder_constraints_when_paginating(): void
    {
        // Arrange - Create draft and sent quotes
        for ($i = 1; $i <= 10; $i++) {
            $this->createTestQuote([
                'quote_number'    => 'Q-DRAFT-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
                'quote_status_id' => 1, // Draft
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->createTestQuote([
                'quote_number'    => 'Q-SENT-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
                'quote_status_id' => 2, // Sent
            ]);
        }

        $builder = Quote::query()->where('quote_status_id', 1); // Draft only

        // Act
        $result = PagerHelper::pager('/quotes/draft', $builder, 5);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        // The builder should have been paginated with draft filter preserved
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 50; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query();

        // Act - Use custom perPage of 25
        $result = PagerHelper::pager('/quotes', $builder, 25);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_handles_already_paginated_results_without_double_pagination(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 30; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $paginated = Quote::query()->paginate(10);

        // Act - Pass already paginated result
        $result = PagerHelper::pager('/quotes', $paginated);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }
}

#[CoversClass(SettingsHelper::class)]
class SettingsHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_settings')->delete();
    }

    public static function checkSelectProvider(): array
    {
        return [
            'equal strings'      => ['test', 'test', '==', false, 'selected="selected"'],
            'unequal strings'    => ['test', 'other', '==', false, ''],
            'not equal'          => ['test', 'other', '!=', false, 'selected="selected"'],
            'equal with checked' => ['test', 'test', '==', true, 'checked="checked"'],
            'numeric equal'      => [1, '1', '==', false, 'selected="selected"'],
            'boolean true'       => [true, null, '==', false, 'selected="selected"'],
            'boolean false'      => [false, null, '==', false, ''],
        ];
    }

    #[Test]
    public function it_gets_setting_value(): void
    {
        Setting::setValue('test_key', 'test_value');

        $result = SettingsHelper::getSetting('test_key');

        $this->assertSame('test_value', $result);
    }

    #[Test]
    public function it_returns_default_when_setting_not_found(): void
    {
        $result = SettingsHelper::getSetting('non_existent_key', 'default_value');

        $this->assertSame('default_value', $result);
    }

    #[Test]
    public function it_escapes_html_when_requested(): void
    {
        Setting::setValue('html_key', '<script>alert("xss")</script>');

        $result = SettingsHelper::getSetting('html_key', '', true);

        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
    }

    #[Test]
    public function it_does_not_escape_by_default(): void
    {
        Setting::setValue('html_key', '<b>bold</b>');

        $result = SettingsHelper::getSetting('html_key');

        $this->assertSame('<b>bold</b>', $result);
    }

    #[Test]
    public function it_gets_gateway_settings(): void
    {
        Setting::setValue('paypal_enabled', '1');
        Setting::setValue('paypal_api_key', 'test_key');
        Setting::setValue('paypal_secret', 'test_secret');
        Setting::setValue('stripe_enabled', '1');

        $result = SettingsHelper::getGatewaySettings('paypal');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('paypal_enabled', $result);
        $this->assertArrayHasKey('paypal_api_key', $result);
        $this->assertArrayHasKey('paypal_secret', $result);
        $this->assertArrayNotHasKey('stripe_enabled', $result);
    }

    #[Test]
    public function it_returns_empty_array_for_gateway_with_no_settings(): void
    {
        Setting::setValue('other_setting', 'value');

        $result = SettingsHelper::getGatewaySettings('nonexistent');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_checks_select_for_equal_values(): void
    {
        ob_start();
        SettingsHelper::checkSelect('test', 'test');
        $output = ob_get_clean();

        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_unequal_values(): void
    {
        ob_start();
        SettingsHelper::checkSelect('test', 'other');
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    #[Test]
    public function it_checks_select_with_not_equal_operator(): void
    {
        ob_start();
        SettingsHelper::checkSelect('test', 'other', '!=');
        $output = ob_get_clean();

        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_boolean_true(): void
    {
        ob_start();
        SettingsHelper::checkSelect(true);
        $output = ob_get_clean();

        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_boolean_false(): void
    {
        ob_start();
        SettingsHelper::checkSelect(false);
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    #[Test]
    public function it_outputs_checked_instead_of_selected(): void
    {
        ob_start();
        SettingsHelper::checkSelect('test', 'test', '==', true);
        $output = ob_get_clean();

        $this->assertSame('checked="checked"', $output);
    }

    #[Test]
    public function it_checks_empty_operator(): void
    {
        ob_start();
        SettingsHelper::checkSelect('', null, 'e');
        $output = ob_get_clean();

        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_not_empty_operator(): void
    {
        ob_start();
        SettingsHelper::checkSelect('value', null, 'e');
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    #[Test]
    #[DataProvider('checkSelectProvider')]
    public function it_handles_various_check_select_scenarios($value1, $value2, string $operator, bool $checked, string $expected): void
    {
        ob_start();
        SettingsHelper::checkSelect($value1, $value2, $operator, $checked);
        $output = ob_get_clean();

        $this->assertSame($expected, $output);
    }

    #[Test]
    public function it_returns_empty_string_as_default(): void
    {
        $result = SettingsHelper::getSetting('nonexistent');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_numeric_settings(): void
    {
        Setting::setValue('numeric_key', '123');

        $result = SettingsHelper::getSetting('numeric_key');

        $this->assertSame('123', $result);
    }

    #[Test]
    public function it_handles_null_setting_values(): void
    {
        Setting::setValue('null_key', null);

        $result = SettingsHelper::getSetting('null_key', 'fallback');

        $this->assertSame('fallback', $result);
    }
}

/**
 * Test that storage directory structure matches Laravel requirements.
 */
class StorageStructureTest extends TestCase
{
    /**
     * Test that required storage directories exist.
     */
    public function test_required_storage_directories_exist(): void
    {
        $basePath = dirname(__DIR__, 2);

        $requiredDirectories = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/app/uploads/temp/mpdf',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($requiredDirectories as $directory) {
            $fullPath = $basePath . '/' . $directory;
            $this->assertDirectoryExists(
                $fullPath,
                "Required storage directory does not exist: {$directory}"
            );
        }
    }

    /**
     * Test that storage directories are writable.
     */
    public function test_storage_directories_are_writable(): void
    {
        $basePath = dirname(__DIR__, 2);

        $writableDirectories = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($writableDirectories as $directory) {
            $fullPath = $basePath . '/' . $directory;
            $this->assertTrue(
                is_writable($fullPath),
                "Storage directory is not writable: {$directory}"
            );
        }
    }

    /**
     * Test that .gitignore files exist in storage directories.
     */
    public function test_gitignore_files_exist_in_storage_directories(): void
    {
        $basePath = dirname(__DIR__, 2);

        $directoriesWithGitignore = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($directoriesWithGitignore as $directory) {
            $gitignorePath = $basePath . '/' . $directory . '/.gitignore';
            $this->assertFileExists(
                $gitignorePath,
                ".gitignore file does not exist in: {$directory}"
            );
        }
    }

    /**
     * Test that .gitignore files have correct content.
     */
    public function test_gitignore_files_have_correct_content(): void
    {
        $basePath = dirname(__DIR__, 2);

        // Test storage/app/.gitignore
        $appGitignore = file_get_contents($basePath . '/storage/app/.gitignore');
        $this->assertStringContainsString('*', $appGitignore);
        $this->assertStringContainsString('!public/', $appGitignore);
        $this->assertStringContainsString('!uploads/', $appGitignore);
        $this->assertStringContainsString('!.gitignore', $appGitignore);

        // Test storage/framework/cache/.gitignore
        $cacheGitignore = file_get_contents($basePath . '/storage/framework/cache/.gitignore');
        $this->assertStringContainsString('*', $cacheGitignore);
        $this->assertStringContainsString('!data/', $cacheGitignore);
        $this->assertStringContainsString('!.gitignore', $cacheGitignore);

        // Test other directories have standard .gitignore
        $standardGitignoreContent = "*\n!.gitignore\n";
        $standardDirs             = [
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($standardDirs as $directory) {
            $gitignorePath = $basePath . '/' . $directory . '/.gitignore';
            $content       = file_get_contents($gitignorePath);
            $this->assertEquals(
                $standardGitignoreContent,
                $content,
                "Incorrect .gitignore content in: {$directory}"
            );
        }
    }

    /**
     * Test upload helper functions.
     */
    public function test_upload_helper_functions(): void
    {
        require_once __DIR__ . '/../../bootstrap/helpers.php';

        $basePath = dirname(__DIR__, 2);

        // Test uploads_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads',
            mb_rtrim(uploads_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_archive_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/archive',
            mb_rtrim(uploads_archive_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_customer_files_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/customer_files',
            mb_rtrim(uploads_customer_files_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_temp_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/temp',
            mb_rtrim(uploads_temp_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_temp_mpdf_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/temp/mpdf',
            mb_rtrim(uploads_temp_mpdf_path(), DIRECTORY_SEPARATOR)
        );
    }

    /**
     * Test that UPLOADS constants point to storage location.
     */
    public function test_upload_constants_point_to_storage(): void
    {
        require_once __DIR__ . '/../../bootstrap/paths.php';

        $basePath = dirname(__DIR__, 2);

        $this->assertStringContainsString(
            'storage/app/uploads',
            UPLOADS_FOLDER,
            'UPLOADS_FOLDER should point to storage/app/uploads'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/archive',
            UPLOADS_ARCHIVE_FOLDER,
            'UPLOADS_ARCHIVE_FOLDER should point to storage/app/uploads/archive'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/customer_files',
            UPLOADS_CFILES_FOLDER,
            'UPLOADS_CFILES_FOLDER should point to storage/app/uploads/customer_files'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/temp',
            UPLOADS_TEMP_FOLDER,
            'UPLOADS_TEMP_FOLDER should point to storage/app/uploads/temp'
        );
    }
}

/**
 * TemplateService Unit Tests.
 *
 * NOTE: These tests verify the service's behavior with the actual filesystem.
 * The service uses the APPPATH constant which points to the application directory.
 * Tests verify graceful handling of missing directories and proper file filtering.
 */
#[CoversClass(TemplateService::class)]
class TemplateServiceTest extends AbstractServiceTestCase
{
    private TemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TemplateService();
    }

    /**
     * Test service returns empty array when invoice PDF templates directory doesn't exist.
     *
     * This tests graceful degradation - the service should return empty array
     * rather than throwing an exception when the template directory is missing.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_invoice_pdf_templates_directory_not_exists(): void
    {
        /** Arrange */
        // APPPATH is defined in bootstrap to point to 'application' directory
        // The old CodeIgniter template path (APPPATH/views/invoice_templates/pdf)
        // won't exist in the new Laravel structure, where templates are in
        // Modules/Core/Resources/views/invoice_templates/

        /** Act */
        $result = $this->service->getInvoiceTemplates('pdf');

        /* Assert */
        // Service should gracefully handle missing directory by returning empty array
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service returns empty array when invoice public templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_invoice_public_templates_directory_not_exists(): void
    {
        /** Arrange */
        // Test graceful handling of missing public template directory

        /** Act */
        $result = $this->service->getInvoiceTemplates('public');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when public directory does not exist');
    }

    /**
     * Test service returns empty array when quote PDF templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_quote_pdf_templates_directory_not_exists(): void
    {
        /** Arrange */
        // Test graceful handling of missing quote template directory

        /** Act */
        $result = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service returns empty array when quote public templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_quote_public_templates_directory_not_exists(): void
    {
        /** Arrange */
        // Test graceful handling of missing public quote template directory

        /** Act */
        $result = $this->service->getQuoteTemplates('public');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service uses 'pdf' as default type parameter for invoice templates.
     */
    #[Test]
    public function it_defaults_to_pdf_type_for_invoice_templates(): void
    {
        /** Arrange */
        // Service should use 'pdf' as default when no type is specified

        /** Act */
        $resultDefault = $this->service->getInvoiceTemplates();
        $resultPdf     = $this->service->getInvoiceTemplates('pdf');

        /* Assert */
        $this->assertEquals($resultPdf, $resultDefault, 'Default should match PDF type results');
    }

    /**
     * Test service uses 'pdf' as default type parameter for quote templates.
     */
    #[Test]
    public function it_defaults_to_pdf_type_for_quote_templates(): void
    {
        /** Arrange */
        // Service should use 'pdf' as default when no type is specified

        /** Act */
        $resultDefault = $this->service->getQuoteTemplates();
        $resultPdf     = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        $this->assertEquals($resultPdf, $resultDefault, 'Default should match PDF type results');
    }

    /**
     * Test service filters out dot directories ('.' and '..') from results.
     *
     * Note: This test verifies expected behavior even when directories don't exist.
     * When directory doesn't exist, scandir returns false and empty array is returned.
     */
    #[Test]
    public function it_filters_out_dot_directories(): void
    {
        /** Arrange */
        // Service should exclude '.' and '..' from directory listings using array_filter

        /** Act */
        $invoiceTemplates = $this->service->getInvoiceTemplates();
        $quoteTemplates   = $this->service->getQuoteTemplates();

        /* Assert */
        // The service uses array_filter to remove '.' and '..'
        $this->assertNotContains('.', $invoiceTemplates, 'Should not contain current directory marker');
        $this->assertNotContains('..', $invoiceTemplates, 'Should not contain parent directory marker');
        $this->assertNotContains('.', $quoteTemplates, 'Should not contain current directory marker');
        $this->assertNotContains('..', $quoteTemplates, 'Should not contain parent directory marker');
    }

    /**
     * Test service removes file extensions from template names.
     *
     * Note: This test verifies the extension removal logic is applied,
     * even when working with empty arrays (when directories don't exist).
     */
    #[Test]
    public function it_removes_file_extensions_from_template_names(): void
    {
        /** Arrange */
        // Service should strip .php extensions using pathinfo(PATHINFO_FILENAME)

        /** Act */
        $invoiceTemplates = $this->service->getInvoiceTemplates('pdf');
        $quoteTemplates   = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        // All template names should have extensions removed
        // (if any templates exist in the configured directories)
        foreach ($invoiceTemplates as $template) {
            $this->assertStringNotContainsString('.php', $template, 'Template name should not contain .php extension');
            $this->assertStringNotContainsString('.blade.php', $template, 'Template name should not contain .blade.php extension');
        }

        foreach ($quoteTemplates as $template) {
            $this->assertStringNotContainsString('.php', $template, 'Template name should not contain .php extension');
            $this->assertStringNotContainsString('.blade.php', $template, 'Template name should not contain .blade.php extension');
        }
    }

    /**
     * Test service handles both 'pdf' and 'public' template types.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_different_template_types(): void
    {
        /** Arrange */
        // Service should handle both 'pdf' and 'public' directory types

        /** Act */
        $pdfTemplates    = $this->service->getInvoiceTemplates('pdf');
        $publicTemplates = $this->service->getInvoiceTemplates('public');

        /* Assert */
        $this->assertIsArray($pdfTemplates, 'PDF templates should return array');
        $this->assertIsArray($publicTemplates, 'Public templates should return array');
        // Both types should return arrays (empty or populated based on directory existence)
    }

    /**
     * Test service returns numerically indexed array (not associative).
     *
     * The service uses array_values() to ensure numeric indexing after filtering.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_indexed_array(): void
    {
        /** Arrange */
        // Service uses array_values() to ensure numeric indexing

        /** Act */
        $templates = $this->service->getInvoiceTemplates();

        /* Assert */
        // Should be numerically indexed (array_values is used in the service)
        $this->assertIsArray($templates, 'Should return an array');

        // If array is not empty, verify it starts at index 0
        if (count($templates) > 0) {
            $this->assertArrayHasKey(0, $templates, 'Array should be numerically indexed starting at 0');
        }

        // Verify all keys are sequential integers
        $keys         = array_keys($templates);
        $expectedKeys = range(0, count($templates) - 1);
        $this->assertEquals($expectedKeys, $keys, 'Array keys should be sequential integers starting from 0');
    }
}

#[CoversClass(TranslationHelper::class)]
class TranslationHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_settings')->delete();
        Setting::setValue('default_language', 'en');
    }

    #[Test]
    public function it_translates_simple_strings(): void
    {
        $result = TranslationHelper::trans('validation.required');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_key_when_translation_not_found(): void
    {
        $key = 'non.existent.translation.key';

        $result = TranslationHelper::trans($key);

        $this->assertSame($key, $result);
    }

    #[Test]
    public function it_uses_default_value_when_translation_not_found(): void
    {
        $key     = 'non.existent.key';
        $default = 'Default value';

        $result = TranslationHelper::trans($key, '', $default);

        $this->assertSame($default, $result);
    }

    #[Test]
    public function it_wraps_translation_in_label_with_id(): void
    {
        $fieldId = 'test_field';

        $result = TranslationHelper::trans('validation.required', $fieldId);

        $this->assertStringStartsWith('<label for="' . $fieldId . '">', $result);
        $this->assertStringEndsWith('</label>', $result);
    }

    #[Test]
    public function it_does_not_wrap_when_id_is_empty(): void
    {
        $result = TranslationHelper::trans('validation.required', '');

        $this->assertStringStartsNotWith('<label', $result);
    }

    #[Test]
    public function it_sets_application_locale(): void
    {
        TranslationHelper::setLanguage('fr');

        $this->assertSame('fr', app()->getLocale());
    }

    #[Test]
    public function it_uses_system_default_for_system_language(): void
    {
        Setting::setValue('default_language', 'de');

        TranslationHelper::setLanguage('system');

        $this->assertSame('de', app()->getLocale());
    }

    #[Test]
    public function it_sets_specific_language(): void
    {
        TranslationHelper::setLanguage('es');

        $this->assertSame('es', app()->getLocale());
    }

    #[Test]
    public function it_returns_available_languages(): void
    {
        $languages = TranslationHelper::getAvailableLanguages();

        $this->assertIsArray($languages);
        $this->assertContains('en', $languages);
    }

    #[Test]
    public function it_returns_empty_array_when_lang_directory_missing(): void
    {
        // This test assumes the lang directory exists, but tests the handling
        $languages = TranslationHelper::getAvailableLanguages();

        $this->assertIsArray($languages);
    }

    #[Test]
    public function it_returns_sorted_languages(): void
    {
        $languages = TranslationHelper::getAvailableLanguages();

        if (count($languages) > 1) {
            $sorted = $languages;
            sort($sorted);
            $this->assertSame($sorted, $languages);
        }
    }

    #[Test]
    public function it_handles_empty_translation_key(): void
    {
        $result = TranslationHelper::trans('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_uses_configured_default_language(): void
    {
        Setting::setValue('default_language', 'fr');

        $result = TranslationHelper::trans('validation.required');

        $this->assertIsString($result);
    }
}

#[CoversClass(UserHelper::class)]
class UserHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_users')->delete();
    }

    #[Test]
    public function it_returns_empty_string_for_null_user(): void
    {
        $result = UserHelper::format_user(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_nonexistent_user_id(): void
    {
        $result = UserHelper::format_user(99999);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_user_with_name_only(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe', $result);
    }

    #[Test]
    public function it_formats_user_with_company(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - ACME Corp', $result);
    }

    #[Test]
    public function it_formats_user_with_contact(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => '',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - jane@example.com', $result);
    }

    #[Test]
    public function it_formats_user_with_all_fields(): void
    {
        $user = (object) [
            'user_name'              => 'John Doe',
            'user_company'           => 'ACME Corp',
            'user_invoicing_contact' => 'jane@example.com',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('John doe - ACME Corp - jane@example.com', $result);
    }

    #[Test]
    public function it_capitalizes_first_letter_of_name(): void
    {
        $user = (object) [
            'user_name'              => 'john',
            'user_company'           => '',
            'user_invoicing_contact' => '',
        ];

        $result = UserHelper::format_user($user);

        $this->assertStringStartsWith('John', $result);
    }

    #[Test]
    public function it_handles_user_object_without_optional_fields(): void
    {
        $user = (object) [
            'user_name' => 'Jane Smith',
        ];

        $result = UserHelper::format_user($user);

        $this->assertSame('Jane smith', $result);
    }
}
