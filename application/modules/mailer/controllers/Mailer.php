<?php

if (! defined('BASEPATH'))
{
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
    private $mailer_configured;
    private $errors = [];

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('mailer');

        $this->mailer_configured = mailer_configured();

        if (! $this->mailer_configured)
        {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    /**
     * @param $invoice_id
     */
    public function invoice($invoice_id)
    {
        if (! $this->mailer_configured)
        {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('email_templates/mdl_email_templates');
        $this->load->helper('template');

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        $email_template_id = select_email_invoice_template($invoice);

        if ($email_template_id)
        {
            $email_template = $this->mdl_email_templates->get_by_id($email_template_id);
            $this->layout->set('email_template', json_encode($email_template));
        }
        else
        {
            $this->layout->set('email_template', '{}');
        }

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = [];
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table)
        {
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
        if (! $this->mailer_configured)
        {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        $this->load->model('email_templates/mdl_email_templates');

        $email_template_id = get_setting('email_quote_template');

        if ($email_template_id)
        {
            $email_template = $this->mdl_email_templates->get_by_id($email_template_id);
            $this->layout->set('email_template', json_encode($email_template));
        }
        else
        {
            $this->layout->set('email_template', '{}');
        }

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = [];
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table)
        {
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
        if ($this->input->post('btn_cancel'))
        {
            redirect('invoices/view/' . $invoice_id);
        }

        if (! $this->mailer_configured)
        {
            return;
        }

        $to   = $this->input->post('to_email', true);
        $from = $this->input->post('from_email', true);

        if (! filter_var($to, FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = 'to_email';
        }

        if (! filter_var($from, FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = 'from_email';
        }

        $this->check_mail_errors('mailer/invoice/' . $invoice_id);

        $from         = [$from, $this->input->post('from_name')];
        $pdf_template = $this->input->post('pdf_template', true);
        $subject      = $this->input->post('subject');
        $body         = $this->input->post('body');

        if (strlen($body) != strlen(strip_tags($body)))
        {
            $body = htmlspecialchars_decode($body, ENT_COMPAT);
        }
        else
        {
            $body = htmlspecialchars_decode(nl2br($body), ENT_COMPAT);
        }

        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');

        $this->load->model('upload/mdl_uploads');
        $attachment_files = $this->mdl_uploads->get_invoice_uploads($invoice_id);

        $this->mdl_invoices->generate_invoice_number_if_applicable($invoice_id);

        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files))
        {
            $this->mdl_invoices->mark_sent($invoice_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('invoices/view/' . $invoice_id);
        }
        redirect('mailer/invoice/' . $invoice_id);
    }

    /**
     * @param $quote_id
     */
    public function send_quote($quote_id)
    {
        if ($this->input->post('btn_cancel'))
        {
            redirect('quotes/view/' . $quote_id);
        }

        if (! $this->mailer_configured)
        {
            return;
        }

        $to  = $this->input->post('to_email');
        $from = $this->input->post('from_email');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = 'to_email';
        }

        if (! filter_var($from, FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = 'from_email';
        }

        $this->check_mail_errors('mailer/quote/' . $quote_id);

        $from         = [$from, $this->input->post('from_name')];
        $pdf_template = $this->input->post('pdf_template');
        $subject      = $this->input->post('subject');

        if (strlen($this->input->post('body')) != strlen(strip_tags($this->input->post('body'))))
        {
            $body = htmlspecialchars_decode($this->input->post('body'), ENT_COMPAT);
        }
        else
        {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')), ENT_COMPAT);
        }

        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');

        $this->load->model('upload/mdl_uploads');
        $attachment_files = $this->mdl_uploads->get_quote_uploads($quote_id);

        $this->mdl_quotes->generate_quote_number_if_applicable($quote_id);

        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files))
        {
            $this->mdl_quotes->mark_sent($quote_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));

            redirect('quotes/view/' . $quote_id);
        }
        redirect('mailer/quote/' . $quote_id);
    }

    /**
     * @param str $redirect
     */
    public function check_mail_errors($redirect)
    {
        if ($this->errors)
        {
            foreach($this->errors as $i => $e)
            {
                $this->errors[$i] = strtr(trans('form_validation_valid_email'), ['{field}' => trans($e)]);
            }
            $this->session->set_flashdata('alert_error', implode('<br>', $this->errors));
            redirect($redirect);
        }
    }

}
