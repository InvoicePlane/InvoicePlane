<?php

use GuzzleHttp\Client;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

class Payment_Information extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    public function form($invoice_url_key, $payment_provider = null)
    {
        $this->load->model('payment_methods/mdl_payment_methods');
        $disable_form = false;

        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()->row();

        if (! $invoice) {
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            $disable_form = true;
            show_404();
        }

        // Get all payment gateways
        $this->load->model('mdl_settings');
        $this->config->load('payment_gateways');
        $gateways = $this->config->item('payment_gateways');

        $available_drivers = [];
        foreach ($gateways as $driver => $fields) {

            $d = strtolower($driver);

            if (get_setting('gateway_' . $d . '_enabled') == 1) {
                $invoice_payment_method = $invoice->payment_method;
                $driver_payment_method = get_setting('gateway_' . $d . '_payment_method');

                if ($invoice_payment_method == 0 || $driver_payment_method == 0 || $driver_payment_method == $invoice_payment_method) {
                    array_push($available_drivers, $driver);
                }
            }
        }

        //If only one provider is available, serve it without showing options
        if (count($available_drivers) == 1) {
            $payment_provider = $available_drivers[0];
        }

        // Get additional invoice information
        $payment_method = $this->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }

        $is_overdue = ($invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time() ? true : false);

        // Return the view
        $view_data = [
            'disable_form' => $disable_form,
            'invoice' => $invoice,
            'gateways' => $available_drivers,
            'payment_method' => $payment_method,
            'is_overdue' => $is_overdue,
            'invoice_url_key' => $invoice_url_key,
            'payment_provider' => $payment_provider
        ];

        $this->load->view('guest/payment_information', $view_data) . $payment_provider && $this->$payment_provider($invoice_url_key);
    }

    /**
     * Load the stripe payments page
     * with the pertinent data
     *
     * @return View the stripe page view
     */
    public function stripe($invoice_url_key)
    {
        //get the api key for which the card token must be generated
        $view_data['stripe_api_key'] = get_setting('gateway_stripe_apiKeyPublic');
        $view_data['invoice_url_key'] = $invoice_url_key;
        $this->load->view('guest/gateways/stripe', $view_data);
    }

    /**
     * Create the order on PayPal and load the
     * paypal payments page.
     *
     * @param  string  $invoice_url_key
     * @return View the paypal page view
     */
    public function paypal($invoice_url_key)
    {
        $view_data['paypal_client_id'] = get_setting('gateway_paypal_clientId');
        $view_data['invoice_url_key'] = $invoice_url_key;
        $view_data['currency'] = $this->mdl_settings->setting('gateway_paypal_currency');
        $this->load->view('guest/gateways/paypal', $view_data);
    }

    /**
     * Create the order on PayPal and load the
     * paypal payments page.
     *
     * @param string $invoice_url_key
     * @return View the paypal page view
     */
    public function paypal($invoice_url_key)
    {
        $view_data['paypal_client_id'] = get_setting('gateway_paypal_clientId');
        $view_data['invoice_url_key'] = $invoice_url_key;
        $view_data['currency'] = $this->mdl_settings->setting('gateway_paypal_currency');
        $this->load->view('guest/gateway/paypal',$view_data);
    }

    public function paypal_create_order($invoice_url_key) {
        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)
        ->get()->row();

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            //TODO: redirect to invoice page
        }

        $this->load->library('crypt');

        $this->load->library('gateways/paypal',[
            'client_id' => get_setting('gateway_paypal_clientId'),
            'client_secret' => $this->crypt->decode(get_setting('gateway_paypal_clientSecret')),
            'demo' => true
        ]);

        $order_information = [
            'invoice_id' => $invoice->invoice_id,
            'currency_code' => $this->mdl_settings->setting('gateway_paypal_currency'),
            'value' => $invoice->invoice_balance,
            'custom_id' => $invoice_url_key
        ];

        $paypal_client = $this->paypal->createOrder($order_information);

       print $paypal_client;

    }

    public function paypal_capture_payment($order_id) {

        $this->load->library('crypt');

        $this->load->library('gateways/paypal',[
            'client_id' => get_setting('gateway_paypal_clientId'),
            'client_secret' => $this->crypt->decode(get_setting('gateway_paypal_clientSecret')),
            'demo' => true
        ]);

        $paypal_response = $this->paypal->captureOrder($order_id);

        //handle the payment
        if($paypal_response['status']) {
            $paypal_object = json_decode($paypal_response['response']->getBody());

            $invoice_id = $paypal_object->purchase_units[0]->payments->captures[0]->invoice_id;
            $amount = $paypal_object->purchase_units[0]->payments->captures[0]->amount->value;

            //record the payment
            $this->load->model('payments/mdl_payments');

            $db_array = [
                'invoice_id' => $invoice_id,
                'payment_date' => date('Y-m-d'),
                'payment_amount' => $amount,
                'payment_method_id' => get_setting('gateway_paypal_payment_method'),
                'payment_note' => '',
            ];

            $this->mdl_payments->save(null,$db_array);

            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)
            ->get()->row();

            $this->session->set_flashdata('alert_success', trans('online_payment_payment_successful',$invoice->invoice_number));
            $this->session->keep_flashdata('alert_success');

                //save online transaction
                $this->setGatewayResponse(
                    true,
                    'paypal',
                    $paypal_response['response']->getBody()->getContents(),
                    $order_id,
                    $invoice_id ?? 0,
                );
        }
        else
        {
            $response_error = json_decode($paypal_response['error']->getResponse()->getBody());

            //save online transaction
            $this->setGatewayResponse(
                true,
                'paypal',
                json_encode($response_error),
                $order_id,
                $invoice_id ?? 0,
            );


            $this->session->set_flashdata('alert_error',
            trans('online_payment_payment_failed') . '<br/>' . $response_error->details[0]->description);
            $this->session->keep_flashdata('alert_error');
        }
    }

    protected function setGatewayResponse($is_success,$merchant,$message,$merchant_reference,$invoice_id = 0) : void {

        $db_array = [
            'invoice_id' => $invoice_id,
            'merchant_response_successful' => $is_success,
            'merchant_response_date' => date('Y-m-d'),
            'merchant_response_driver' => $merchant,
            'merchant_response' => $message,
            'merchant_response_reference' => $merchant_reference,
        ];

        $this->db->insert('ip_merchant_responses', $db_array);
    }
}
