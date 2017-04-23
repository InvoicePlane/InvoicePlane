<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Base_Controller
 */
class Base_Controller extends MX_Controller
{
    public $ajax_controller = false;

    public function __construct()
    {
        parent::__construct();

        $this->config->load('invoice_plane');

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller && !$this->input->is_ajax_request()) {
            exit;
        }

        // Load basic stuff
        $this->load->library('session');
        $this->load->helper('redirect');
        $this->load->helper('url');

        // Check if database has been configured
        if (!env_bool('SETUP_COMPLETED')) {
            redirect('/welcome');
        } else {

            $this->load->library('encryption');
            $this->load->library('form_validation');
            $this->load->library('session');
            $this->load->database();

            $this->load->helper('trans');
            $this->load->helper('number');
            $this->load->helper('pager');
            $this->load->helper('invoice');
            $this->load->helper('date');
            $this->load->helper('form');
            $this->load->helper('echo');

            // Load setting model and load settings
            $this->load->model('settings/mdl_settings');
            $this->mdl_settings->load_settings();
            $this->load->helper('settings');

            // Load the language based on user config, fall back to system if needed
            $user_lang = $this->session->userdata('user_language');

            if (empty($user_lang) || $user_lang == 'system') {
                set_language(get_setting('default_language'));
            } else {
                set_language($user_lang);
            }

            $this->load->helper('language');

            // Load the layout module to start building the app
            $this->load->module('layout');

        }
    }
}
