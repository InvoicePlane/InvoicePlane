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
            $transaction_ref = $mollie_response['reference'];

            // Save the transaction reference in session for callback (backup)
            $this->session->set_userdata('mollie_transaction_ref_' . $invoice_url_key, $transaction_ref);

            // Record successful payment creation
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => true,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'mollie',
                'merchant_response'            => 'Payment created, awaiting customer action',
                'merchant_response_reference'  => 'transaction_ref: ' . $transaction_ref,
            ]);

            // Redirect to Mollie payment page with transaction ref in URL
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
     * @param string $transaction_ref (optional, from URL parameter)
     *
     * @return void
     */
    public function callback($invoice_url_key, $transaction_ref = null)
    {
        $user_message = '';
        $alert_type   = 'error';

        try {
            // Retrieve the invoice first
            $this->load->model('invoices/mdl_invoices');
            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

            if ( ! $invoice) {
                throw new Exception('Invoice not found');
            }

            // Get transaction reference from URL parameter or session (fallback)
            if ( ! $transaction_ref) {
                $transaction_ref = $this->session->userdata('mollie_transaction_ref_' . $invoice_url_key);
            }

            if ( ! $transaction_ref) {
                throw new Exception('No transaction reference found');
            }

            // Fetch payment details from Mollie
            $mollie_response = $this->lib_mollie->getPayment($transaction_ref);

            if ( ! $mollie_response['status']) {
                throw new Exception('Failed to fetch payment details: ' . ($mollie_response['message'] ?? 'Unknown error'));
            }

            $payment      = $mollie_response['response'];
            $payment_data = $payment->getData();
            $status       = $payment_data['status'] ?? 'unknown';

            // Verify the payment belongs to this invoice
            $metadata = $payment_data['metadata'] ?? [];
            if (isset($metadata['invoice_key']) && $metadata['invoice_key'] !== $invoice_url_key) {
                throw new Exception('Payment does not belong to this invoice');
            }

            // Handle different payment states
            if ($payment->isSuccessful()) {
                // Payment successful - record it
                $this->load->model('payments/mdl_payments');
                $payment_amount = isset($payment_data['amount']['value'])
                    ? (float) $payment_data['amount']['value']
                    : (float) $invoice->invoice_balance;

                $this->mdl_payments->save(null, [
                    'invoice_id'        => $invoice->invoice_id,
                    'payment_date'      => date('Y-m-d'),
                    'payment_amount'    => $payment_amount,
                    'payment_method_id' => get_setting('gateway_mollie_payment_method'),
                    'payment_note'      => trans('online_payment_intent_id') . ': ' . $transaction_ref,
                ]);

                $response_msg = 'Payment successful: ' . $status;
                $user_message = sprintf(trans('online_payment_successful'), '#' . $invoice->invoice_number);
                $alert_type   = 'success';
                $success      = true;
            } elseif ($payment->isPending()) {
                // Payment is pending (e.g., bank transfer)
                $response_msg = 'Payment pending: ' . $status;
                $user_message = trans('online_payment_pending');
                $alert_type   = 'info';
                $success      = false;
            } elseif ($payment->isCancelled()) {
                // Payment was cancelled by user
                $response_msg = 'Payment cancelled: ' . $status;
                $user_message = trans('online_payment_cancelled');
                $alert_type   = 'info';
                $success      = false;
            } else {
                // Payment failed, expired, or other status
                $response_msg = 'Payment ' . $status;
                $user_message = trans('online_payment_failed');
                $alert_type   = 'error';
                $success      = false;
            }

            // Record merchant response
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => (int) $success,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'mollie',
                'merchant_response'            => $response_msg,
                'merchant_response_reference'  => 'transaction_ref: ' . $transaction_ref,
            ]);

            // Set user notification
            $this->session->set_flashdata('alert_' . $alert_type, $user_message);

            // Clean up session
            $this->session->unset_userdata('mollie_transaction_ref_' . $invoice_url_key);
        } catch (Error|Exception|ErrorException $e) {
            // Log the error with context
            $error_context = 'Mollie callback exception for invoice ' . $invoice_url_key;
            if (isset($transaction_ref)) {
                $error_context .= ', transaction: ' . $transaction_ref;
            }
            log_message('error', $error_context . ' - ' . $e->getMessage());

            // Record error in merchant responses
            if (isset($invoice)) {
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice->invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'mollie',
                    'merchant_response'            => 'Callback error: ' . $e->getMessage(),
                    'merchant_response_reference'  => isset($transaction_ref) ? $transaction_ref : 'none',
                ]);
            }

            // Provide more specific error message based on exception type
            $error_message = trans('online_payment_error');
            if ($e instanceof InvalidArgumentException || strpos($e->getMessage(), 'not found') !== false) {
                $error_message .= ' ' . trans('please_try_again');
            }
            $this->session->set_flashdata('alert_error', $error_message);
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
