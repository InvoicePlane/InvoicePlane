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
 * Class View
 */
class View extends Base_Controller
{
    /**
     * @param $invoice_url_key
     */
    public function invoice($invoice_url_key = '')
    {
        if (!$invoice_url_key) {
            show_404();
        }

        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() != 1) {
            show_404();
        }

        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');
        $this->load->model('payment_methods/mdl_payment_methods');

        $invoice = $invoice->row();

        if ($this->session->userdata('user_type') <> 1 and $invoice->invoice_status_id == 2) {
            $this->mdl_invoices->mark_viewed($invoice->invoice_id);
        }

        $payment_method = $this->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }

        // Attachments
        $attachments = $this->get_attachments($invoice_url_key);

        $is_overdue = ($invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time() ? true : false);

        $data = array(
            'invoice' => $invoice,
            'items' => $this->mdl_items->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_url_key' => $invoice_url_key,
            'flash_message' => $this->session->flashdata('flash_message'),
            'payment_method' => $payment_method,
            'is_overdue' => $is_overdue,
            'attachments' => $attachments,
        );

        $this->load->view('invoice_templates/public/' . get_setting('public_invoice_template') . '.php', $data);
    }

    private function get_attachments($key)
    {
        $path = UPLOADS_FOLDER . '/customer_files';
        $files = scandir($path);
        $attachments = array();

        if ($files !== false) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && strpos($file, $key) !== false) {
                    $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                    $obj['fullname'] = $file;
                    $obj['size'] = filesize($path . '/' . $file);
                    $obj['fullpath'] = base_url($path . '/' . $file);
                    $attachments[] = $obj;
                }
            }
        }

        return $attachments;
    }

    /**
     * @param $invoice_url_key
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_invoice_pdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if (!$invoice_template) {
                $invoice_template = get_setting('pdf_invoice_template');
            }

            $this->load->helper('pdf');

            generate_invoice_pdf($invoice->invoice_id, $stream, $invoice_template, 1);
        }
    }

    /**
     * @param $invoice_url_key
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_sumex_pdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if ($invoice->sumex_id == NULL) {
                show_404();
                return;
            }

            if (!$invoice_template) {
                $invoice_template = get_setting('pdf_invoice_template');
            }

            $this->load->helper('pdf');

            generate_invoice_sumex($invoice->invoice_id);
        }
    }

    /**
     * @param $quote_url_key
     */
    public function quote($quote_url_key = '')
    {
        if (!$quote_url_key) {
            show_404();
        }

        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('quote_url_key', $quote_url_key)->get();

        if ($quote->num_rows() != 1) {
            show_404();
        }

        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');

        $quote = $quote->row();

        if ($this->session->userdata('user_type') <> 1 and $quote->quote_status_id == 2) {
            $this->mdl_quotes->mark_viewed($quote->quote_id);
        }

        // Attachments
        $attachments = $this->get_attachments($quote_url_key);
        /*$path = '/uploads/customer_files';
        $files = scandir(getcwd() . $path);
        $attachments = array();

        if ($files !== false) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && strpos($file, $quote_url_key) !== false) {
                    $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                    $obj['fullname'] = $file;
                    $obj['size'] = filesize($path . '/' . $file);
                    $obj['fullpath'] = base_url($path . '/' . $file);
                    $attachments[] = $obj;
                }
            }
        }*/

        $is_expired = (strtotime($quote->quote_date_expires) < time() ? true : false);

        $data = array(
            'quote' => $quote,
            'items' => $this->mdl_quote_items->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_url_key' => $quote_url_key,
            'flash_message' => $this->session->flashdata('flash_message'),
            'is_expired' => $is_expired,
            'attachments' => $attachments,
        );

        $this->load->view('quote_templates/public/' . get_setting('public_quote_template') . '.php', $data);
    }

    /**
     * @param $quote_url_key
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_quote_pdf($quote_url_key, $stream = true, $quote_template = null)
    {
        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('quote_url_key', $quote_url_key)->get();

        if ($quote->num_rows() == 1) {
            $quote = $quote->row();

            if (!$quote_template) {
                $quote_template = get_setting('pdf_quote_template');
            }

            $this->load->helper('pdf');

            generate_quote_pdf($quote->quote_id, $stream, $quote_template);
        }
    }

    /**
     * @param $quote_url_key
     */
    public function approve_quote($quote_url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->approve_quote_by_key($quote_url_key);
        email_quote_status($this->mdl_quotes->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, "approved");

        redirect('guest/view/quote/' . $quote_url_key);
    }

    /**
     * @param $quote_url_key
     */
    public function reject_quote($quote_url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->reject_quote_by_key($quote_url_key);
        email_quote_status($this->mdl_quotes->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, "rejected");

        redirect('guest/view/quote/' . $quote_url_key);
    }

}
