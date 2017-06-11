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
 * Class Settings
 */
class Settings extends Admin_Controller
{
    /**
     * Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_settings');
        $this->load->library('crypt');
        $this->load->library('form_validation');
    }

    public function index()
    {
        // Get the payment gateway configurations
        $this->config->load('payment_gateways');
        $gateways = $this->config->item('payment_gateways');

        // Save input if request is POSt
        if ($this->input->post('settings')) {
            $settings = $this->input->post('settings');

            // Only execute if the setting is different
            if ($settings['tax_rate_decimal_places'] != get_setting('tax_rate_decimal_places')) {
                $this->db->query("
                    ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent`
                    DECIMAL( 5, {$settings['tax_rate_decimal_places']} ) NOT null"
                );
            }

            // Save the submitted settings
            foreach ($settings as $key => $value) {
                if (strpos($key, 'field_is_password') !== false || strpos($key, 'field_is_amount') !== false) {
                    // Skip all meta fields
                    continue;
                }

                if (isset($settings[$key . '_field_is_password']) && empty($value)) {
                    // Password field, but empty value, let's skip it
                    continue;
                }

                if (isset($settings[$key . '_field_is_password']) && $value != '') {
                    // Encrypt passwords but don't save empty passwords
                    $this->mdl_settings->save($key, $this->crypt->encode(trim($value)));

                } elseif (isset($settings[$key . '_field_is_amount'])) {

                    // Format amount inputs
                    $this->mdl_settings->save($key, standardize_amount($value));

                } else {

                    $this->mdl_settings->save($key, $value);

                }
            }

            $upload_config = array(
                'upload_path' => './uploads/',
                'allowed_types' => 'gif|jpg|jpeg|png|svg',
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

            $this->session->set_flashdata('alert_success', trans('settings_successfully_saved'));

            redirect('settings');
        }

        // Load required resources
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('email_templates/mdl_email_templates');
        $this->load->model('settings/mdl_versions');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('invoices/mdl_templates');

        $this->load->helper('country');

        // Collect the list of templates
        $pdf_invoice_templates = $this->mdl_templates->get_invoice_templates('pdf');
        $public_invoice_templates = $this->mdl_templates->get_invoice_templates('public');
        $pdf_quote_templates = $this->mdl_templates->get_quote_templates('pdf');
        $public_quote_templates = $this->mdl_templates->get_quote_templates('public');

        // Get all themes
        $available_themes = $this->mdl_settings->get_themes();

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
                'languages' => get_available_languages(),
                'countries' => get_country_list(trans('cldr')),
                'date_formats' => date_formats(),
                'current_date' => new DateTime(),
                'available_themes' => $available_themes,
                'email_templates_quote' => $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result(),
                'email_templates_invoice' => $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result(),
                'gateway_drivers' => $gateways,
                'gateway_currency_codes' => \Omnipay\Common\Currency::all(),
                'current_version' => $current_version,
                'first_days_of_weeks' => array('0' => lang('sunday'), '1' => lang('monday'))
            )
        );

        $this->layout->buffer('content', 'settings/index');
        $this->layout->render();
    }

    /**
     * @param $type
     */
    public function remove_logo($type)
    {
        unlink('./uploads/' . get_setting($type . '_logo'));

        $this->mdl_settings->save($type . '_logo', '');

        $this->session->set_flashdata('alert_success', lang($type . '_logo_removed'));

        redirect('settings');
    }
}
