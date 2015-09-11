<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Mdl_Setup extends CI_Model
{
    public $errors = array();

    public function install_tables()
    {
        $file_contents = read_file(APPPATH . 'modules/setup/sql/000_1.0.0.sql');

        $this->execute_contents($file_contents);

        $this->save_version('000_1.0.0.sql');

        if ($this->errors) {
            return false;
        }

        $this->install_default_data();

        $this->install_default_settings();

        return true;
    }

    public function upgrade_tables()
    {
        // Collect the available SQL files
        $sql_files = directory_map(APPPATH . 'modules/setup/sql', true);

        // Sort them so they're in natural order
        sort($sql_files);

        // Unset the installer
        unset($sql_files[0]);

        // Loop through the files and take appropriate action
        foreach ($sql_files as $sql_file) {
            if (substr($sql_file, -4) == '.sql') {
                // $this->db->select('COUNT(*) AS update_applied');
                $this->db->where('version_file', $sql_file);
                // $update_applied = $this->db->get('ip_versions')->row()->update_applied;
                $update_applied = $this->db->get('ip_versions');

                // if (!$update_applied)
                if (!$update_applied->num_rows()) {
                    $file_contents = read_file(APPPATH . 'modules/setup/sql/' . $sql_file);

                    $this->execute_contents($file_contents);

                    $this->save_version($sql_file);

                    // Check for any required upgrade methods
                    $upgrade_method = 'upgrade_' . str_replace('.', '_', substr($sql_file, 0, -4));

                    if (method_exists($this, $upgrade_method)) {
                        $this->$upgrade_method();
                    }
                }
            }
        }

        if ($this->errors) {
            return false;
        }

        $this->install_default_settings();

        return true;
    }

    private function execute_contents($contents)
    {
        $commands = explode(';', $contents);

        foreach ($commands as $command) {
            if (trim($command)) {
                if (!$this->db->query(trim($command) . ';')) {
                    $this->errors[] = mysql_error();
                }
            }
        }
    }

    public function install_default_data()
    {
        $this->db->insert('ip_invoice_groups',
            array('invoice_group_name' => 'Invoice Default', 'invoice_group_next_id' => 1));
        $this->db->insert('ip_invoice_groups', array(
            'invoice_group_name' => 'Quote Default',
            'invoice_group_prefix' => 'QUO',
            'invoice_group_next_id' => 1
        ));
    }

    private function install_default_settings()
    {
        $this->load->helper('string');

        $default_settings = array(
            'default_language' => $this->session->userdata('ip_lang'),
            'date_format' => 'm/d/Y',
            'currency_symbol' => '$',
            'currency_symbol_placement' => 'before',
            'invoices_due_after' => 30,
            'quotes_expire_after' => 15,
            'default_invoice_group' => 1,
            'default_quote_group' => 2,
            'thousands_separator' => ',',
            'decimal_point' => '.',
            'cron_key' => random_string('alnum', 16),
            'tax_rate_decimal_places' => 2,
            'item_price_decimal_places' => 2,
            'item_amount_decimal_places' => 2,
            'pdf_invoice_template' => 'default',
            'pdf_invoice_template_paid' => 'default',
            'pdf_invoice_template_overdue' => 'default',
            'pdf_quote_template' => 'default',
            'public_invoice_template' => 'default',
            'public_quote_template' => 'default',
            'disable_sidebar' => 1
        );

        foreach ($default_settings as $setting_key => $setting_value) {
            $this->db->where('setting_key', $setting_key);

            if (!$this->db->get('ip_settings')->num_rows()) {
                $db_array = array(
                    'setting_key' => $setting_key,
                    'setting_value' => $setting_value
                );

                $this->db->insert('ip_settings', $db_array);
            }
        }
    }

    private function save_version($sql_file)
    {
        $version_db_array = array(
            'version_date_applied' => time(),
            'version_file' => $sql_file,
            'version_sql_errors' => count($this->errors)
        );

        $this->db->insert('ip_versions', $version_db_array);
    }

    /*
     * Place upgrade functions here
     * e.g. if table rows have to be converted
     * public function upgrade_010_1_0_1() { ... }
     */

    public function upgrade_001_1_0_1()
    {
        // Nothing to do here
    }

    public function upgrade_002_1_0_1()
    {
        // Nothing to do here
    }

    public function upgrade_003_1_1_0()
    {
        // Nothing to do here
    }

    public function upgrade_004_1_1_1()
    {
        // Nothing to do here
    }

    public function upgrade_005_1_1_2()
    {
        // Nothing to do here
    }

    public function upgrade_006_1_2_0()
    {
        /* Update alert to notify about the changes with invoice deletion and credit invoices
         * but only display the warning when the previous version is 1.1.2 or lower and it's an update
         * therefore check if it's an update, if the time difference between v1.1.2 and v1.2.0 is
         * greater than 100 and if v1.2.0 was not installed within this update process
         */
        $this->db->where_in("version_file", array("006_1.2.0.sql", "005_1.1.2.sql"));
        $versions = $this->db->get('ip_versions')->result();
        $upgrade_diff = $versions[1]->version_date_applied - $versions[0]->version_date_applied;

        if ($this->session->userdata('is_upgrade') && $upgrade_diff > 100 && $versions[1]->version_date_applied > (time() - 100)) {
            $setup_notice = array(
                'type' => 'alert-danger',
                'content' => lang('setup_v120_alert'),
            );
            $this->session->set_userdata('setup_notice', $setup_notice);
        }
    }

    public function upgrade_007_1_2_1()
    {
        // Nothing to do here
    }
}
