<?php

/**
 * Class Test
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class Test extends Permission_Controller
{

    public $module_permission = 'module_permission';

    public function index()
    {
        $this->permission('method_permission');

        echo 'Test successfully loaded.';
    }
}
