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

class Setup extends MX_Controller
{
    public $errors = 0;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->helper('file');
        $this->load->helper('directory');
        $this->load->helper('url');
        $this->load->helper('language');

        $this->load->model('mdl_setup');

        $this->load->module('layout');

        if (!$this->session->userdata('ip_lang')) {
            $this->session->set_userdata('ip_lang', 'english');
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

        $this->session->unset_userdata('install_step');
        $this->session->unset_userdata('is_upgrade');

        $this->load->helper('directory');

        $languages = directory_map(APPPATH . '/language', TRUE);

        sort($languages);

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
                $this->session->set_userdata('is_upgrade', TRUE);
                $this->session->set_userdata('install_step', 'upgrade_tables');
                redirect('setup/upgrade_tables');
            }
        }

        if ($this->input->post('db_hostname')) {
            $this->write_database_config($this->input->post('db_hostname'), $this->input->post('db_username'), $this->input->post('db_password'), $this->input->post('db_database'));
        }

        $this->layout->set('database', $this->check_database());
        $this->layout->set('errors', $this->errors);
        $this->layout->buffer('content', 'setup/configure_database');
        $this->layout->render('setup');
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

        $this->layout->set(
            array(
                'success' => $this->mdl_setup->upgrade_tables(),
                'errors' => $this->mdl_setup->errors
            )
        );

        $this->layout->buffer('content', 'setup/upgrade_tables');
        $this->layout->render('setup');
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

            $this->mdl_users->save(NULL, $db_array);

            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }

        $this->layout->set(
            array(
                'countries' => get_country_list(lang('cldr')),
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

    private function check_writables()
    {
        $checks = array();

        $writables = array(
            './uploads',
            './uploads/temp',
            './uploads/archive',
            './uploads/customer_files',
            './' . APPPATH . 'config/', // for database.php
            './' . APPPATH . 'helpers/mpdf/tmp',
            './' . APPPATH . 'helpers/mpdf/ttfontdata',
            './' . APPPATH . 'helpers/mpdf/graph_cache',
            './' . APPPATH . 'logs'
        );

        foreach ($writables as $writable) {
            if (!is_writable($writable)) {
                $checks[] = array(
                    'message' => $writable . ' ' . lang('is_not_writable'),
                    'success' => 0
                );

                $this->errors += 1;
            } else {
                $checks[] = array(
                    'message' => $writable . ' ' . lang('is_writable'),
                    'success' => 1
                );
            }
        }

        return $checks;
    }

    private function check_database()
    {
        $this->load->library('lib_mysql');

        if (is_file(APPPATH . '/config/database.php')) {
            // There is alread a (hopefully working?) database.php file.
            require(APPPATH . '/config/database.php');
        } else {
            // No database.php file existent. Use the _empty template.
            require(APPPATH . '/config/database_empty.php');
        }

        $db = $db['default'];

        $can_connect = $this->lib_mysql->connect($db['hostname'], $db['username'], $db['password']);

        if (!$can_connect) {
            $this->errors += 1;

            return array(
                'message' => lang('cannot_connect_database_server'),
                'success' => 0
            );
        }

        $can_select_db = $this->lib_mysql->select_db($db['database']);

        if (!$can_select_db) {
            $this->errors += 1;

            return array(
                'message' => lang('cannot_select_specified_database'),
                'success' => 0
            );
        }

        return array(
            'message' => lang('database_properly_configured'),
            'success' => 1
        );
    }

    private function check_basics()
    {
        $checks = array();

        $php_required = '5.3';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

        if ($php_installed < $php_required) {
            $this->errors += 1;

            $checks[] = array(
                'message' => sprintf(lang('php_version_fail'), $php_installed, $php_required),
                'success' => 0
            );
        } else {
            $checks[] = array(
                'message' => lang('php_version_success'),
                'success' => 1
            );
        }

        if (!ini_get('date.timezone')) {
            #$this->errors += 1;

            $checks[] = array(
                'message' => sprintf(lang('php_timezone_fail'), date_default_timezone_get()),
                'warning' => 1
            );
        } else {
            $checks[] = array(
                'message' => lang('php_timezone_success'),
                'success' => 1
            );
        }

        return $checks;
    }

    private function write_database_config($hostname, $username, $password, $database)
    {
        $db_file = read_file(APPPATH . 'config/database_empty.php');

        $db_file = str_replace('$db[\'default\'][\'hostname\'] = \'\'', '$db[\'default\'][\'hostname\'] = \'' . addcslashes($hostname, '\'\\') . '\'', $db_file);
        $db_file = str_replace('$db[\'default\'][\'username\'] = \'\'', '$db[\'default\'][\'username\'] = \'' . addcslashes($username, '\'\\') . '\'', $db_file);
        $db_file = str_replace('$db[\'default\'][\'password\'] = \'\'', '$db[\'default\'][\'password\'] = \'' . addcslashes($password, '\'\\') . '\'', $db_file);
        $db_file = str_replace('$db[\'default\'][\'database\'] = \'\'', '$db[\'default\'][\'database\'] = \'' . addcslashes($database, '\'\\') . '\'', $db_file);

        write_file(APPPATH . 'config/database.php', $db_file);
    }

    private function load_ci_database()
    {
        $this->load->database();
        // $this->db->db_debug = 0;
    }

}
