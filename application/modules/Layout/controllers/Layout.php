<?php

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Layout
 *
 * @property string $layout      Sets the basic layout that should be loaded for the module view
 * @property array  $layout_data Contains all extended sections of the layout
 */
class Layout extends MX_Controller
{

    /** @var string */
    public $layout = 'base';

    /** @var array */
    public $layout_data = [];

    /**
     * @param $new_layout
     */
    public function setLayout($new_layout)
    {
        $this->layout = $new_layout;
    }

    /**
     * @param string $section Part of the layout that should be filled
     * @param string $view    The module view that should be used to render the output
     * @param array  $data    Optional data to pass to the view
     */
    public function extend($section, $view, $data = [])
    {
        $this->layout_data[$section] = $this->load->view($view, $data, true);
    }

    /**
     * @param       $view
     * @param array $data
     */
    public function render($view, $data = [])
    {
        self::extend('content', $view, $data);

        $this->load->view('layout/' . $this->layout, $this->layout_data);
    }
}
