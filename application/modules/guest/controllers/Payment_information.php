<?php

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
}
