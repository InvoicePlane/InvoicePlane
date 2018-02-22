<?php

/**
 * Class Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Test extends Permission_Controller
{

    public $module_permission = 'module_permission';

    public function index()
    {
        $this->permission('method_permission');

        die('Test successfully loaded.');
    }
}
