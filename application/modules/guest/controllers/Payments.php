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
class Payments extends Guest_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('payments/mdl_payments');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        // Security: Use a subquery with proper parameter binding instead of string concatenation
        // Get invoice IDs for this user's clients
        $invoice_ids = $this->db->select('invoice_id')
            ->from('ip_invoices')
            ->where_in('client_id', $this->user_clients)
            ->get()
            ->result_array();

        $invoice_ids = array_column($invoice_ids, 'invoice_id');

        if ( $invoice_ids !== []) {
            $this->mdl_payments->where_in('ip_payments.invoice_id', $invoice_ids);
        } else {
            // No invoices for this user, ensure no payments are returned
            $this->mdl_payments->where('1=0'); // Always false condition - no results
        }

        $this->mdl_payments->paginate(site_url('guest/payments/index'), $page);

        $payments = $this->mdl_payments->result();

        $this->layout->set(
            [
                'payments'           => $payments,
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_payments'),
                'filter_method'      => 'filter_payments',
            ]
        );

        $this->layout->buffer('content', 'guest/payments_index');
        $this->layout->render('layout_guest');
    }
}
