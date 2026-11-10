<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'libraries/PaymentCallbackLock.php';

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function add()
    {
        $this->load->model('payments/mdl_payments');

        // Serialize concurrent payment submissions for the same invoice (CWE-362/367):
        // without this, two admin sessions can both read the pre-payment balance in
        // validate_payment_amount() and both pass before either commits, overpaying
        // the invoice. The lock is released even if save()/validation throws.
        $invoice_id    = (int) $this->input->post('invoice_id');
        $lock          = $invoice_id ? new PaymentCallbackLock($this->db) : null;
        $lock_acquired = $lock === null || $lock->acquire($invoice_id);

        try {
            if ( ! $lock_acquired) {
                $response = [
                    'success'           => 0,
                    'validation_errors' => [trans('payment_in_progress_try_again')],
                ];
            } elseif ($this->mdl_payments->run_validation()) {
                $payment_id = $this->mdl_payments->save();

                $response = [
                    'success'    => 1,
                    'payment_id' => $payment_id,
                ];
            } else {
                $this->load->helper('json_error');
                $response = [
                    'success'           => 0,
                    'validation_errors' => json_errors(),
                ];
            }
        } finally {
            $lock?->release();
        }

        $this->json_encode_ajax($response);
    }

    public function modal_add_payment()
    {
        $this->load->module('layout');
        $this->load->model('payments/mdl_payments');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_payment_custom');

        $data = [
            'payment_methods'        => $this->mdl_payment_methods->get()->result(),
            'invoice_id'             => $this->security->xss_clean($this->input->post('invoice_id')),
            'invoice_balance'        => $this->input->post('invoice_balance'),
            'invoice_payment_method' => $this->input->post('invoice_payment_method'),
            'payment_cf_exist'       => $this->security->xss_clean($this->input->post('payment_cf_exist')),
        ];

        $this->layout->load_view('payments/modal_add_payment', $data);
    }
}
