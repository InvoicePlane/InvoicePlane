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
class Invoices extends Guest_Controller
{
    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    public function index(): void
    {
        // Display open invoices by default
        redirect('guest/invoices/status/open');
    }

    /**
     * @param int $page
     */
    public function status(string $status = 'open', $page = 0): void
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'all':
                $this->mdl_invoices->guest_visible();
                break;
            case 'paid':
                $this->mdl_invoices->is_paid();
                break;
            case 'overdue':
                $this->mdl_invoices->is_overdue();
                break;
            default:
                $this->mdl_invoices->is_open();
                break;
        }

        $this->mdl_invoices->where_in('ip_invoices.client_id', $this->user_clients);
        $this->mdl_invoices->paginate(site_url('guest/invoices/status/' . $status), $page);

        $invoices = $this->mdl_invoices->result();

        $this->layout->set(
            [
                'invoices'               => $invoices,
                'status'                 => $status,
                'enable_online_payments' => get_setting('enable_online_payments'),
            ]
        );

        $this->layout->buffer('content', 'guest/invoices_index');
        $this->layout->render('layout_guest');
    }

    /**
     * @param $invoice_id
     */
    public function view($invoice_id): void
    {
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();

        if ( ! $invoice) {
            show_404();
        }

        $this->mdl_invoices->mark_viewed($invoice->invoice_id);

        $this->load->model(
            [
                'invoices/mdl_items',
                'invoices/mdl_invoice_tax_rates',
                'upload/mdl_uploads',
            ]
        );

        $this->load->helper('dropzone');

        $this->layout->set(
            [
                'invoice_id'             => $invoice_id,
                'invoice'                => $invoice,
                'items'                  => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
                'invoice_tax_rates'      => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
                'enable_online_payments' => get_setting('enable_online_payments'),
                'legacy_calculation'     => config_item('legacy_calculation'),
            ]
        );

        $this->layout->buffer('content', 'guest/invoices_view');
        $this->layout->render('layout_guest');
    }

    /**
     * @param      $invoice_id
     * @param bool $stream
     */
    public function generate_pdf($invoice_id, $stream = true, $invoice_template = null): void
    {
        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();

        if ( ! $invoice) {
            show_404();
        }

        $this->mdl_invoices->mark_viewed($invoice_id);

        $this->load->helper('pdf');

        generate_invoice_pdf($invoice_id, $stream, $invoice_template, true);
    }

    /**
     * @param      $invoice_id
     * @param bool $stream
     */
    public function generate_sumex_pdf($invoice_id, $stream = true, $invoice_template = null): void
    {
        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();

        if ( ! $invoice) {
            show_404();
        }

        $this->mdl_invoices->mark_viewed($invoice_id);

        $this->load->helper('pdf');

        generate_invoice_sumex($invoice_id, $stream, $invoice_template, true);
    }
}
