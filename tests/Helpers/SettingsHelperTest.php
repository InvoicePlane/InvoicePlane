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
        /* Arrange */
        Setting::setValue('test_key', 'test_value');

        /* Act */
        $result = SettingsHelper::getSetting('test_key');

        /* Assert */
        $this->assertSame('test_value', $result);
    }

    #[Test]
    public function it_returns_default_when_setting_not_found(): void
    {
        /* Arrange */

        /* Act */
        $result = SettingsHelper::getSetting('non_existent_key', 'default_value');

        /* Assert */
        $this->assertSame('default_value', $result);
    }

    #[Test]
    public function it_escapes_html_when_requested(): void
    {
        /* Arrange */
        Setting::setValue('html_key', '<script>alert("xss")</script>');

        /* Act */
        $result = SettingsHelper::getSetting('html_key', '', true);

        /* Assert */
        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
    }

    #[Test]
    public function it_does_not_escape_by_default(): void
    {
        /* Arrange */
        Setting::setValue('html_key', '<b>bold</b>');

        /* Act */
        $result = SettingsHelper::getSetting('html_key');

        /* Assert */
        $this->assertSame('<b>bold</b>', $result);
    }

    #[Test]
    public function it_gets_gateway_settings(): void
    {
        /* Arrange */
        Setting::setValue('paypal_enabled', '1');
        Setting::setValue('paypal_api_key', 'test_key');
        Setting::setValue('paypal_secret', 'test_secret');
        Setting::setValue('stripe_enabled', '1');

        /* Act */
        $result = SettingsHelper::getGatewaySettings('paypal');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertArrayHasKey('paypal_enabled', $result);
        $this->assertArrayHasKey('paypal_api_key', $result);
        $this->assertArrayHasKey('paypal_secret', $result);
        $this->assertArrayNotHasKey('stripe_enabled', $result);
    }

    #[Test]
    public function it_returns_empty_array_for_gateway_with_no_settings(): void
    {
        /* Arrange */
        Setting::setValue('other_setting', 'value');

        /* Act */
        $result = SettingsHelper::getGatewaySettings('nonexistent');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_checks_select_for_equal_values(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('test', 'test');
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_unequal_values(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('test', 'other');
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_checks_select_with_not_equal_operator(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('test', 'other', '!=');
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_boolean_true(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect(true);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_select_for_boolean_false(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect(false);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_outputs_checked_instead_of_selected(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('test', 'test', '==', true);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('checked="checked"', $output);
    }

    #[Test]
    public function it_checks_empty_operator(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('', null, 'e');
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('selected="selected"', $output);
    }

    #[Test]
    public function it_checks_not_empty_operator(): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect('value', null, 'e');
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('', $output);
    }

    #[Test]
    #[DataProvider('checkSelectProvider')]
    public function it_handles_various_check_select_scenarios(string|int|bool $value1, ?string $value2, string $operator, bool $checked, string $expected): void
    {
        /* Arrange */
        ob_start();
        SettingsHelper::checkSelect($value1, $value2, $operator, $checked);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame($expected, $output);
    }

    #[Test]
    public function it_returns_empty_string_as_default(): void
    {
        /* Arrange */

        /* Act */
        $result = SettingsHelper::getSetting('nonexistent');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_numeric_settings(): void
    {
        /* Arrange */
        Setting::setValue('numeric_key', '123');

        /* Act */
        $result = SettingsHelper::getSetting('numeric_key');

        /* Assert */
        $this->assertSame('123', $result);
    }

    #[Test]
    public function it_handles_null_setting_values(): void
    {
        /* Arrange */
        Setting::setValue('null_key', null);

        /* Act */
        $result = SettingsHelper::getSetting('null_key', 'fallback');

        /* Assert */
        $this->assertSame('fallback', $result);
    }
}
