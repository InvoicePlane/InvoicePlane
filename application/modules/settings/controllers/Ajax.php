<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     *
     */
    public function get_cron_key()
    {
        $this->load->helper('string');
        echo random_string('alnum', 16);
    }

    public function test_mail()
    {
        $this->load->helper('mailer');
        email_invoice(1, 'InvoicePlane', 'denys@denv.it', 'denys@denv.it', 'Test', 'Some text');
    }

}
