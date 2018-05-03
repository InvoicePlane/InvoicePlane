<?php

/**
 * Class Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Setup extends MX_Controller
{
    public function __construct()
    {
        // Abort if the setup was already run
        if (env_bool('DISABLE_SETUP', false)) {
            show_error('The setup is disabled.', 403);
        }

        // Load the base controller
        parent::__construct();

        // Load the layout module and configure it
        $this->load->module('layout');
        $this->layout->setLayout('setup');
    }

    /**
     * Show the setup welcome screen with the language select
     */
    public function index()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'requirements');
            redirect('setup/requirements');
        }

        $this->layout->render('setup/index');
    }

    /**
     * Run the requirements check
     */
    public function requirements()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'database');
            redirect('setup/database');
        }

        $this->checkStep('requirements');

        //
    }

    /**
     * Get the database credentials from the user and check connection
     */
    public function database()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'databaseInit');
            redirect('setup/database-init');
        }

        $this->checkStep('database');

        //
    }

    /**
     * Import the initial database scheme and import the default data
     */
    public function databaseInit()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'adminAccount');
            redirect('setup/admin-account');
        }

        $this->checkStep('databaseInit');

        //
    }

    /**
     * Let the user create his admin account and save it to the database
     */
    public function adminAccount()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'systemSettings');
            redirect('setup/system-settings');
        }

        $this->checkStep('adminAccount');

        //
    }

    /**
     * Let the user configure some basic parts of the application
     */
    public function systemSettings()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'advancedSettings');
            redirect('setup/advanced-settings');
        }

        $this->checkStep('systemSettings');

        //
    }

    /**
     * Let the user configure more detailed settings for the application
     */
    public function advancedSettings()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'completed');
            redirect('setup/completed');
        }

        $this->checkStep('advancedSettings');

        //
    }

    /**
     * Show the completed screen with login link
     */
    public function completed()
    {
        $this->checkStep('completed');

        //
    }

    /**
     * Check if the user is in the correct installation step
     *
     * @param string $step
     */
    private function checkStep($step)
    {
        if ($this->session->userdata('install_step') !== $step) {
            redirect('setup');
        }
    }
}
