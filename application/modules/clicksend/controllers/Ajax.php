<?php

if ( ! defined('BASEPATH')) {
exit('No direct script access allowed');
}

/*
 * InvoicePlane - Clicksend Module
 *
 * @author		Matthias Schaffer
 * @copyright	Copyright (c) 2020 matthiasschaffer.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Ajax.
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    private $clicksend_configured;

    private $clicksend_auth;

    /**
     * Clicksend constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('clicksend');

        $this->clicksend_configured = clicksend_configured();

        if ($this->clicksend_configured == true) {
            $this->clicksend_auth = clicksend_get_auth();
        }
    }

    /**
     * Check API Credentials for settings page.
     */
    public function check_api_credentials()
    {
        header('Content-type: application/json');

        $api_username = $this->input->post('api_username');
        $api_key      = $this->input->post('api_key');

        if (empty($api_username) || empty($api_key)) {
            http_response_code(400);
            exit(json_encode(['error' => true, 'msg' => 'credentials are empty']));
        }

        $response = clicksendapi_request([
            'method' => 'GET',
            'action' => 'account',
            'auth'   => base64_encode($api_username . ':' . $api_key),
        ]);

        if ($response['error']) {
            exit(json_encode($response));
        }

        if ($response['response']['response_code'] == 'SUCCESS') {
            exit(json_encode(['error' => false, 'success' => true]));
        }
            exit(json_encode(['error' => false, 'success' => false]));
    }

    /**
     * Get price for letter.
     */
    public function get_letter_price()
    {
        $this->load->model('invoices/mdl_invoices');

        header('Content-type: application/json');
        if ($this->clicksend_configured == false) {
            exit(json_encode(['error' => true, 'msg' => 'clicksend is not configured']));
        }

        $invoice_id = (int) $this->input->post('invoice_id');

        if (empty($invoice_id)) {
            http_response_code(400);
            exit(json_encode(['error' => true, 'msg' => 'invoice id is missing']));
        }

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        if (!$invoice) {
            http_response_code(404);
            exit(json_encode(['error' => true, 'msg' => 'invoice not found']));
        }

        $current_user_id = $this->session->userdata('user_id');
        if ($current_user_id === null || (isset($invoice->user_id) && (int) $invoice->user_id !== (int) $current_user_id)) {
            http_response_code(403);
            exit(json_encode(['error' => true, 'msg' => 'not authorized to access this invoice']));
        }

        $address_name        = $this->input->post('address_name') ?? '';
        $address_line_1      = $this->input->post('address_line_1') ?? '';
        $address_line_2      = $this->input->post('address_line_2') ?? '';
        $address_postal_code = $this->input->post('address_postal_code') ?? '';
        $address_city        = $this->input->post('address_city') ?? '';
        $address_state       = $this->input->post('address_state') ?? '';
        $address_country     = $this->input->post('address_country') ?? '';
        $print_duplex        = (int) $this->input->post('print_duplex');
        $print_color         = (int) $this->input->post('print_color');

        $options = [
            'method' => 'POST',
            'action' => 'post/letters/price',
            'auth'   => $this->clicksend_auth,
            'body'   => [
                'file_url'      => clicksend_get_pdf_url($invoice),
                'duplex'        => $print_duplex,
                'colour'        => $print_color,
                'priority_post' => 0,
                'template_used' => 0,
                'recipients'    => [[
                    'address_name'        => $address_name,
                    'address_line_1'      => $address_line_1,
                    'address_line_2'      => $address_line_2,
                    'address_postal_code' => $address_postal_code,
                    'address_city'        => $address_city,
                    'address_state'       => $address_state,
                    'address_country'     => $address_country,
                ]],
                'source' => 'Invoiceplane_Clicksend_Module',
            ],
        ];
        $response = clicksendapi_request($options);

        if ($response['error']) {
            exit(json_encode($response));
        }

        if ($response['response']['response_code'] == 'SUCCESS') {
            exit(json_encode([
                'error'              => false,
                'total_price'        => $response['response']['data']['total_price'],
                'total_price_format' => format_currency($response['response']['data']['total_price']),
            ]));
        }
        exit(json_encode(['error' => true, 'response_msg' => $response['response']['response_msg']]));
    }
}
