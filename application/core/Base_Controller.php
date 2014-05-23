<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Base_Controller extends MX_Controller {

    public $ajax_controller = false;

    public function __construct()
    {
        parent::__construct();

        $this->config->load('fusion_invoice');

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller and !$this->input->is_ajax_request())
        {
            exit;
        }

        $this->load->library('session');
        $this->load->helper('url');

        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper('number');
        $this->load->helper('pager');
        $this->load->helper('invoice');
        $this->load->helper('date');
        $this->load->helper('redirect');

        // Load setting model and load settings
        $this->load->model('settings/mdl_settings');
        $this->mdl_settings->load_settings();

        $this->lang->load('fi', $this->mdl_settings->setting('default_language'));

        $this->load->helper('language');

        $this->load->module('layout');
    }

}

?>