<?php

/**
 * Class Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Access extends Base_Controller
{
    public function index()
    {
        $this->load->model('mdl_test');

        die($this->mdl_test->check());
    }
}
