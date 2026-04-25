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
        // Security: Use a single join query instead of a two-step PHP-level prefetch.
        // Joining on ip_invoices avoids fetching potentially large invoice-ID lists
        // into PHP and removes the risk of hitting SQL IN-list limits.
        if ( ! empty($this->user_clients)) {
            $this->mdl_payments
                ->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id', 'inner')
                ->where_in('ip_invoices.client_id', $this->user_clients);
        } else {
            // No client access for this user — return an empty result set.
            $this->mdl_payments->where('1=0');
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
