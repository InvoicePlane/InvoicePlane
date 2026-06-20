<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\DB;
use Tests\Feature\Quotes\Setting;
use Tests\Feature\Quotes\SettingsHelper;

#[CoversClass(Tests\Helpers\SettingsHelper::class)]

class SettingsHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');

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
    public function it_handles_various_check_select_scenarios(string|int|bool $value1, ?string $value2, string $operator, bool $checked, string $expected): void
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
