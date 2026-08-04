<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function get_cron_key()
    {
        $this->load->helper('ip_security');
        // 8 bytes -> 16 hexadecimal characters.
        echo generate_secure_token(8);
    }
}
