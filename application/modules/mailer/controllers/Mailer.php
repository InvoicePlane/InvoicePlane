<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mailer extends Admin_Controller
{
    private bool $mailer_configured;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('mailer');

        $this->mailer_configured = mailer_configured();

        if ( ! $this->mailer_configured) {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    /**
     * @param $invoice_id
     */
    public function invoice($invoice_id)
    {
        if ( ! $this->mailer_configured) {
            return;
        }

        $this->load->model(
            [
                'email_templates/mdl_email_templates',
                'custom_fields/mdl_custom_fields',
                'invoices/mdl_templates',
                'invoices/mdl_invoices',
                'upload/mdl_uploads',
            ]
        );

        $this->load->helper(['template', 'dropzone']);

        $invoice           = $this->mdl_invoices->get_by_id($invoice_id);
        $email_template_id = select_email_invoice_template($invoice);
        $email_template    = '{}';

        if ($email_template_id) {
            $email_template = json_encode($this->mdl_email_templates->get_by_id($email_template_id), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }

        // Get all custom fields
        $custom_fields = [];
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        // Determine default sender email: use smtp_mail_from setting if configured, otherwise fall back to user email
        $default_from_email = get_setting('smtp_mail_from');
        if (empty($default_from_email)) {
            $default_from_email = $invoice->user_email;
        }

        $this->layout->set(
            [
                'selected_email_template' => $email_template_id,
                'selected_pdf_template'   => select_pdf_invoice_template($invoice),
                'email_templates'         => $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result(),
                'email_template'          => $email_template,
                'custom_fields'           => $custom_fields,
                'pdf_templates'           => $this->mdl_templates->get_invoice_templates(),
                'invoice'                 => $invoice,
                'default_from_email'      => $default_from_email,
            ]
        );
        $this->layout->buffer('content', 'mailer/invoice');
        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function quote($quote_id)
    {
        if ( ! $this->mailer_configured) {
            return;
        }

        $this->load->model(
            [
                'email_templates/mdl_email_templates',
                'custom_fields/mdl_custom_fields',
                'invoices/mdl_templates',
                'quotes/mdl_quotes',
                'upload/mdl_uploads',
            ]
        );

        $this->load->helper('dropzone');

        $email_template_id = get_setting('email_quote_template');
        $email_template    = '{}';

        if ($email_template_id) {
            $email_template = json_encode($this->mdl_email_templates->get_by_id($email_template_id), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }

        // Get all custom fields
        $custom_fields = [];
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $quote = $this->mdl_quotes->get_by_id($quote_id);

        // Determine default sender email: use smtp_mail_from setting if configured, otherwise fall back to user email
        $default_from_email = get_setting('smtp_mail_from');
        if (empty($default_from_email)) {
            $default_from_email = $quote->user_email;
        }

        $this->layout->set(
            [
                'selected_email_template' => $email_template_id,
                'selected_pdf_template'   => get_setting('pdf_quote_template'),
                'email_templates'         => $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result(),
                'email_template'          => $email_template,
                'custom_fields'           => $custom_fields,
                'pdf_templates'           => $this->mdl_templates->get_quote_templates(),
                'quote'                   => $quote,
                'default_from_email'      => $default_from_email,
            ]
        );
        $this->layout->buffer('content', 'mailer/quote');
        $this->layout->render();
    }

    /**
     * @param $invoice_id
     */
    public function send_invoice(string $invoice_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoices/view/' . $invoice_id);
        }

        if ( ! $this->mailer_configured) {
            return;
        }

        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        if (! $invoice) {
            show_404();
        }

        $pdf_template = $this->input->post('pdf_template', true);
        $subject      = $this->input->post('subject');
        $body         = $this->input->post('body');

        $to = $invoice->client_email;

        $from_email = get_setting('smtp_mail_from');
        if (empty($from_email)) {
            $from_email = $invoice->user_email;
        }
        $from_name = get_setting('smtp_mail_from_name');
        if (empty($from_name)) {
            $from_name = $invoice->user_name;
        }
        $from = [$from_email, $from_name];

        $this->load->helper('html_sanitizer');

        if (is_plain_text($body)) {
            $body = nl2br($body);
        }

        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');

        // Authorize CC/BCC addresses to internal domain
        if (!$this->authorize_cc_bcc_addresses($cc, $bcc)) {
            $this->session->set_flashdata('alert_danger', 'CC and BCC addresses must be internal (authorized domain only)');
            redirect('mailer/invoice/' . $invoice_id);
        }

        $this->load->model('upload/mdl_uploads');
        $attachment_files = $this->mdl_uploads->get_invoice_uploads($invoice_id);

        $this->mdl_invoices->generate_invoice_number_if_applicable($invoice_id);

        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_invoices->mark_sent($invoice_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('invoices/view/' . $invoice_id);
        }

        redirect('mailer/invoice/' . $invoice_id);
    }

    /**
     * @param $quote_id
     */
    public function send_quote(string $quote_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('quotes/view/' . $quote_id);
        }

        if ( ! $this->mailer_configured) {
            return;
        }

        $this->load->model('quotes/mdl_quotes');
        $quote = $this->mdl_quotes->get_by_id($quote_id);
        if (! $quote) {
            show_404();
        }

        $pdf_template = $this->input->post('pdf_template');
        $subject      = $this->input->post('subject');
        $body         = $this->input->post('body');

        $to = $quote->client_email;

        $from_email = get_setting('smtp_mail_from');
        if (empty($from_email)) {
            $from_email = $quote->user_email;
        }
        $from_name = get_setting('smtp_mail_from_name');
        if (empty($from_name)) {
            $from_name = $quote->user_name;
        }
        $from = [$from_email, $from_name];

        $this->load->helper('html_sanitizer');

        if (is_plain_text($body)) {
            $body = nl2br($body);
        }

        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');

        // Authorize CC/BCC addresses to internal domain
        if (!$this->authorize_cc_bcc_addresses($cc, $bcc)) {
            $this->session->set_flashdata('alert_danger', 'CC and BCC addresses must be internal (authorized domain only)');
            redirect('mailer/quote/' . $quote_id);
        }

        $this->load->model('upload/mdl_uploads');
        $attachment_files = $this->mdl_uploads->get_quote_uploads($quote_id);

        $this->mdl_quotes->generate_quote_number_if_applicable($quote_id);

        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_quotes->mark_sent($quote_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));

            redirect('quotes/view/' . $quote_id);
        }

        redirect('mailer/quote/' . $quote_id);
    }

    /**
     * Authorize CC and BCC addresses to the internal domain.
     *
     * CC/BCC addresses must match the organization's internal domain to prevent
     * information disclosure (sending branded PDFs to unauthorized recipients).
     * The internal domain is determined by:
     * 1. mailer_internal_domain setting (if set)
     * 2. Domain extracted from smtp_mail_from setting
     *
     * @param string $cc  Comma-separated CC addresses
     * @param string $bcc Comma-separated BCC addresses
     * @return bool True if all addresses authorized, false otherwise
     */
    private function authorize_cc_bcc_addresses(string $cc, string $bcc): bool
    {
        // Empty CC/BCC always authorized
        $cc = trim($cc);
        $bcc = trim($bcc);
        if (empty($cc) && empty($bcc)) {
            return true;
        }

        // Get internal domain from settings
        $internal_domain = get_setting('mailer_internal_domain');
        if (empty($internal_domain)) {
            // Fallback: extract domain from smtp_mail_from
            $smtp_from = get_setting('smtp_mail_from');
            if (!empty($smtp_from) && strpos($smtp_from, '@') !== false) {
                $internal_domain = substr($smtp_from, strpos($smtp_from, '@') + 1);
            }
        }

        // If no domain configured, authorize all (lenient default)
        if (empty($internal_domain)) {
            return true;
        }

        // Validate CC addresses
        if (!empty($cc)) {
            $cc_addresses = array_map('trim', explode(',', $cc));
            foreach ($cc_addresses as $addr) {
                if (!$this->is_address_authorized($addr, $internal_domain)) {
                    return false;
                }
            }
        }

        // Validate BCC addresses
        if (!empty($bcc)) {
            $bcc_addresses = array_map('trim', explode(',', $bcc));
            foreach ($bcc_addresses as $addr) {
                if (!$this->is_address_authorized($addr, $internal_domain)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if an email address belongs to the authorized internal domain.
     *
     * @param string $address Email address to check
     * @param string $internal_domain Authorized domain (e.g., "company.internal")
     * @return bool True if address matches domain
     */
    private function is_address_authorized(string $address, string $internal_domain): bool
    {
        // RFC email syntax validation (already done by form validation)
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Extract domain from email and compare
        if (strpos($address, '@') === false) {
            return false;
        }

        $address_domain = substr($address, strpos($address, '@') + 1);
        return strcasecmp($address_domain, $internal_domain) === 0;
    }
}
