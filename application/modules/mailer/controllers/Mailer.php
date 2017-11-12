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
 * Class Mailer
 */
class Mailer extends Admin_Controller
{
    private $mailer_configured;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('mailer');

        $this->mailer_configured = mailer_configured();

        if ($this->mailer_configured == false) {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    /**
     * @param $invoice_id
     */
    public function invoice($invoice_id)
    {
        if (!$this->mailer_configured) {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('email_templates/mdl_email_templates');
        $this->load->helper('template');

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        $email_template_id = select_email_invoice_template($invoice);

        if ($email_template_id) {
            $email_template = $this->mdl_email_templates->get_by_id($email_template_id);
            $this->layout->set('email_template', json_encode($email_template));
        } else {
            $this->layout->set('email_template', '{}');
        }

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = array();
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $this->layout->set('selected_pdf_template', select_pdf_invoice_template($invoice));
        $this->layout->set('selected_email_template', $email_template_id);
        $this->layout->set('email_templates', $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result());
        $this->layout->set('invoice', $invoice);
        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('pdf_templates', $this->mdl_templates->get_invoice_templates());
        $this->layout->buffer('content', 'mailer/invoice');
        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function quote($quote_id)
    {
        if (!$this->mailer_configured) {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        $this->load->model('email_templates/mdl_email_templates');

        $email_template_id = get_setting('email_quote_template');

        if ($email_template_id) {
            $email_template = $this->mdl_email_templates->get_by_id($email_template_id);
            $this->layout->set('email_template', json_encode($email_template));
        } else {
            $this->layout->set('email_template', '{}');
        }

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = array();
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $this->layout->set('selected_pdf_template', get_setting('pdf_quote_template'));
        $this->layout->set('selected_email_template', $email_template_id);
        $this->layout->set('email_templates', $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result());
        $this->layout->set('quote', $this->mdl_quotes->get_by_id($quote_id));
        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('pdf_templates', $this->mdl_templates->get_quote_templates());
        $this->layout->buffer('content', 'mailer/quote');
        $this->layout->render();

    }

    /**
     * @param $invoice_id
     */
    public function send_invoice($invoice_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoices/view/' . $invoice_id);
        }

        if (!$this->mailer_configured) {
            return;
        }

        $to = $this->input->post('to_email');

        if (empty($to)) {
            $this->session->set_flashdata('alert_danger', trans('email_to_address_missing'));
            redirect('mailer/invoice/' . $invoice_id);
        }

        $this->load->model('upload/mdl_uploads');
        $from = array(
            $this->input->post('from_email'),
            $this->input->post('from_name')
        );

        $pdf_template = $this->input->post('pdf_template');
        $subject = $this->input->post('subject');
        $body = $this->input->post('body');

        if (strlen($body) != strlen(strip_tags($body))) {
            $body = htmlspecialchars_decode($body);
        } else {
            $body = htmlspecialchars_decode(nl2br($body));
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_invoice_uploads($invoice_id);

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        if (!empty($invoice)) {
            if ($invoice->invoice_status_id == 1) {
                // Generate new invoice number if applicable
                if (get_setting('generate_invoice_number_for_draft') == 0) {
                    $invoice_number = $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id);

                    // Set new invoice number and save
                    $this->db->where('invoice_id', $invoice_id);
                    $this->db->set('invoice_number', $invoice_number);
                    $this->db->update('ip_invoices');
                }
            }
        }

        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_invoices->mark_sent($invoice_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('invoices/view/' . $invoice_id);
        } else {
            redirect('mailer/invoice/' . $invoice_id);
        }
    }

    /**
     * @param $quote_id
     */
    public function send_quote($quote_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('quotes/view/' . $quote_id);
        }

        if (!$this->mailer_configured) {
            return;
        }

        $to = $this->input->post('to_email');

        if (empty($to)) {
            $this->session->set_flashdata('alert_danger', trans('email_to_address_missing'));
            redirect('mailer/quote/' . $quote_id);
        }

        $this->load->model('upload/mdl_uploads');
        $from = array(
            $this->input->post('from_email'),
            $this->input->post('from_name')
        );

        $pdf_template = $this->input->post('pdf_template');
        $subject = $this->input->post('subject');

        if (strlen($this->input->post('body')) != strlen(strip_tags($this->input->post('body')))) {
            $body = htmlspecialchars_decode($this->input->post('body'));
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')));
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_quote_uploads($quote_id);

        $quote = $this->mdl_quotes->get_by_id($quote_id);

        if (!empty($quote)) {
            if ($quote->quote_status_id == 1) {
                // Generate new invoice number if applicable
                if (get_setting('generate_quote_number_for_draft') == 0) {
                    $quote_number = $this->mdl_quotes->get_quote_number($quote->invoice_group_id);

                    // Set new invoice number and save
                    $this->db->where('quote_id', $quote_id);
                    $this->db->set('quote_number', $quote_number);
                    $this->db->update('ip_quotes');
                }
            }
        }

        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_quotes->mark_sent($quote_id);

            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));

            redirect('quotes/view/' . $quote_id);
        } else {
            redirect('mailer/quote/' . $quote_id);
        }
    }

}
