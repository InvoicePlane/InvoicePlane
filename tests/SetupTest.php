<?php

/**
 * InvoicePlane
 *
 * @package      InvoicePlane
 * @author       InvoicePlane Developers & Contributors
 * @copyright    Copyright (c) 2012 - 2018, InvoicePlane (https://invoiceplane.com/)
 * @license      http://opensource.org/licenses/MIT     MIT License
 * @link         https://invoiceplane.com
 */

/**
 * Class SetupTest
 *
 * Checks if the testing environment is set up correctly
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
