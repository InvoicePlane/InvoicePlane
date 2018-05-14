<?php

/**
 * Class SetupTest
 * Checks if the testing environment is set up correctly
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class SetupTest extends CI_TestCase
{
    public function test_bootstrap_constants()
    {
        $this->assertTrue(defined('PROJECT_BASE'));
        $this->assertTrue(defined('BASEPATH'));
        $this->assertTrue(defined('APPPATH'));
        $this->assertTrue(defined('VIEWPATH'));
        $this->assertTrue(defined('IP_ENV'));
    }
}
