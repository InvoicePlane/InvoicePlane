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

class Mailer extends Admin_Controller {

    private $mailer_configured;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('mailer');

        $this->mailer_configured = mailer_configured();

        $this->load->helper('template');
    }

    public function invoice($invoice_id)
    {
        if ($this->mailer_configured == TRUE)
        {
            if ($this->input->post('btn_send'))
            {
                $from             = ($this->input->post('from_name')) ? array($this->input->post('from_email'), $this->input->post('from_name')) : $this->input->post('from_email');
                $invoice_template = $this->input->post('invoice_template');
                $to               = $this->input->post('to_email');
                $subject          = $this->input->post('subject');
                $body             = ($this->input->post('body')) ? : ' ';
                $cc               = $this->input->post('to_cc');
                $bcc              = $this->input->post('to_bcc');

                if (email_invoice($invoice_id, $invoice_template, $from, $to, $subject, $body, $cc, $bcc))
                {
                    $this->mdl_invoices->mark_sent($invoice_id);
                    
                    $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));

                    redirect('dashboard');
                }
                else
                {
                    redirect('mailer/invoice/' . $invoice_id);
                }
            }

            $this->load->model('invoices/mdl_templates');
            $this->load->model('invoices/mdl_invoices');
            $this->load->model('email_templates/mdl_email_templates');

            $invoice = $this->mdl_invoices->where('fi_invoices.invoice_id', $invoice_id)->get()->row();
            
            $this->load->helper('template');
            $selected_pdf_template = select_pdf_invoice_template($invoice);
            $selected_email_template = select_email_invoice_template($invoice);
            
            if ($selected_email_template)
            {
                $email_template = $this->mdl_email_templates->where('email_template_id', $selected_email_template)->get();

                if ($email_template->num_rows())
                {
                    $this->layout->set('body', $email_template->row()->email_template_body);
                }
                else
                {
                    $this->layout->set('body', '');
                }
            }
            else
            {
                $this->layout->set('body', '');
            }
            
            $this->layout->set('selected_pdf_template', $selected_pdf_template);
            $this->layout->set('selected_email_template', $selected_email_template);
            $this->layout->set('email_templates', $this->mdl_email_templates->get()->result());
            $this->layout->set('invoice', $invoice);
            $this->layout->set('invoice_templates', $this->mdl_templates->get_invoice_templates());
            $this->layout->buffer('content', 'mailer/invoice');
            $this->layout->render();
        }
        else
        {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    public function quote($quote_id)
    {
        if ($this->mailer_configured == TRUE)
        {
            if ($this->input->post('btn_send'))
            {
                $from           = ($this->input->post('from_name')) ? array($this->input->post('from_email'), $this->input->post('from_name')) : $this->input->post('from_email');
                $quote_template = $this->input->post('quote_template');
                $to             = $this->input->post('to_email');
                $subject        = $this->input->post('subject');
                $body           = ($this->input->post('body')) ? : ' ';
                $cc             = $this->input->post('to_cc');
                $bcc            = $this->input->post('to_bcc');

                if (email_quote($quote_id, $quote_template, $from, $to, $subject, $body, $cc, $bcc))
                {
                    $this->mdl_quotes->mark_sent($quote_id);
                    
                    $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));

                    redirect('dashboard');
                }
                else
                {
                    redirect('mailer/quote/' . $quote_id);
                }
            }

            $this->load->model('invoices/mdl_templates');
            $this->load->model('quotes/mdl_quotes');
            $this->load->model('email_templates/mdl_email_templates');

            $email_template_id = $this->mdl_settings->setting('default_email_template');

            if ($email_template_id)
            {
                $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();

                if ($email_template->num_rows())
                {
                    $this->layout->set('body', $email_template->row()->email_template_body);
                }
                else
                {
                    $this->layout->set('body', '');
                }
            }
            else
            {
                $this->layout->set('body', '');
            }

            $this->layout->set('email_templates', $this->mdl_email_templates->get()->result());
            $this->layout->set('quote', $this->mdl_quotes->where('fi_quotes.quote_id', $quote_id)->get()->row());
            $this->layout->set('quote_templates', $this->mdl_templates->get_quote_templates());
            $this->layout->buffer('content', 'mailer/quote');
            $this->layout->render();
        }
        else
        {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

}

?>