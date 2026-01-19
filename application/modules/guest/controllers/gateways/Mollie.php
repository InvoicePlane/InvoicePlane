<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mollie extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_create_client();
    }

    /**
     * Create the payment on Mollie and redirect user to payment page.
     *
     * @param string $invoice_url_key
     *
     * @return void
     */
    public function mollie_create_payment($invoice_url_key)
    {
        // Check if the invoice exists and is billable
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

        // Create the payment
        $mollie_response = $this->lib_mollie->createPayment([
            'amount'      => number_format($invoice->invoice_balance, 2, '.', ''),
            'currency'    => get_setting('gateway_mollie_currency'),
            'description' => trans('invoice') . ' #' . $invoice->invoice_number,
            'return_url'  => site_url('guest/gateways/mollie/callback/' . $invoice_url_key),
            'invoice_id'  => $invoice->invoice_id,
            'invoice_key' => $invoice_url_key,
        ]);

        // Handle the payment creation
        if ($mollie_response['status']) {
            // Save the transaction reference in session for callback
            $this->session->set_userdata('mollie_transaction_ref', $mollie_response['reference']);

            // Redirect to Mollie payment page
            redirect($mollie_response['redirect_url']);
        } else {
            // Record the failed transaction
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => false,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'mollie',
                'merchant_response'            => 'Payment creation failed: ' . ($mollie_response['message'] ?? 'Unknown error'),
                'merchant_response_reference'  => 'none',
            ]);

            // Set error message
            $this->session->set_flashdata('alert_error', trans('online_payment_failed'));
            redirect(site_url('guest/payment_information/form/' . $invoice_url_key . '/mollie'));
        }
    }

    /**
     * The callback endpoint called by Mollie after payment.
     *
     * @param string $invoice_url_key
     *
     * @return void
     */
    public function callback($invoice_url_key)
    {
        try {
            // Get transaction reference from session
            $transaction_ref = $this->session->userdata('mollie_transaction_ref');

            if ( ! $transaction_ref) {
                throw new Exception('No transaction reference found');
            }

            // Fetch payment details from Mollie
            $mollie_response = $this->lib_mollie->getPayment($transaction_ref);

            if ( ! $mollie_response['status']) {
                throw new Exception('Failed to fetch payment details');
            }

            $payment = $mollie_response['response'];

            // Retrieve the invoice
            $this->load->model('invoices/mdl_invoices');
            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

            // Check if payment was successful
            $paid = $payment->isSuccessful();

            if ($paid) {
                // Save the payment
                $this->load->model('payments/mdl_payments');
                $this->mdl_payments->save(null, [
                    'invoice_id'        => $invoice->invoice_id,
                    'payment_date'      => date('Y-m-d'),
                    'payment_amount'    => $payment->getData()['amount']['value'] ?? $invoice->invoice_balance,
                    'payment_method_id' => get_setting('gateway_mollie_payment_method'),
                    'payment_note'      => trans('online_payment_intent_id') . ': ' . $transaction_ref,
                ]);
            }

            // Record merchant response
            $response_msg = $paid ? 'Payment successful'
                                  : 'Payment ' . ($payment->getData()['status'] ?? 'failed');

            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => (int) $paid,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'mollie',
                'merchant_response'            => $response_msg,
                'merchant_response_reference'  => 'transaction_ref: ' . $transaction_ref,
            ]);

            // Notify user
            if ($paid) {
                $this->session->set_flashdata(
                    'alert_success',
                    sprintf(trans('online_payment_successful'), '#' . $invoice->invoice_number)
                );
            } else {
                $this->session->set_flashdata(
                    'alert_info',
                    trans('online_payment_failed')
                );
            }

            // Clean up session
            $this->session->unset_userdata('mollie_transaction_ref');
        } catch (Error|Exception|ErrorException $e) {
            // Log the error
            log_message('error', 'Mollie callback exception: ' . $e->getMessage());

            // Record error in merchant responses
            if (isset($invoice)) {
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice->invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'mollie',
                    'merchant_response'            => 'Error: ' . $e->getMessage(),
                    'merchant_response_reference'  => 'none',
                ]);
            }

            $this->session->set_flashdata('alert_error', trans('online_payment_error'));
        } finally {
            // Redirect to invoice
            redirect('guest/view/invoice/' . $invoice_url_key);
        }
    }

    protected function _create_client(): void
    {
        $this->load->library('crypt');

        // Load the Mollie library
        $this->load->library('gateways/MollieLib', [
            'api_key' => $this->crypt->decode(get_setting('gateway_mollie_apiKey')),
        ], 'lib_mollie');
    }
}
