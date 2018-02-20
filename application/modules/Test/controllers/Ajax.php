<?php

/**
 * Class Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Ajax extends Permission_Controller
{

    public $ajax_controller = true;

    public function index()
    {
        die('This is an ajax controller');
    }
}
