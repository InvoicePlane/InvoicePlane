<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * InvoicePlane
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */

/**
 * Class Test
 */
class Test extends Permission_Controller
{

    public $controller_permission = 'controller_permission';

    public function index()
    {
        $this->permission('method_permission');

        die('Test successfully loaded.');
    }
}
