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
class Ajax extends Permission_Controller
{

    public $ajax_controller = true;

    public function index()
    {
        echo 'This is an ajax controller';
    }
}
