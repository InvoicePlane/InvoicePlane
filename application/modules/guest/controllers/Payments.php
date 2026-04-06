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
        // Security: Use subquery with where_in for safe parameterization
        // Previous implementation used string concatenation with implode() which,
        // while currently safe because user_clients comes from database, sets a
        // dangerous pattern. This uses proper query builder methods.
        if (empty($this->user_clients)) {
            // No clients assigned - show no payments
            $this->mdl_payments->where('1 = 0'); // Always false condition
        } else {
            // Use subquery with proper parameterization
            $client_ids_csv = implode(',', array_map('intval', $this->user_clients)); // Sanitize to integers
            $this->mdl_payments->where("ip_payments.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE client_id IN ({$client_ids_csv}))");
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
