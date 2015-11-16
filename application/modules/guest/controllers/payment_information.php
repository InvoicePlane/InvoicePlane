<?php

if (!defined('BASEPATH')) {
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

class Payment_Information extends Guest_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    public function form($invoice_url_key)
    {
        $disable_form = false;

        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->where_in('ip_invoices.client_id',
            $this->user_clients)->get()->row();

        if (!$invoice) {
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            //$this->session->set_flashdata('alert_error', lang('invoice_already_paid'));
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            $disable_form = true;
        }

        // Get all payment gateways
        $this->load->model('mdl_settings');
        $omnipay = new \Omnipay\Omnipay();
        $this->config->load('payment_gateways');
        $allowed_drivers = $this->config->item('payment_gateways');
        $gateway_drivers = array_intersect($omnipay->getFactory()->getSupportedGateways(), $allowed_drivers);

        $available_drivers = array();
        foreach ($gateway_drivers as $driver) {

            $d = strtolower($driver);
            $setting = $this->mdl_settings->setting('gateway_' . $d);
            $invoice_payment_method = $invoice->payment_method;
            $driver_payment_method = $this->mdl_settings->setting('gateway_payment_method_' . $d);

            if ($setting == 1) {
                if ($invoice_payment_method == 0 || $driver_payment_method == $invoice_payment_method) {
                    array_push($available_drivers, $driver);
                }
            }
        }

        $this->layout->set(
            array(
                'disable_form' => $disable_form,
                'invoice' => $invoice,
                'gateways' => $available_drivers,
            )
        );

        $this->layout->buffer('content', 'guest/payment_information');
        $this->layout->render('layout_guest');

    }

}
