<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Base_Controller
 */
class Base_Controller extends MX_Controller
{

    /** @var bool */
    public $ajax_controller = false;

    /**
     * Base_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config->load('invoice_plane');

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller && !$this->input->is_ajax_request()) {
            exit;
        }

        // Globally disallow GET requests to delete methods
        $this->load->helper('url');
        if (strstr(current_url(), 'delete') && $this->input->method() !== 'post') {
            show_404();
        }

        // Load basic stuff
        $this->load->library('session');
        $this->load->helper('redirect');

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
            $this->load->helper('client');

            // Load setting model and load settings
            $this->load->model('settings/mdl_settings');
            $this->mdl_settings->load_settings();
            $this->load->helper('settings');
            $this->load->helper('language');

            // Fall back to default language if the current language isn't present
            /**
             * IP-785: When the language is set in the database, but not available locally, an error occurs
             * When people copy their databases with set languages into the IP database,
             * languages like 'german' and 'greek' are set as users_language.
             * When that language isn't available in /application/language, an error occurs
             * The todo was to fix that error. This is an attempt:
             * 1) Find available languages
             * 2) Match with the users language
             * 3) if users language is not available, fall back to 'default_language' from the settings
             *
             * Edge case:
             * 1) If 'default_language' from the settings isn't available... fix that too?
             * 2) Then fall back to english
             */
            $avail_languages = get_available_languages();
            $user_lang = $this->session->userdata('user_language');

            if(!in_array($user_lang, $avail_languages)|| is_null($user_lang))
            {
                $current_language = $user_lang = get_setting('default_language');
            }

            if (empty($user_lang) || $user_lang == 'system') {
                set_language(get_setting('default_language'));
            } else {
                set_language($user_lang);
            }

            // Load the layout module to start building the app
            $this->load->module('layout');

        }
    }
}
