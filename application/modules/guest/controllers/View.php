<?php

if (! defined('BASEPATH')) {
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

        $this->load->model(
            [
                'invoices/mdl_items',
                'invoices/mdl_invoice_tax_rates',
                'payment_methods/mdl_payment_methods',
                'custom_fields/mdl_custom_fields',
                'upload/mdl_uploads',
            ]
        );
        $this->load->helper('template');

        $invoice = $invoice->row();

        if ($this->session->userdata('user_type') != 1 && $invoice->invoice_status_id == 2) {
            $this->mdl_invoices->mark_viewed($invoice->invoice_id);
        }

        $payment_method = $this->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }

        // Get all custom fields
        $custom_fields = [
            'invoice' => $this->mdl_custom_fields->get_values_for_fields('mdl_invoice_custom', $invoice->invoice_id),
            'client'  => $this->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $invoice->client_id),
            'user'    => $this->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $invoice->user_id),
        ];

        // Attachments
        $attachments = $this->get_attachments($invoice_url_key);

        $is_overdue = ($invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time());

        $data = [
            'invoice'            => $invoice,
            'items'              => $this->mdl_items->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_tax_rates'  => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_url_key'    => $invoice_url_key,
            'flash_message'      => $this->session->flashdata('flash_message'),
            'payment_method'     => $payment_method,
            'is_overdue'         => $is_overdue,
            'attachments'        => $attachments,
            'custom_fields'      => $custom_fields,
            'legacy_calculation' => config_item('legacy_calculation'),
        ];

        $this->load->view('invoice_templates/public/' . get_setting('public_invoice_template') . '.php', $data);
    }

    /**
     * Retail since 1.6.3
     * @param $url_key
     * @return array
     */
    private function get_attachments($url_key)
    {
        $query = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $url_key . "'");

        $names = [];

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $names[] =
                [
                    'name'     => $row->file_name_original,
                    'fullname' => $row->file_name_new,
                    'size'     => filesize(UPLOADS_CFILES_FOLDER . $row->file_name_new),
                ];
            }
        }

        return $names;
    }

    /**
     * @param $invoice_url_key
     * @param bool $stream
     */
    public function generate_invoice_pdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if (! $invoice_template) {
                $this->load->helper('template');
                $invoice_template = select_pdf_invoice_template($invoice);
            }

            $this->load->helper('pdf');

            generate_invoice_pdf($invoice->invoice_id, $stream, $invoice_template, 1);
        }
    }

    /**
     * @param $invoice_url_key
     * @param bool $stream
     */
    public function generate_sumex_pdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if ($invoice->sumex_id == null) {
                show_404();
            }

            if (! $invoice_template) {
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
        if (! $quote_url_key) {
            show_404();
        }

        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('quote_url_key', $quote_url_key)->get();

        if ($quote->num_rows() != 1) {
            show_404();
        }

        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');

        $quote = $quote->row();

        if ($this->session->userdata('user_type') != 1 && $quote->quote_status_id == 2) {
            $this->mdl_quotes->mark_viewed($quote->quote_id);
        }

        // Get all custom fields
        $custom_fields =
        [
            'quote'  => $this->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
            'client' => $this->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
            'user'   => $this->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
        ];

        // Attachments
        $attachments = $this->get_attachments($quote_url_key);

        $is_expired = (strtotime($quote->quote_date_expires) < time());

        $data =
        [
            'quote'              => $quote,
            'items'              => $this->mdl_quote_items->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_tax_rates'    => $this->mdl_quote_tax_rates->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_url_key'      => $quote_url_key,
            'flash_message'      => $this->session->flashdata('flash_message'),
            'is_expired'         => $is_expired,
            'attachments'        => $attachments,
            'custom_fields'      => $custom_fields,
            'legacy_calculation' => config_item('legacy_calculation'),
        ];

        $this->load->view('quote_templates/public/' . get_setting('public_quote_template') . '.php', $data);
    }

    /**
     * @param $quote_url_key
     * @param bool $stream
     */
    public function generate_quote_pdf($quote_url_key, $stream = true, $quote_template = null)
    {
        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('quote_url_key', $quote_url_key)->get()->row();

        if (! $quote) {
            show_404();
        }

        if (! $quote_template) {
            $quote_template = get_setting('pdf_quote_template');
        }

        $this->load->helper('pdf');

        generate_quote_pdf($quote->quote_id, $stream, $quote_template);
    }

    /**
     * @param $quote_url_key
     */
    public function approve_quote($quote_url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->approve_quote_by_key($quote_url_key);
        email_quote_status($this->mdl_quotes->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, 'approved');

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
        email_quote_status($this->mdl_quotes->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, 'rejected');

        redirect('guest/view/quote/' . $quote_url_key);
    }
}
