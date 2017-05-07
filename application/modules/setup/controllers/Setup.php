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
 * Class Setup
 */
class Setup extends MX_Controller
{
    public $errors = 0;

    /**
     * Setup constructor.
     */
    public function __construct()
    {
        if (env_bool('DISABLE_SETUP', false)) {
            show_error('The setup is disabled.', 403);
        }

        parent::__construct();

        $this->load->library('session');

        $this->load->helper('file');
        $this->load->helper('directory');
        $this->load->helper('url');
        $this->load->helper('language');
        $this->load->helper('trans');
        $this->load->helper('settings');
        $this->load->helper('echo');

        $this->load->model('mdl_setup');

        $this->load->module('layout');

        if (!$this->session->userdata('ip_lang')) {
            $this->session->set_userdata('ip_lang', 'english');
        } else {
            set_language($this->session->userdata('ip_lang'));
        }

        $this->lang->load('ip', $this->session->userdata('ip_lang'));
    }

    public function index()
    {
        redirect('setup/language');
    }

    public function language()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('ip_lang', $this->input->post('ip_lang'));
            $this->session->set_userdata('install_step', 'prerequisites');
            redirect('setup/prerequisites');
        }

        // Reset the session cache
        $this->session->unset_userdata('install_step');
        $this->session->unset_userdata('is_upgrade');

        // Get all languages
        $languages = get_available_languages();
        $this->layout->set('languages', $languages);

        $this->layout->buffer('content', 'setup/language');
        $this->layout->render('setup');
    }

    public function prerequisites()
    {
        if ($this->session->userdata('install_step') <> 'prerequisites') {
            redirect('setup/language');
        }

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'configure_database');
            redirect('setup/configure_database');
        }

        $this->layout->set(
            array(
                'basics' => $this->check_basics(),
                'writables' => $this->check_writables(),
                'errors' => $this->errors
            )
        );

        $this->layout->buffer('content', 'setup/prerequisites');
        $this->layout->render('setup');
    }

    /**
     * @return array
     */
    private function check_basics()
    {
        $checks = array();

        $php_required = '5.6';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

        if ($php_installed < $php_required) {
            $this->errors += 1;

            $checks[] = array(
                'message' => sprintf(trans('php_version_fail'), $php_installed, $php_required),
                'success' => 0
            );
        } else {
            $checks[] = array(
                'message' => trans('php_version_success'),
                'success' => 1
            );
        }

        if (!ini_get('date.timezone')) {
            $checks[] = array(
                'message' => sprintf(trans('php_timezone_fail'), date_default_timezone_get()),
                'success' => 1,
                'warning' => 1
            );
        } else {
            $checks[] = array(
                'message' => trans('php_timezone_success'),
                'success' => 1
            );
        }

        return $checks;
    }

    /**
     * @return array
     */
    private function check_writables()
    {
        $checks = array();

        $writables = array(
            './ipconfig.php',
            './uploads',
            './uploads/archive',
            './uploads/customer_files',
            './uploads/temp',
            './uploads/temp/mpdf',
            './application/logs',
        );

        foreach ($writables as $writable) {
            if (!is_writable($writable)) {
                $checks[] = array(
                    'message' => $writable . ' ' . trans('is_not_writable'),
                    'success' => 0
                );

                $this->errors += 1;
            } else {
                $checks[] = array(
                    'message' => $writable . ' ' . trans('is_writable'),
                    'success' => 1
                );
            }
        }

        return $checks;
    }

    public function configure_database()
    {
        if ($this->session->userdata('install_step') <> 'configure_database') {
            redirect('setup/prerequisites');
        }

        if ($this->input->post('btn_continue')) {
            $this->load_ci_database();

            // This might be an upgrade - check if it is
            if (!$this->db->table_exists('ip_versions')) {
                // This appears to be an install
                $this->session->set_userdata('install_step', 'install_tables');
                redirect('setup/install_tables');
            } else {
                // This appears to be an upgrade
                $this->session->set_userdata('is_upgrade', true);
                $this->session->set_userdata('install_step', 'upgrade_tables');
                redirect('setup/upgrade_tables');
            }
        }

        if ($this->input->post('db_hostname')) {
            // Write a new database configuration to the ipconfig.php file
            $this->write_database_config(
                $this->input->post('db_hostname'),
                $this->input->post('db_username'),
                $this->input->post('db_password'),
                $this->input->post('db_database'),
                $this->input->post('db_port')
            );
        }

        // Check if the set credentials are correct
        $check_database = $this->check_database();

        $this->layout->set('database', $check_database);
        $this->layout->set('errors', $this->errors);
        $this->layout->buffer('content', 'setup/configure_database');
        $this->layout->render('setup');
    }

    /**
     * Load the database connection trough CodeIgniter
     */
    private function load_ci_database()
    {
        $this->load->database();
    }

    /**
     * @param $hostname
     * @param $username
     * @param $password
     * @param $database
     * @param int $port
     */
    private function write_database_config($hostname, $username, $password, $database, $port = 3306)
    {
        $config = file_get_contents(IPCONFIG_FILE);

        $config = preg_replace("/DB_HOSTNAME=(.*)?/", "DB_HOSTNAME=" . $hostname, $config);
        $config = preg_replace("/DB_USERNAME=(.*)?/", "DB_USERNAME=" . $username, $config);
        $config = preg_replace("/DB_PASSWORD=(.*)?/", "DB_PASSWORD=" . $password, $config);
        $config = preg_replace("/DB_DATABASE=(.*)?/", "DB_DATABASE=" . $database, $config);
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
            $this->errors += 1;

            return array(
                'message' => trans('cannot_connect_database_server'),
                'success' => false,
            );
        }

        // Initialize the database connection, turn off automatic error reporting to display connection issues manually
        error_reporting(0);
        $db_object = $this->load->database($db, true);

        // Try to initialize the database connection
        $can_connect = $db_object->conn_id ? true : false;

        if (!$can_connect) {
            $this->errors += 1;

            return array(
                'message' => trans('setup_db_cannot_connect'),
                'success' => false,
            );
        }

        return array(
            'message' => trans('database_properly_configured'),
            'success' => true,
        );
    }

    public function install_tables()
    {
        if ($this->session->userdata('install_step') <> 'install_tables') {
            redirect('setup/prerequisites');
        }

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'upgrade_tables');
            redirect('setup/upgrade_tables');
        }

        $this->load_ci_database();

        $this->layout->set(
            array(
                'success' => $this->mdl_setup->install_tables(),
                'errors' => $this->mdl_setup->errors
            )
        );

        $this->layout->buffer('content', 'setup/install_tables');
        $this->layout->render('setup');
    }

    public function upgrade_tables()
    {
        if ($this->session->userdata('install_step') <> 'upgrade_tables') {
            redirect('setup/prerequisites');
        }

        if ($this->input->post('btn_continue')) {
            if (!$this->session->userdata('is_upgrade')) {
                $this->session->set_userdata('install_step', 'create_user');
                redirect('setup/create_user');
            } else {
                $this->session->set_userdata('install_step', 'complete');
                redirect('setup/complete');
            }
        }

        $this->load_ci_database();

        // Set a new encryption key if none exists
        if (env('ENCRYPTION_KEY') === null) {
            $this->set_encryption_key();
        }

        $this->layout->set(
            array(
                'success' => $this->mdl_setup->upgrade_tables(),
                'errors' => $this->mdl_setup->errors
            )
        );

        $this->layout->buffer('content', 'setup/upgrade_tables');
        $this->layout->render('setup');
    }

    /**
     * Set a new encryption key in the ipconfig.php file
     */
    private function set_encryption_key()
    {
        $length = (env('ENCRYPTION_CIPHER') == 'AES-256' ? 32 : 16);

        if (function_exists('random_bytes')) {
            $key = 'base64:' . base64_encode(random_bytes($length));
        } else {
            $key = 'base64:' . base64_encode(openssl_random_pseudo_bytes($length));
        }

        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace("/ENCRYPTION_KEY=(.*)?/", "ENCRYPTION_KEY=" . $key, $config);
        write_file(IPCONFIG_FILE, $config);
    }

    public function create_user()
    {
        if ($this->session->userdata('install_step') <> 'create_user') {
            redirect('setup/prerequisites');
        }

        $this->load_ci_database();

        $this->load->model('users/mdl_users');

        $this->load->helper('country');

        if ($this->mdl_users->run_validation()) {
            $db_array = $this->mdl_users->db_array();
            $db_array['user_type'] = 1;

            $this->mdl_users->save(null, $db_array);

            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }

        $this->layout->set(
            array(
                'countries' => get_country_list(trans('cldr')),
                'languages' => get_available_languages(),
            )
        );
        $this->layout->buffer('content', 'setup/create_user');
        $this->layout->render('setup');
    }

    public function complete()
    {
        if ($this->session->userdata('install_step') <> 'complete') {
            redirect('setup/prerequisites');
        }

        // Additional tasks after setup is completed
        $this->post_setup_tasks();

        // Check if this is an update or the first install
        // First get all version entries from the database and format them
        $this->load_ci_database();
        $versions = $this->db->query('SELECT * FROM ip_versions');
        if ($versions->num_rows() > 0) {
            foreach ($versions->result() as $row):
                $data[] = $row;
            endforeach;
        }

        // Then check if the first version entry is less than 30 minutes old
        // If yes we assume that the user ran the setup a few minutes ago
        if ($data[0]->version_date_applied < (time() - 1800)) {
            $update = true;
        } else {
            $update = false;
        }
        $this->layout->set('update', $update);

        $this->layout->buffer('content', 'setup/complete');
        $this->layout->render('setup');
    }

    private function post_setup_tasks()
    {
        // Set SETUP_COMPLETED to true
        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace("/SETUP_COMPLETED=(.*)?/", "SETUP_COMPLETED=true", $config);
        write_file(IPCONFIG_FILE, $config);
    }

}
