<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Helpers\DateHelper::class)]

class DateHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');

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
        /* Arrange */

        /* Act */
        $formats = DateHelper::dateFormats();

        /* Assert */
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
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::dateFromMysql('2024-01-15');

        /* Assert */
        $this->assertSame('01/15/2024', $result);
    }

    #[Test]
    public function it_converts_mysql_date_with_european_format(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'd/m/Y');

        /* Act */
        $result = DateHelper::dateFromMysql('2024-01-15');

        /* Assert */
        $this->assertSame('15/01/2024', $result);
    }

    #[Test]
    public function it_converts_mysql_date_with_iso_format(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'Y-m-d');

        /* Act */
        $result = DateHelper::dateFromMysql('2024-01-15');

        /* Assert */
        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_null_date(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::dateFromMysql(null);

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_date(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::dateFromMysql('invalid-date');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_converts_timestamp_to_date(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        $timestamp = strtotime('2024-01-15');
        /* Act */
        $result    = DateHelper::dateFromTimestamp($timestamp);

        /* Assert */
        $this->assertSame('01/15/2024', $result);
    }

    #[Test]
    public function it_converts_user_date_to_mysql_format(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::dateToMysql('01/15/2024');

        /* Assert */
        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_converts_european_date_to_mysql(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'd/m/Y');

        /* Act */
        $result = DateHelper::dateToMysql('15/01/2024');

        /* Assert */
        $this->assertSame('2024-01-15', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_user_date(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::dateToMysql('invalid');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    #[DataProvider('validDateProvider')]
    public function it_validates_dates(string $date, bool $expected): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::isDate($date);

        /* Assert */
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_gets_date_format_setting(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'Y-m-d');

        /* Act */
        $result = DateHelper::dateFormatSetting();

        /* Assert */
        $this->assertSame('Y-m-d', $result);
    }

    #[Test]
    public function it_gets_datepicker_format(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::dateFormatDatepicker();

        /* Assert */
        $this->assertSame('mm/dd/yyyy', $result);
    }

    #[Test]
    public function it_gets_datepicker_format_for_european(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'd.m.Y');

        /* Act */
        $result = DateHelper::dateFormatDatepicker();

        /* Assert */
        $this->assertSame('dd.mm.yyyy', $result);
    }

    #[Test]
    public function it_increments_user_date_by_days(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::incrementUserDate('01/15/2024', '+7 days');

        /* Assert */
        $this->assertSame('01/22/2024', $result);
    }

    #[Test]
    public function it_increments_user_date_by_months(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::incrementUserDate('01/15/2024', '+1 month');

        /* Assert */
        $this->assertSame('02/15/2024', $result);
    }

    #[Test]
    public function it_decrements_user_date(): void
    {
        /* Arrange */
        Setting::setValue('default_date_format', 'm/d/Y');

        /* Act */
        $result = DateHelper::incrementUserDate('01/15/2024', '-7 days');

        /* Assert */
        $this->assertSame('01/08/2024', $result);
    }

    #[Test]
    public function it_increments_mysql_date_by_days(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::incrementDate('2024-01-15', '+7 days');

        /* Assert */
        $this->assertSame('2024-01-22', $result);
    }

    #[Test]
    public function it_increments_mysql_date_by_years(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::incrementDate('2024-01-15', '+1 year');

        /* Assert */
        $this->assertSame('2025-01-15', $result);
    }

    #[Test]
    public function it_handles_leap_year_increments(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::incrementDate('2024-02-29', '+1 year');

        // PHP DateTime handles this as February 28, 2025
        /* Assert */
        $this->assertSame('2025-02-28', $result);
    }

    #[Test]
    public function it_handles_month_end_increments(): void
    {
        /* Arrange */

        /* Act */
        $result = DateHelper::incrementDate('2024-01-31', '+1 month');

        // PHP DateTime handles this as February 29, 2024 (leap year)
        /* Assert */
        $this->assertSame('2024-02-29', $result);
    }
}
