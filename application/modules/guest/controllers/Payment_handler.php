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
 * Class Payment_Handler
 */
class Payment_Handler extends Base_Controller
{
    /**
     * Payment_Handler constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('merchant');
        $this->load->library('encrypt');

        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * @param $invoice_url_key
     */
    public function make_payment($invoice_url_key)
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            // Load the merchant driver
            $this->merchant->load(get_setting('merchant_driver'));

            // Pass the required settings
            $settings = array(
                'username' => get_setting('merchant_username'),
                'password' => $this->encrypt->decode(get_setting('merchant_password')),
                'signature' => get_setting('merchant_signature'),
                'test_mode' => (get_setting('merchant_test_mode')) ? true : false
            );

            // Init the driver
            $this->merchant->initialize($settings);

            // Create the parameters
            $params = array(
                'description' => trans('invoice') . ' ' . $invoice->invoice_number,
                'amount' => $invoice->invoice_balance,
                'currency' => get_setting('merchant_currency_code'),
                'return_url' => site_url('guest/payment_handler/payment_return/' . $invoice_url_key . '/r'),
                'cancel_url' => site_url('guest/payment_handler/payment_cancel/' . $invoice_url_key . '/c'),
            );

            // Get the response; redirects to gateway if successful
            $response = $this->merchant->purchase($params);

            if (!$response->success()) {
                // Oops - something went wrong
                $this->session->set_flashdata('flash_message', $response->message());

                // Redirect to guest invoice view with flash message
                redirect('guest/view/invoice/' . $invoice_url_key);
            }
        }
    }

    /**
     * @param $invoice_url_key
     */
    public function payment_return($invoice_url_key)
    {
        // See if the response can be validated
        if ($this->payment_validate($invoice_url_key)) {
            // Set the success flash message
            $this->session->set_flashdata('flash_message', trans('merchant_payment_success'));

            // Attempt to get the invoice
            $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

            if ($invoice->num_rows() == 1) {
                $invoice = $invoice->row();

                // Create the payment record
                $this->load->model('payments/mdl_payments');

                $db_array = array(
                    'invoice_id' => $invoice->invoice_id,
                    'payment_date' => date('Y-m-d'),
                    'payment_amount' => $invoice->invoice_balance,
                    'payment_method_id' => (get_setting('online_payment_method')) ? get_setting('online_payment_method') : 0
                );

                $this->mdl_payments->save(null, $db_array);
            }
        } else {
            // Set the failure flash message
            $this->session->set_flashdata('flash_message', trans('merchant_payment_fail'));
        }

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

    /**
     * @param $invoice_url_key
     * @return int
     */
    private function payment_validate($invoice_url_key)
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            // Load the merchant driver
            $this->merchant->load(get_setting('merchant_driver'));

            // Pass the required settings
            $settings = array(
                'username' => get_setting('merchant_username'),
                'password' => $this->encrypt->decode(get_setting('merchant_password')),
                'signature' => get_setting('merchant_signature'),
                'test_mode' => (get_setting('merchant_test_mode')) ? true : false
            );

            // Init the driver
            $this->merchant->initialize($settings);

            // Create the parameters
            $params = array(
                'description' => trans('invoice') . ' ' . $invoice->invoice_number,
                'amount' => $invoice->invoice_balance,
                'currency' => get_setting('merchant_currency_code')
            );

            // Get the response
            $response = $this->merchant->purchase_return($params);

            // Determine if it was successful or not
            $merchant_response = ($response->success()) ? 1 : 0;

            // Create the record for ip_merchant_responses
            $db_array = array(
                'invoice_id' => $invoice->invoice_id,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => get_setting('merchant_driver'),
                'merchant_response' => $merchant_response,
                'merchant_response_reference' => ($response->reference()) ? $response->reference() : ''
            );

            $this->db->insert('ip_merchant_responses', $db_array);

            return $merchant_response;
        }

        return 0;
    }

    /**
     * @param $invoice_url_key
     */
    public function payment_cancel($invoice_url_key)
    {
        // Validate the response
        $this->payment_validate($invoice_url_key);

        // Set the cancel flash message
        $this->session->set_flashdata('flash_message', trans('merchant_payment_cancel'));

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

}
