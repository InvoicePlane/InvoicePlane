<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Layout extends MX_Controller
{
    public $view_data = array();

    public function buffer()
    {
        $args = func_get_args();

        if (count($args) == 1) {
            foreach ($args[0] as $arg) {
                $key = $arg[0];
                $view = explode('/', $arg[1]);
                $data = array_merge(isset($arg[2]) ? $arg[2] : array(), $this->view_data);

                $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
            }
        } else {
            $key = $args[0];
            $view = explode('/', $args[1]);
            $data = array_merge(isset($args[2]) ? $args[2] : array(), $this->view_data);

            $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
        }
        return $this;
    }

    public function set()
    {
        $args = func_get_args();

        if (count($args) == 1) {
            foreach ($args[0] as $key => $value) {
                $this->view_data[$key] = $value;
            }
        } else {
            $this->view_data[$args[0]] = $args[1];
        }
        return $this;
    }

    public function render($view = 'layout')
    {
        $this->load->view('layout/' . $view, $this->view_data);
    }

    /**
     * Simple function to load a view directly using the assigned template
     * Does not use buffering or rendering
     * @param string $view
     * @param array $data
     */
    public function load_view($view, $data = array())
    {
        $view = explode('/', $view);

        $this->load->view($view[0] . '/' . $view[1], $data);
    }

}
