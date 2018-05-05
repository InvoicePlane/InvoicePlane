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
    /** @var bool */
    public $errors = false;

    /**
     * Setup constructor.
     */
    public function __construct()
    {
        // Abort if the setup was already run
        if (env_bool('DISABLE_SETUP', false)) {
            show_error('The setup is disabled.', 403);
        }

        // Load the base controller
        parent::__construct();

        // Load the setup model
        $this->load->model('mdl_setup');

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

        // Reset the setup progress
        $this->session->unset_userdata('install_step');

        $this->layout->render('setup/index', ['progress' => 12]);
    }

    /**
     * Run the requirements check
     */
    public function requirements()
    {
        $this->checkStep('requirements');

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'database');
            redirect('setup/database');
        }

        $this->layout->render('setup/requirements', [
            'basics' => $this->checkBasics(),
            'writables' => $this->checkWritables(),
            'errors' => $this->errors,
            'progress' => 25,
        ]);
    }

    /**
     * Get the database credentials from the user and check connection
     */
    public function database()
    {
        $this->checkStep('database');

        if ($this->input->post('btn_continue')) {
            $this->load->database();

            $this->session->set_userdata('install_step', 'databaseInit');
            redirect('setup/database-init');
        }

        if ($this->input->post('db_hostname')) {
            // Write a new database configuration to the ipconfig.php file
            $this->write_database_config(
                $this->input->post('db_hostname'),
                $this->input->post('db_username'),
                $this->input->post('db_password'),
                $this->input->post('db_database'),
                $this->input->post('db_prefix'),
                $this->input->post('db_port')
            );
        }

        $this->layout->render('setup/database', [
            'database' => $this->check_database(),
            'errors' => $this->errors,
            'progress' => 38,
        ]);
    }

    /**
     * Import the initial database scheme and import the default data
     */
    public function databaseInit()
    {
        $this->checkStep('databaseInit');

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'adminAccount');
            redirect('setup/admin-account');
        }

        $this->layout->render('setup/database_init', [
            'errors' => $this->errors,
            'progress' => 50,
        ]);
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

    /**
     * Check the basics needed for InvoicePlane such as the PHP version
     * and a correctly set timezone.
     *
     * @return array
     */
    private function checkBasics()
    {
        $checks = [];

        $php_required = '7.1';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

        if ($php_installed < $php_required) {
            $this->errors = true;

            $checks[] = [
                'message' => sprintf(trans('setup_php_version_fail'), $php_installed, $php_required),
                'success' => false,
            ];
        } else {
            $checks[] = [
                'message' => trans('setup_php_version_success'),
                'success' => true,
            ];
        }

        if (!ini_get('date.timezone')) {
            $checks[] = [
                'message' => sprintf(trans('setup_php_timezone_fail'), date_default_timezone_get()),
                'success' => true,
                'warning' => true,
            ];
        } else {
            $checks[] = [
                'message' => trans('setup_php_timezone_success'),
                'success' => true,
            ];
        }

        return $checks;
    }

    /**
     * Check if specific files or folders are writable by the application
     *
     * @return array
     */
    private function checkWritables()
    {
        $checks = [];

        $writables = [
            IPCONFIG_FILE,
            LOGS_FOLDER,
        ];

        foreach ($writables as $writable) {
            $writable_check = [
                'message' => '<code>' . str_replace(FCPATH, '', $writable) . '</code>&nbsp;',
                'success' => true,
            ];

            if (!is_writable($writable)) {
                $writable_check['message'] .= trans('setup_file_is_not_writable');
                $writable_check['success'] = false;

                $this->errors = true;
            } else {
                $writable_check['message'] .= trans('setup_file_is_writable');
            }

            $checks[] = $writable_check;
        }

        return $checks;
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $prefix
     * @param int    $port
     */
    private function write_database_config($hostname, $username, $password, $database, $prefix, $port = 3306)
    {
        $config = file_get_contents(IPCONFIG_FILE);

        $config = preg_replace("/DB_HOSTNAME=(.*)?/", "DB_HOSTNAME=" . $hostname, $config);
        $config = preg_replace("/DB_USERNAME=(.*)?/", "DB_USERNAME=" . $username, $config);
        $config = preg_replace("/DB_PASSWORD=(.*)?/", "DB_PASSWORD=" . $password, $config);
        $config = preg_replace("/DB_DATABASE=(.*)?/", "DB_DATABASE=" . $database, $config);
        $config = preg_replace("/DB_PREFIX=(.*)?/", "DB_PREFIX=" . $prefix, $config);
        $config = preg_replace("/DB_PORT=(.*)?/", "DB_PORT=" . $port, $config);

        write_file(IPCONFIG_FILE, $config);
    }

    /**
     * @return array
     */
    private function check_database()
    {
        // Reload the ipconfig.php file
        global $dotenv;
        $dotenv->overload();

        // Load the database config and configure it to test the connection
        include(APPPATH . 'config/database.php');
        $db = $db['default'];
        $db['autoinit'] = false;
        $db['db_debug'] = false;

        // Check if there is some configuration set
        if (empty($db['hostname'])) {
            $this->errors = true;

            return [
                'message' => trans('cannot_connect_database_server'),
                'success' => false,
            ];
        }

        // Initialize the database connection, turn off automatic error reporting to display connection issues manually
        error_reporting(0);
        $db_object = $this->load->database($db, true);

        // Try to initialize the database connection
        if (!$db_object->initialize()) {
            $this->errors = true;

            return [
                'message' => trans('setup_db_cannot_connect'),
                'success' => false,
            ];
        }

        return [
            'message' => trans('database_properly_configured'),
            'success' => true,
        ];
    }
}
