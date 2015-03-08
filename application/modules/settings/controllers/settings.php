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

class Settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_settings');
    }

    public function index()
    {
        if ($this->input->post('settings')) {
            $settings = $this->input->post('settings');

            // Only execute if the setting is different
            if ($settings['tax_rate_decimal_places'] <> $this->mdl_settings->setting('tax_rate_decimal_places')) {
                $this->db->query("ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent` DECIMAL( 5, {$settings['tax_rate_decimal_places']} ) NOT NULL");
            }

            // Save the submitted settings
            foreach ($settings as $key => $value) {
                // Don't save empty passwords
                if ($key == 'smtp_password' or $key == 'merchant_password') {
                    if ($value <> '') {
                        $this->load->library('encrypt');
                        $this->mdl_settings->save($key, $this->encrypt->encode($value));
                    }
                } else {
                    $this->mdl_settings->save($key, $value);
                }
            }

            $upload_config = array(
                'upload_path' => './uploads/',
                'allowed_types' => 'gif|jpg|png|svg',
                'max_size' => '9999',
                'max_width' => '9999',
                'max_height' => '9999'
            );

            // Check for invoice logo upload
            if ($_FILES['invoice_logo']['name']) {
                $this->load->library('upload', $upload_config);

                if (!$this->upload->do_upload('invoice_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                }

                $upload_data = $this->upload->data();

                $this->mdl_settings->save('invoice_logo', $upload_data['file_name']);
            }

            // Check for login logo upload
            if ($_FILES['login_logo']['name']) {
                $this->load->library('upload', $upload_config);

                if (!$this->upload->do_upload('login_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                }

                $upload_data = $this->upload->data();

                $this->mdl_settings->save('login_logo', $upload_data['file_name']);
            }

            $this->session->set_flashdata('alert_success', lang('settings_successfully_saved'));

            redirect('settings');
        }

        // Load required resources
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('email_templates/mdl_email_templates');
        $this->load->model('settings/mdl_versions');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('invoices/mdl_templates');

        $this->load->helper('directory');
        $this->load->helper('country');

        $this->load->library('merchant');

        // Collect the list of templates
        $pdf_invoice_templates = $this->mdl_templates->get_invoice_templates('pdf');
        $public_invoice_templates = $this->mdl_templates->get_invoice_templates('public');
        $pdf_quote_templates = $this->mdl_templates->get_quote_templates('pdf');
        $public_quote_templates = $this->mdl_templates->get_quote_templates('public');

        // Collect the list of languages
        $languages = directory_map(APPPATH . 'language', TRUE);
        sort($languages);

        // Get the current version
        $current_version = $this->mdl_versions->limit(1)->where('version_sql_errors', 0)->get()->row()->version_file;
        $current_version = str_replace('.sql', '', substr($current_version, strpos($current_version, '_') + 1));

        // Set data in the layout
        $this->layout->set(
            array(
                'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'public_invoice_templates' => $public_invoice_templates,
                'pdf_invoice_templates' => $pdf_invoice_templates,
                'public_quote_templates' => $public_quote_templates,
                'pdf_quote_templates' => $pdf_quote_templates,
                'languages' => $languages,
                'countries' => get_country_list(lang('cldr')),
                'date_formats' => date_formats(),
                'current_date' => new DateTime(),
                'email_templates_quote' => $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result(),
                'email_templates_invoice' => $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result(),
                'merchant_drivers' => $this->merchant->valid_drivers(),
                'merchant_currency_codes' => Merchant::$NUMERIC_CURRENCY_CODES,
                'current_version' => $current_version,
                'first_days_of_weeks' => array("0" => lang("sunday"), "1" => lang("monday"))
            )
        );

        $this->layout->buffer('content', 'settings/index');
        $this->layout->render();
    }

    public function remove_logo($type)
    {
        unlink('./uploads/' . $this->mdl_settings->setting($type . '_logo'));

        $this->mdl_settings->save($type . '_logo', '');

        $this->session->set_flashdata('alert_success', lang($type . '_logo_removed'));

        redirect('settings');
    }

}
