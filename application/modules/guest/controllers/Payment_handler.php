<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package     InvoicePlane
 * @author      Kovah (www.kovah.de)
 * @copyright   Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 * 
 */

#[AllowDynamicProperties]
class Payment_Handler extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('encrypt');

        $this->load->model('invoices/mdl_invoices');
    }

    public function make_payment()
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $this->input->post('invoice_url_key'))->get();

        if ($invoice->num_rows() == 1) {

            // @todo Currently not working for websites with redirect like PayPal, needs implementation of the redirect method! Stripe is working without problems. - Kovah <mail@kovah.de>

            // Get the invoice data and load the encrypt library
            $invoice = $invoice->row();
            $this->load->library('encrypt');

            // Get and set the merchant driver
            $driver = $this->input->post('gateway');
            $gateway = \Omnipay\Omnipay::create($driver);

            // Get the driver settings
            $d = strtolower($driver);
            $driver_api_secret = $this->encrypt->decode($this->mdl_settings->setting('gateway_password_' . $d));
            $driver_currency = $this->mdl_settings->setting('gateway_currency_' . $d);

            $gateway->setApiKey($driver_api_secret);

            // Get the credit card data
            $cc_number = $this->input->post('creditcard_number');
            $cc_expire_month = $this->input->post('creditcard_expiry_month');
            $cc_expire_year = $this->input->post('creditcard_expiry_year');
            $cc_cvv = $this->input->post('creditcard_cvv');

            if ($cc_number) {
                $credit_card = array(
                    'number' => $cc_number,
                    'expiryMonth' => $cc_expire_month,
                    'expiryYear' => $cc_expire_year,
                    'cvv' => $cc_cvv
                );
            } else {
                $credit_card = array();
            }

            // Set up the api data
            // @todo Redirect URLs are missing - Kovah <mail@kovah.de>
            $request = array(
                'amount' => $invoice->invoice_balance,
                'currency' => $driver_currency,
                'card' => $credit_card,
                'metadata' => array(
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_guest_url' => $invoice->invoice_url_key
                ),
            );

            // Send the request
            $response = $gateway->purchase($request)->send();

            // Process the response
            if ($response->isSuccessful()) {

                // Set invoice to paid
                $this->load->model('payments/mdl_payments');

                $db_array = array(
                    'invoice_id' => $invoice->invoice_id,
                    'payment_date' => date('Y-m-d'),
                    'payment_amount' => $invoice->invoice_balance,
                    'payment_method_id' => $invoice->payment_method,
                );

                $this->mdl_payments->save(null, $db_array);

                // Save gateway response
                $db_array = array(
                    'invoice_id' => $invoice->invoice_id,
                    'merchant_response_date' => date('Y-m-d'),
                    'merchant_response_driver' => $driver,
                );

                $this->db->insert('ip_merchant_responses', $db_array);

                // Redirect user and display the success message
                $this->session->set_flashdata('alert_success', lang('online_payment_payment_successful'));
                redirect('guest/invoices/status/open/');

            } elseif ($response->isRedirect()) {

                // Redirect to offsite payment gateway
                //@todo redirect is not handled at the moment - Kovah <mail@kovah.de>
                $response->redirect();

            } else {

                // Payment failed
                // Save the response in the database
                $db_array = array(
                    'invoice_id' => $invoice->invoice_id,
                    'merchant_response_date' => date('Y-m-d'),
                    'merchant_response_driver' => $driver,
                    'merchant_response' => $response->getMessage(),
                );

                $this->db->insert('ip_merchant_responses', $db_array);

                // Redirect the user and display failure message
                $this->session->set_flashdata('alert_error', lang('online_payment_payment_failed') . '<br/>' . $response->getMessage());
                redirect('guest/payment_information/form/' . $invoice->invoice_url_key);
            }
        }
    }

    /*
     * 
     * =======================================================================================
     * The code below may not be needed anymore except for the payments with offiste redirect.
     * =======================================================================================
     *
     */
    public function payment_return($invoice_url_key)
    {
        // See if the response can be validated
        if ($this->payment_validate($invoice_url_key)) {
            // Set the success flash message
            $this->session->set_flashdata('alert_success', lang('online_payment_payment_successful'));

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
                    'payment_method_id' => ($this->mdl_settings->setting('online_payment_method')) ? $this->mdl_settings->setting('online_payment_method') : 0
                );

                $this->mdl_payments->save(null, $db_array);
            }
        } else {
            // Set the failure flash message
            $this->session->set_flashdata('alert_error', lang('online_payment_payment_failed'));
        }

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

    public function payment_cancel($invoice_url_key)
    {
        // Validate the response
        $this->payment_validate($invoice_url_key);

        // Set the cancel flash message
        $this->session->set_flashdata('alert_info', lang('online_payment_payment_cancelled'));

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

    private function payment_validate($invoice_url_key)
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            // Load the merchant driver
            $this->merchant->load($this->mdl_settings->setting('merchant_driver'));

            // Pass the required settings
            $settings = array(
                'username' => $this->mdl_settings->setting('merchant_username'),
                'password' => $this->encrypt->decode($this->mdl_settings->setting('merchant_password')),
                'signature' => $this->mdl_settings->setting('merchant_signature'),
                'test_mode' => ($this->mdl_settings->setting('merchant_test_mode')) ? true : false
            );

            // Init the driver
            $this->merchant->initialize($settings);

            // Create the parameters
            $params = array(
                'description' => lang('invoice') . ' ' . $invoice->invoice_number, 
                'amount' => $invoice->invoice_balance,
                'currency' => $this->mdl_settings->setting('merchant_currency_code')
            );

            // Get the response
            $response = $this->merchant->purchase_return($params);

            // Determine if it was successful or not
            $merchant_response = ($response->success()) ? 1 : 0;

            // Create the record for ip_merchant_responses
            $db_array = array(
                'invoice_id' => $invoice->invoice_id,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => $this->mdl_settings->setting('merchant_driver'),
                'merchant_response' => $merchant_response,
                'merchant_response_reference' => ($response->reference()) ? $response->reference() : ''
            );

            $this->db->insert('ip_merchant_responses', $db_array);

            return $merchant_response;
        }

        return 0;
    }
}