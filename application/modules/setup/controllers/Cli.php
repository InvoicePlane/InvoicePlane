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
