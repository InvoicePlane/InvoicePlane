<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane - Clicksend Module
 *
 * @author		Matthias Schaffer
 * @copyright	Copyright (c) 2020 matthiasschaffer.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Clicksend
 */
class Clicksend extends Admin_Controller
{
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

        if ($this->clicksend_configured == false) {
            $this->layout->buffer('content', 'clicksend/not_configured');
            $this->layout->render();
        }
    }

    /**
     * @param $invoice_id
     */
    public function invoice($invoice_id)
    {
        if (!$this->clicksend_configured) {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('invoices/mdl_invoices');
        $this->load->helper('template');
        $this->load->helper('country');

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = array();
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $this->layout->set('selected_pdf_template', select_pdf_invoice_template($invoice));
        $this->layout->set('invoice', $invoice);
        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('countries', get_country_list(trans('cldr')));
        $this->layout->set('pdf_templates', $this->mdl_templates->get_invoice_templates());
        $this->layout->buffer('content', 'clicksend/invoice');
        $this->layout->render();
    }

    /**
     * @param $quote_id
     */
    public function quote($quote_id)
    {
        if (!$this->clicksend_configured) {
            return;
        }

        $this->load->model('invoices/mdl_templates');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('upload/mdl_uploads');
        $this->load->model('email_templates/mdl_email_templates');

        $email_template_id = get_setting('email_quote_template');

        if ($email_template_id) {
            $email_template = $this->mdl_email_templates->get_by_id($email_template_id);
            $this->layout->set('email_template', json_encode($email_template));
        } else {
            $this->layout->set('email_template', '{}');
        }

        // Get all custom fields
        $this->load->model('custom_fields/mdl_custom_fields');
        $custom_fields = array();
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $this->layout->set('selected_pdf_template', get_setting('pdf_quote_template'));
        $this->layout->set('selected_email_template', $email_template_id);
        $this->layout->set('email_templates', $this->mdl_email_templates->where('email_template_type', 'quote')->get()->result());
        $this->layout->set('quote', $this->mdl_quotes->get_by_id($quote_id));
        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('pdf_templates', $this->mdl_templates->get_quote_templates());
        $this->layout->buffer('content', 'clicksend/quote');
        $this->layout->render();

    }

    /**
     * @param $invoice_id
     */
    public function send_invoice($invoice_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoices/view/' . $invoice_id);
        }

        if (!$this->clicksend_configured) {
            return;
        }
        if (!$this->clicksend_auth) {
            $this->clicksend_auth = clicksend_getAuth();
        }

        $this->load->model('invoices/mdl_invoices');

        $address_name = $this->input->post('address_name');
        $address_line_1 = $this->input->post('address_line_1');
        $address_line_2 = $this->input->post('address_line_2');
        $address_postal_code = $this->input->post('address_postal_code');
        $address_city = $this->input->post('address_city');
        $address_state = $this->input->post('address_state');
        $address_country = $this->input->post('address_country');
        $clicksend_add_cost_to_invoice = $this->input->post('clicksend_add_cost_to_invoice');
        $print_duplex = $this->input->post('print_duplex');
        $print_color = $this->input->post('print_color');

        if (empty($address_name) || empty($address_line_1) || empty($address_country)) {
            $this->session->set_flashdata('alert_danger', trans('address_missing'));
            redirect('clicksend/invoice/' . $invoice_id);
        }
        
        //$address_postal_code = 11111; # TODO for development

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        $this->mdl_invoices->generate_invoice_number_if_applicable($invoice_id);
        
        $options = [
            "method" => "POST",
            "action" => "post/letters/send",
            "auth" => $this->clicksend_auth,
            "body" => [
                "file_url" => clicksend_getPDFURL($invoice),
                "duplex" => intval($print_duplex),
                "colour" => intval($print_color),
                "priority_post" => 0,
                "template_used" => 0,
                "recipients" => [[
                    "address_name" => $address_name,
                    "address_line_1" => $address_line_1,
                    "address_line_2" => $address_line_2,
                    "address_postal_code" => $address_postal_code,
                    "address_city" => $address_city,
                    "address_state" => $address_state,
                    "address_country" => $address_country,
                    "return_address_id" => $this->getReturnAddressID($invoice)
                ]],
                "source" => "Invoiceplane_Clicksend_Module"
            ]
        ];
        
        $response = clicksendapi_request($options);

        /*header("Content-type: application/json");
        echo json_encode($response);
        exit();*/

        if($response["error"]){
            $this->session->set_flashdata('alert_danger', $response["msg"]);
            redirect('clicksend/invoice/' . $invoice_id);
        }

        if($response["response"]["response_code"] == "SUCCESS"){
            $this->mdl_invoices->mark_sent($invoice_id);
            $this->session->set_flashdata('alert_success', trans('letter_successfully_sent').".<br><small>".trans('price').": ".format_currency($response["response"]["data"]["total_price"])."</small>");
            redirect('invoices/view/' . $invoice_id);
        }else{
            $this->session->set_flashdata('alert_danger', $response["response"]["response_msg"]);
            redirect('clicksend/invoice/' . $invoice_id);
        }
    }

    private function getReturnAddressID($invoice = null)
    {
        if (!$this->clicksend_configured) {
            return;
        }
        if (!$this->clicksend_auth) {
            $this->clicksend_auth = clicksend_getAuth();
        }

        if(!empty(get_setting('clicksend_return_address_id'))){
            return get_setting('clicksend_return_address_id');
        }else{
            if($invoice == null) return null;

            $response = clicksendapi_request([
                "method" => "POST",
                "action" => "post/return-addresses",
                "auth" => $this->clicksend_auth,
                "body" => [
                    "address_name" => $invoice->user_name,
                    "address_line_1" => $invoice->user_address_1,
                    "address_line_2" => $invoice->user_address_2,
                    "address_postal_code" => $invoice->user_zip,
                    "address_city" => $invoice->user_city,
                    "address_state" => $invoice->user_state,
                    "address_country" => $invoice->user_country
                ]
            ]);

            if($response["error"]){
                return null;
            }
            
            if($response["response"]["response_code"] == "SUCCESS"){
                $return_address_id = $response["response"]["data"]["return_address_id"];
                $this->mdl_settings->save('clicksend_return_address_id', $return_address_id);
                return $return_address_id;
            }else{
                return null;
            }
        }
    }

}
