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

class Mailer extends Admin_Controller
{
    private $mailer_configured;

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

    public function invoice($invoice_id)
    {
        if (!$this->mailer_configured) return;

        $this->load->model('invoices/mdl_templates');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('email_templates/mdl_email_templates');
        $this->load->helper('template');

        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

        $email_template_id = select_email_invoice_template($invoice);

        if ($email_template_id) {
            $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();

            $this->layout->set('email_template', json_encode($email_template->row()));
        } else {
            $this->layout->set('email_template', '{}');
        }

        $this->layout->set('selected_pdf_template', select_pdf_invoice_template($invoice));
        $this->layout->set('selected_email_template', $email_template_id);
        $this->layout->set('email_templates', $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result());
        $this->layout->set('invoice', $invoice);
        $this->layout->set('pdf_templates', $this->mdl_templates->get_invoice_templates());
        $this->layout->buffer('content', 'mailer/invoice');
        $this->layout->render();
    }

    public function quote($quote_id)
    {
        if (!$this->mailer_configured) return;

        $this->load->model('invoices/mdl_templates');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        $this->load->model('email_templates/mdl_email_templates');

        $email_template_id = $this->mdl_settings->setting('email_quote_template');

        if ($email_template_id) {
            $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();

            $this->layout->set('email_template', json_encode($email_template->row()));
        } else {
            $this->layout->set('email_template', '{}');
        }
        $this->layout->set('selected_pdf_template', $this->mdl_settings->setting('pdf_quote_template'));
        $this->layout->set('selected_email_template', $email_template_id);
        $this->layout->set('email_templates', $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result());
        $this->layout->set('quote', $this->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row());
        $this->layout->set('pdf_templates', $this->mdl_templates->get_quote_templates());
        $this->layout->buffer('content', 'mailer/quote');
        $this->layout->render();

    }

    public function send_invoice($invoice_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoices');
        }

        if (!$this->mailer_configured) return;

        $this->load->model('upload/mdl_uploads');
        $from = array(
            $this->input->post('from_email'),
            $this->input->post('from_name')
        );
        $pdf_template = $this->input->post('pdf_template');
        $to = $this->input->post('to_email');
        $subject = $this->input->post('subject');

        if (strlen($this->input->post('body')) != strlen(strip_tags($this->input->post('body')))) {
            $body = htmlspecialchars_decode($this->input->post('body'));
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')));
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_invoice_uploads($invoice_id);

        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_invoices->mark_sent($invoice_id);

            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('invoices/view/' . $invoice_id);
        } else {
            redirect('mailer/invoice/' . $invoice_id);
        }
    }

    public function send_quote($quote_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('quotes');
        }

        if (!$this->mailer_configured) return;

        $this->load->model('upload/mdl_uploads');
        $from = array(
            $this->input->post('from_email'),
            $this->input->post('from_name')
        );
        $pdf_template = $this->input->post('pdf_template');
        $to = $this->input->post('to_email');
        $subject = $this->input->post('subject');

        if (strlen($this->input->post('body')) != strlen(strip_tags($this->input->post('body')))) {
            $body = htmlspecialchars_decode($this->input->post('body'));
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')));
        }

        $cc = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $attachment_files = $this->mdl_uploads->get_quote_uploads($quote_id);

        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->mdl_quotes->mark_sent($quote_id);

            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));

            redirect('quotes/view/' . $quote_id);
        } else {
            redirect('mailer/quote/' . $quote_id);
        }
    }
}
