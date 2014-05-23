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

class Mdl_Setup extends CI_Model {

    public $errors = array();

    public function install_tables()
    {
        $file_contents = read_file(APPPATH . 'modules/setup/sql/000_1.0.sql');

        $this->execute_contents($file_contents);

        $this->save_version('000_1.0.sql');

        if ($this->errors)
        {
            return FALSE;
        }

        $this->install_default_data();

        $this->install_default_settings();

        return TRUE;
    }

    public function upgrade_tables()
    {
        // Collect the available SQL files
        $sql_files = directory_map(APPPATH . 'modules/setup/sql', TRUE);

        // Sort them so they're in natural order
        sort($sql_files);

        // Unset the installer
        unset($sql_files[0]);

        // Loop through the files and take appropriate action
        foreach ($sql_files as $sql_file)
        {
            if (substr($sql_file, -4) == '.sql')
            {
                // $this->db->select('COUNT(*) AS update_applied');
                $this->db->where('version_file', $sql_file);
                // $update_applied = $this->db->get('fi_versions')->row()->update_applied;
                $update_applied = $this->db->get('fi_versions');

                // if (!$update_applied)
                if (!$update_applied->num_rows())
                {
                    $file_contents = read_file(APPPATH . 'modules/setup/sql/' . $sql_file);

                    $this->execute_contents($file_contents);

                    $this->save_version($sql_file);

                    // Check for any required upgrade methods
                    $upgrade_method = 'upgrade_' . str_replace('.', '_', substr($sql_file, 0, -4));

                    if (method_exists($this, $upgrade_method))
                    {
                        $this->$upgrade_method();
                    }
                }
            }
        }

        if ($this->errors)
        {
            return FALSE;
        }

        $this->install_default_settings();

        return TRUE;
    }

    private function execute_contents($contents)
    {
        $commands = explode(';', $contents);

        foreach ($commands as $command)
        {
            if ($command)
            {
                if (!$this->db->query(trim($command) . ';'))
                {
                    $this->errors[] = mysql_error();
                }
            }
        }
    }

    public function install_default_data()
    {
        $this->db->insert('fi_invoice_groups', array('invoice_group_name'    => 'Invoice Default', 'invoice_group_next_id' => 1));
        $this->db->insert('fi_invoice_groups', array('invoice_group_name'    => 'Quote Default', 'invoice_group_prefix'  => 'QUO', 'invoice_group_next_id' => 1));
    }

    private function install_default_settings()
    {
        $this->load->helper('string');

        $default_settings = array(
            'default_language'             => $this->session->userdata('fi_lang'),
            'date_format'                  => 'm/d/Y',
            'currency_symbol'              => '$',
            'currency_symbol_placement'    => 'before',
            'invoices_due_after'           => 30,
            'quotes_expire_after'          => 15,
            'default_invoice_group'        => 1,
            'default_quote_group'          => 2,
            'thousands_separator'          => ',',
            'decimal_point'                => '.',
            'cron_key'                     => random_string('alnum', 16),
            'tax_rate_decimal_places'      => 2,
            'pdf_invoice_template'         => 'default',
            'pdf_invoice_template_paid'    => 'default',
            'pdf_invoice_template_overdue' => 'default',
            'pdf_quote_template'           => 'default',
            'public_invoice_template'      => 'default',
            'public_quote_template'        => 'default'
        );

        foreach ($default_settings as $setting_key => $setting_value)
        {
            $this->db->where('setting_key', $setting_key);

            if (!$this->db->get('fi_settings')->num_rows())
            {
                $db_array = array(
                    'setting_key'   => $setting_key,
                    'setting_value' => $setting_value
                );

                $this->db->insert('fi_settings', $db_array);
            }
        }
    }

    private function save_version($sql_file)
    {
        $version_db_array = array(
            'version_date_applied' => time(),
            'version_file'         => $sql_file,
            'version_sql_errors'   => count($this->errors)
        );

        $this->db->insert('fi_versions', $version_db_array);
    }

    public function upgrade_013_1_1_3()
    {
        // Assign unique url key to any existing invoices
        $this->load->helper('string');

        $invoices = $this->db->select('invoice_id')->get('fi_invoices')->result();

        foreach ($invoices as $invoice)
        {
            $this->db->where('invoice_id', $invoice->invoice_id);
            $this->db->set('invoice_url_key', random_string('unique'));
            $this->db->update('fi_invoices');
        }

        // Add a unique key to the url key column
        $this->db->query("ALTER TABLE `fi_invoices` ADD UNIQUE (`invoice_url_key`)");
    }

    public function upgrade_030_1_3_0()
    {
        $this->db->where('setting_key', 'default_invoice_template');
        $this->db->delete('fi_settings');

        $this->db->where('setting_key', 'default_quote_template');
        $this->db->delete('fi_settings');

        // Update paid invoices with the new paid status
        $this->load->model('invoices/mdl_invoices');
        $this->mdl_invoices->where('invoice_total >', 0);
        $this->mdl_invoices->where('invoice_paid', 'invoice_total', FALSE);
        $this->mdl_invoices->where('invoice_balance', 0);
        $invoices = $this->mdl_invoices->get()->result();

        foreach ($invoices as $invoice)
        {
            $this->db->set('invoice_status_id', 4);
            $this->db->where('invoice_id', $invoice->invoice_id);
            $this->db->update('fi_invoices');
        }
    }

}

?>