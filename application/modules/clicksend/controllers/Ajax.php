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
 * Class Ajax
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

        /*if ($this->clicksend_configured == false) {
            exit(json_encode(["error" => true, "msg" => "clicksend is not configured"]));
        }*/
        
        if ($this->clicksend_configured == true) {
            $this->clicksend_auth = clicksend_getAuth();
        }
    }

    /**
     * Check API Credentials for settings page
     */
    public function check_api_credentials()
    {
        header("Content-type: application/json");

        if(empty($_POST["api_username"]) || empty($_POST["api_key"])){
            http_response_code(400);
            exit(json_encode(["error" => true, "msg" => "credentials are empty"]));
        }
        
        $response = clicksendapi_request([
            "method" => "GET",
            "action" => "account",
            "auth" => base64_encode($_POST["api_username"].":".$_POST["api_key"])
        ]);

        if($response["error"]){
            exit(json_encode($response));
        }

        if($response["response"]["response_code"] == "SUCCESS"){
            exit(json_encode(["error" => false, "success" => true]));
        }else{
            exit(json_encode(["error" => false, "success" => false]));
        }
    }

    /**
     * Get price for letter
     */
    public function get_letter_price()
    {
        $this->load->model('invoices/mdl_invoices');

        header("Content-type: application/json");
        if ($this->clicksend_configured == false) {
            exit(json_encode(["error" => true, "msg" => "clicksend is not configured"]));
        }

        if(empty($_POST["invoice_id"])){
            http_response_code(400);
            exit(json_encode(["error" => true, "msg" => "invoice id is missing"]));
        }
        $invoice_id = intval($_POST["invoice_id"]);
        
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        
        $options = [
            "method" => "POST",
            "action" => "post/letters/price",
            "auth" => $this->clicksend_auth,
            "body" => [
                "file_url" => clicksend_getPDFURL($invoice),
                "duplex" => intval($_POST["print_duplex"]),
                "colour" => intval($_POST["print_color"]),
                "priority_post" => 0,
                "template_used" => 0,
                "recipients" => [[
                    "address_name" => $_POST["address_name"],
                    "address_line_1" => $_POST["address_line_1"],
                    "address_line_2" => $_POST["address_line_2"],
                    "address_postal_code" => $_POST["address_postal_code"],
                    "address_city" => $_POST["address_city"],
                    "address_state" => $_POST["address_state"],
                    "address_country" => $_POST["address_country"]
                ]],
                "source" => "Invoiceplane_Clicksend_Module"
            ]
        ];
        $response = clicksendapi_request($options);

        if($response["error"]){
            exit(json_encode($response));
        }

        if($response["response"]["response_code"] == "SUCCESS"){
            exit(json_encode([
                "error" => false,
                "total_price" => $response["response"]["data"]["total_price"],
                "total_price_format" => format_currency($response["response"]["data"]["total_price"])
            ]));
        }else{
            exit(json_encode(["error" => true, "response_msg" => $response["response"]["response_msg"]]));
        }
    }

}
