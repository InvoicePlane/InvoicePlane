<?php

/**
 * InvoicePlane Tests
 *
 * Run:
 * - in project root directory:
 * phpunit application/tests/NumberHelperTest.php
 * phpunit -v
 */
class NumberHelperTest extends \PHPUnit_Framework_TestCase
{
    public $CI;
    public $Mdl_Settings;

    // pZ: must be static
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
        // Create a map of arguments to return values.
        $map = array(
            array('currency_symbol', 'EURO'), // euro for example multiple chars
            array('currency_symbol_placement', 'afterspace'),
            array('thousands_separator', ' '),
            array('decimal_point', ','),
        );

        $this->Mdl_Settings = $this->getMock('Mdl_Settings');
        $this->Mdl_Settings
            ->expects($this->any())
            ->method('setting')
            ->will($this->returnValueMap($map));

        $this->CI = $this->getMock('CI');
        $this->CI->mdl_settings = $this->Mdl_Settings;
    }

    protected function tearDown()
    {
    }

    // pZ: must be static
    public static function tearDownAfterClass()
    {
    }

    public function testFormatCurrency()
    {
//        $this->markTestIncomplete('Test Incomplete.');
//        $this->markTestSkipped('Test Skipped');
//        $this->fail('Test fail');
        $this->assertEquals(format_currency(123.45), '123,45&nbsp;EURO', 'pos: 1');
        $this->assertEquals(format_currency(123), '123,00&nbsp;EURO', 'pos: 2');
        $this->assertEquals(format_currency(5678), '5&nbsp;678,00&nbsp;EURO', 'pos: 3');
        $this->assertEquals(format_currency(-123.1), '-123,10&nbsp;EURO', 'pos: 4');
        $this->assertEquals(format_currency(-5678.1), '-5&nbsp;678,10&nbsp;EURO', 'pos: 5');
    }

    public function testFormatAmount()
    {
        $this->assertEquals(format_amount(123.45), '123,45', 'pos: 1');
        $this->assertEquals(format_amount(123), '123,00', 'pos: 2');
        $this->assertEquals(format_amount(5678), '5&nbsp;678,00', 'pos: 3');
        $this->assertEquals(format_amount(-123.1), '-123,10', 'pos: 4');
        $this->assertEquals(format_amount('3456.1'), '3&nbsp;456,10', 'pos: 5');
    }

    public function testStandardizeAmount()
    {
        $this->assertEquals(standardize_amount(123.45), '123.45', 'pos: 1');
        $this->assertEquals(standardize_amount('1&nbsp;234,02'), '1234.02', 'pos: 2');
        $this->assertEquals(standardize_amount('3&nbsp;456,10'), '3456.10', 'pos: 3');
        $this->assertEquals(standardize_amount('3456.1'), '3456.1', 'pos: 4');
        $this->assertEquals(standardize_amount('3456,01 EURO'), '3456.01 EURO', 'pos: 5');
    }

    public function testFormatThousandsSeparator()
    {
        $this->assertEquals(format_thousands_separator('.'), '.', 'The separator is not the same');
        $this->assertEquals(format_thousands_separator(','), ',', 'The separator is not the same');
        $this->assertEquals(format_thousands_separator('  '), '&nbsp;',
            'The space (spaces) separator is not the "&nbsp;"');
    }

}
