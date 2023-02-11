<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
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

        // DO NOT update the invoice and due date if setting['...'] == 1 and invoice is NOT read only
        if (get_setting('no_update_invoice_due_date_mail') == 0 && $invoice->is_read_only != 1) {
            // Save original 'invoice_date_created' and 'invoice_date_due' for an (eventually) reset later
            $this->load->model('settings/mdl_settings');
            $this->mdl_settings->save('tmp_invoice_date', $invoice->invoice_date_created); 
            $this->mdl_settings->save('tmp_due_date', $invoice->invoice_date_due); 
            $this->mdl_invoices->update_invoice_due_dates($invoice_id);
        } 

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
            // reset the original invoice_date_created & invoice_due_date if setting['...'] == 0 and invoice is NOT read only
            $this->load->model('invoices/mdl_invoices');
            $invoice = $this->mdl_invoices->get_by_id($invoice_id);
            if (get_setting('no_update_invoice_due_date_mail') == 0 && $invoice->is_read_only != 1) {
                $org_invoice_date = get_setting('tmp_invoice_date');
                $org_due_date = get_setting('tmp_due_date');
                $this->mdl_invoices->reset_invoice_due_dates($invoice_id, $org_invoice_date, $org_due_date); 									
                // delete setting 'tmp_invoice_date' and 'tmp_due_date'	
                $this->load->model('settings/mdl_settings');
                $this->mdl_settings->delete('tmp_invoice_date'); 
                $this->mdl_settings->delete('tmp_due_date'); 						
            } 
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
            $body = htmlspecialchars_decode($body, ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($body), ENT_COMPAT);
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_invoice_uploads($invoice_id);

        $this->mdl_invoices->generate_invoice_number_if_applicable($invoice_id);

        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_invoices->mark_sent($invoice_id);
            // Clean-up: delete setting 'tmp_invoice_date' and 'tmp_due_date'	
            $this->load->model('settings/mdl_settings');
            $this->mdl_settings->delete('tmp_invoice_date'); 
            $this->mdl_settings->delete('tmp_due_date'); 
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
            $body = htmlspecialchars_decode($this->input->post('body'), ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')), ENT_COMPAT);
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_quote_uploads($quote_id);

        $this->mdl_quotes->generate_quote_number_if_applicable($quote_id);

        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_quotes->mark_sent($quote_id);

            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));

            redirect('quotes/view/' . $quote_id);
        } else {
            redirect('mailer/quote/' . $quote_id);
        }
    }

}
