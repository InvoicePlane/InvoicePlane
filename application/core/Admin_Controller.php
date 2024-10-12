<?php
if (!defined('BASEPATH')) {
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

class Admin_Controller extends User_Controller
{
    public function __construct()
    {
        parent::__construct('user_type', 1);
    }

    protected function filter_input(): void
    {
        $input = $this->input->post();

        array_walk($input, function (&$value, $key): void {
            $value = $this->security->xss_clean($value);
            $value = strip_tags($value);
            $value = html_escape($value);   // <<<=== that's a CodeIgniter helper
        });
    }
}
