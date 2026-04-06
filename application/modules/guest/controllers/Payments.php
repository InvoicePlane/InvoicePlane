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
        // Security: Use Query Builder with proper join and where_in for safe parameterization
        // This replaces the previous string interpolation pattern with proper CI3 Query Builder methods
        if (empty($this->user_clients)) {
            // No clients assigned - show no payments
            $this->mdl_payments->where('1 = 0'); // Always false condition
        } else {
            // Load the invoices model to ensure proper join
            $this->load->model('invoices/mdl_invoices');
            // Use where_in with the client_id column from the already-joined ip_invoices table
            // The mdl_payments model's default_join() method already joins ip_invoices
            $this->mdl_payments->where_in('ip_invoices.client_id', $this->user_clients);
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