<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
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

        $this->load->library('crypt');

        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * Process the payment for the given invoice
     */
    public function make_payment()
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $this->input->post('invoice_url_key'))->get();

        if ($invoice->num_rows() == 1) {

            // Get the invoice data
            $invoice = $invoice->row();

            // Initialize the gateway
            $driver = $this->input->post('gateway');
            $d = strtolower($driver);
            $gateway = $this->initialize_gateway($driver);

            // Get the credit card data
            $cc_number = $this->input->post('creditcard_number');
            $cc_expire_month = $this->input->post('creditcard_expiry_month');
            $cc_expire_year = $this->input->post('creditcard_expiry_year');
            $cc_cvv = $this->input->post('creditcard_cvv');

            if ($cc_number) {
                try {
                    $credit_card = new \Omnipay\Common\CreditCard([
                        'number' => $cc_number,
                        'expiryMonth' => $cc_expire_month,
                        'expiryYear' => $cc_expire_year,
                        'cvv' => $cc_cvv,
                    ]);
                    $credit_card->validate();
                } catch (\Exception $e) {
                    // Redirect the user and display failure message
                    $this->session->set_flashdata('alert_error',
                        trans('online_payment_card_invalid') . '<br/>' . $e->getMessage());
                    redirect('guest/payment_information/form/' . $invoice->invoice_url_key);
                }
            } else {
                $credit_card = [];
            }

            // Set up the api data
            $driver_currency = $this->mdl_settings->setting('gateway_' . $d . '_currency');

            $request = [
                'amount' => $invoice->invoice_balance,
                'currency' => $driver_currency,
                'card' => $credit_card,
                'description' => sprintf(trans('payment_description'), $invoice->invoice_number),
                'metadata' => [
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_guest_url' => $invoice->invoice_url_key,
                ],
                'returnUrl' => site_url('guest/payment_handler/payment_return/' . $invoice->invoice_url_key . '/' . $driver),
                'cancelUrl' => site_url('guest/payment_handler/payment_cancel/' . $invoice->invoice_url_key . '/' . $driver),
            ];

            if ($d === 'worldpay') {
                // Additional param for WorldPay
                $request['cartId'] = $invoice->invoice_number;
            }

            $this->session->set_userdata($invoice->invoice_url_key . '_online_payment', $request);

            // Send the request
            $response = $gateway->purchase($request)->send();

            $reference = $response->getTransactionReference() ? $response->getTransactionReference() : '[no transation reference]';

            // Process the response
            if ($response->isSuccessful()) {

                $payment_note = trans('transaction_reference') . ': ' . $reference . "\n";
                $payment_note .= trans('payment_provider') . ': ' . ucwords(str_replace('_', ' ', $d));

                // Set invoice to paid
                $this->load->model('payments/mdl_payments');

                $db_array = [
                    'invoice_id' => $invoice->invoice_id,
                    'payment_date' => date('Y-m-d'),
                    'payment_amount' => $invoice->invoice_balance,
                    'payment_method_id' => $invoice->payment_method,
                    'payment_note' => $payment_note,
                ];

                $this->mdl_payments->save(null, $db_array);
                $payment_success_msg = sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number);

                // Save gateway response
                $db_array = [
                    'invoice_id' => $invoice->invoice_id,
                    'merchant_response_successful' => true,
                    'merchant_response_date' => date('Y-m-d'),
                    'merchant_response_driver' => $driver,
                    'merchant_response' => $payment_success_msg,
                    'merchant_response_reference' => $reference,
                ];

                $this->db->insert('ip_merchant_responses', $db_array);

                // Redirect user and display the success message
                $this->session->set_flashdata('alert_success', $payment_success_msg);
                $this->session->keep_flashdata('alert_success');

                redirect('guest/view/invoice/' . $invoice->invoice_url_key);

            } elseif ($response->isRedirect()) {

                // Redirect to offsite payment gateway
                $response->redirect();

            } else {
                // Payment failed
                // Save the response in the database

                $db_array = [
                    'invoice_id' => $invoice->invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date' => date('Y-m-d'),
                    'merchant_response_driver' => $driver,
                    'merchant_response' => $response->getMessage(),
                    'merchant_response_reference' => $reference,
                ];

                $this->db->insert('ip_merchant_responses', $db_array);

                // Redirect the user and display failure message
                $this->session->set_flashdata('alert_error',
                    trans('online_payment_payment_failed') . '<br/>' . $response->getMessage());
                $this->session->keep_flashdata('alert_error');

                redirect('guest/payment_information/form/' . $invoice->invoice_url_key);
            }
        }
    }

    /**
     * @param $driver
     *
     * @return mixed
     */
    private function initialize_gateway($driver)
    {
        $d = strtolower($driver);
        $settings = get_gateway_settings($driver);

        // Get the payment gateway fields
        $this->config->load('payment_gateways');
        $gateway_settings = $this->config->item('payment_gateways');
        $gateway_settings = $gateway_settings[$driver];

        $gateway_init = [];
        foreach ($settings as $setting) {
            // Sanitize the field key
            $key = str_replace('gateway_' . $d . '_', '', $setting->setting_key);
            $key = str_replace('gateway_' . $d, '', $key);

            // skip empty key
            if (!$key) {
                continue;
            }

            // Decode password fields and checkboxes
            if (isset($gateway_settings[$key]) && $gateway_settings[$key]['type'] == 'password') {
                $value = $this->crypt->decode($setting->setting_value);
            } elseif (isset($gateway_settings[$key]) && $gateway_settings[$key]['type'] == 'checkbox') {
                $value = $setting->setting_value == '1' ? true : false;
            } else {
                $value = $setting->setting_value;
            }

            $gateway_init[$key] = $value;
        }

        // Load Omnipay and initialize the gateway
        $gateway = \Omnipay\Omnipay::create($driver);
        $gateway->initialize($gateway_init);

        return $gateway;
    }

    /**
     * @param $invoice_url_key
     * @param $driver
     */
    public function payment_return($invoice_url_key, $driver)
    {
        $d = strtolower($driver);

        // See if the response can be validated
        if ($this->payment_validate($invoice_url_key, $driver)) {

            // Save the payment for the invoice
            $this->load->model('payments/mdl_payments');

            $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get()->row();

            $db_array = [
                'invoice_id' => $invoice->invoice_id,
                'payment_date' => date('Y-m-d'),
                'payment_amount' => $invoice->invoice_balance,
                'payment_method_id' => (get_setting('gateway_' . $d . '_payment_method')) ? get_setting('gateway_' . $d . '_payment_method') : 0,
            ];

            $this->mdl_payments->save(null, $db_array);

            // Set the success flash message
            $this->session->set_flashdata('alert_success',
                sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number));

        } else {
            // Set the failure flash message
            $this->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
        }

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

    /**
     * @param $invoice_url_key
     * @param $driver
     * @param bool $canceled
     *
     * @return bool
     */
    private function payment_validate($invoice_url_key, $driver, $canceled = false)
    {
        // Attempt to get the invoice
        $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

        $payment_success = false;

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if (!$canceled) {
                $gateway = $this->initialize_gateway($driver);

                // Load previous settings
                $params = $this->session->userdata($invoice->invoice_url_key . '_online_payment');

                if (isset($_GET['PayerID'])) {
                    $params['transactionReference'] = $_GET['PayerID'];
                }

                $response = $gateway->completePurchase($params)->send();
                $payment_success = true;

                $message = $response->getMessage() ? $response->getMessage() : 'No details provided';
            } else {
                $response = '';
                $message = 'Customer cancelled the purchase process';
            }

            // Create the record for ip_merchant_responses
            $db_array = [
                'invoice_id' => $invoice->invoice_id,
                'merchant_response_successful' => $payment_success,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => $driver,
                'merchant_response' => $message,
                'merchant_response_reference' => $canceled ? '' : $response->getTransactionReference(),
            ];

            $this->db->insert('ip_merchant_responses', $db_array);

            return true;
        }

        return false;
    }

    /**
     * @param $invoice_url_key
     * @param $driver
     */
    public function payment_cancel($invoice_url_key, $driver)
    {
        // Validate the response
        $this->payment_validate($invoice_url_key, $driver, true);

        // Set the cancel flash message
        $this->session->set_flashdata('alert_info', trans('online_payment_payment_cancelled'));

        // Redirect to guest invoice view with flash message
        redirect('guest/view/invoice/' . $invoice_url_key);
    }

}
