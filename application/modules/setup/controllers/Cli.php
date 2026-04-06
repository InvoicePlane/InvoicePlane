<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2026 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Cli extends MX_Controller
{
    public function __construct()
    {
        if ( ! is_cli()) {
            show_error('This controller can only be accessed via the command line.', 403);
        }

        parent::__construct();

        $this->load->helper('directory');
        $this->load->helper('language');
        $this->load->helper('trans');
        $this->load->helper('settings');
        $this->load->helper('string');

        $this->load->model('settings/mdl_settings');
        $this->load->model('setup/mdl_setup');

        $this->load->library('session');

        $lang = getenv('DEFAULT_LANGUAGE') ?: 'english';
        $lang = mb_strtolower($lang);

        if ( ! is_dir(APPPATH . 'language/' . $lang)) {
            echo 'Warning: DEFAULT_LANGUAGE "' . $lang . '" is not available, falling back to "english".' . PHP_EOL;
            $lang = 'english';
        }

        $this->session->set_userdata('ip_lang', $lang);

        $this->lang->load('ip', $lang);
    }

    /**
     * Create a default admin user if no users exist.
     *
     * Reads DEFAULT_ADMIN_EMAIL, DEFAULT_ADMIN_PASSWORD, and DEFAULT_ADMIN_NAME
     * from the environment. Falls back to admin@localhost, a random password,
     * and "admin" respectively. The generated password is printed to stdout.
     * Skips creation when any user already exists in the database.
     *
     * Usage: php index.php setup/cli/create_default_user
     */
    public function create_default_user(): void
    {
        $this->load->database();

        if ($this->db->count_all('ip_users') > 0) {
            echo 'Users already exist — skipping default user creation.' . PHP_EOL;
            return;
        }

        $email           = getenv('DEFAULT_ADMIN_EMAIL')    ?: 'admin@localhost';
        $name            = getenv('DEFAULT_ADMIN_NAME')     ?: 'admin';
        $plain_password  = getenv('DEFAULT_ADMIN_PASSWORD') ?: bin2hex(random_bytes(12));
        $generated       = ! getenv('DEFAULT_ADMIN_PASSWORD');

        $this->load->library('crypt');

        $salt = $this->crypt->salt();
        $now  = date('Y-m-d H:i:s');

        $this->db->insert('ip_users', [
            'user_type'          => 1,
            'user_date_created'  => $now,
            'user_date_modified' => $now,
            'user_name'          => $name,
            'user_email'         => $email,
            'user_password'      => $this->crypt->generate_password($plain_password, $salt),
            'user_psalt'         => $salt,
        ]);

        echo 'Default admin user created.' . PHP_EOL;
        echo '  Email:    ' . $email . PHP_EOL;
        if ($generated) {
            echo '  Password: ' . $plain_password . ' (generated — please change this)' . PHP_EOL;
        }
    }

    /**
     * Run pending database migrations.
     *
     * Usage: php index.php setup/cli/migrate
     */
    public function migrate(): void
    {
        $this->load->database();

        if ( ! $this->db->table_exists('ip_versions')) {
            echo 'No existing installation detected. Running fresh install...' . PHP_EOL;

            $success = $this->mdl_setup->install_tables();

            if ( ! $success) {
                echo 'Install failed with errors:' . PHP_EOL;
                foreach ($this->mdl_setup->errors as $error) {
                    echo '  - ' . $error . PHP_EOL;
                }
                exit(1);
            }

            echo 'Base tables installed successfully.' . PHP_EOL;
        }

        echo 'Running pending migrations...' . PHP_EOL;

        $success = $this->mdl_setup->upgrade_tables();

        if ( ! $success) {
            echo 'Migration failed with errors:' . PHP_EOL;
            foreach ($this->mdl_setup->errors as $error) {
                echo '  - ' . $error . PHP_EOL;
            }
            exit(1);
        }

        echo 'Migrations completed successfully.' . PHP_EOL;
    }
}
