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

    public function form($invoice_id)
    {
        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id',
            $this->user_clients)->get()->row();

        if (!$invoice) {
            show_404();
        }

        // Get all payment gateways
        $this->load->model('mdl_settings');
        $omnipay = new \Omnipay\Omnipay();
        $this->config->load('payment_gateways');
        $allowed_drivers = $this->config->item('payment_gateways');
        $gateway_drivers = array_intersect($omnipay->getFactory()->getSupportedGateways(), $allowed_drivers);

        $available_drivers = array();
        foreach ($gateway_drivers as $driver) {
            $setting = $this->mdl_settings->setting('gateway_' . strtolower($driver));
            if ($setting == 1) {
                array_push($available_drivers, $driver);
            }
        }

        $this->layout->set(
            array(
                'invoice' => $invoice,
                'gateways' => $available_drivers,
            )
        );

        $this->layout->buffer('content', 'guest/payment_information');
        $this->layout->render('layout_guest');

    }

}
