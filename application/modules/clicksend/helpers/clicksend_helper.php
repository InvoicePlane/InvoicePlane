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
 * Check if mail sending is configured in the settings
 *
 * @return bool
 */
function clicksend_configured()
{
    $CI = &get_instance();

    return ($CI->mdl_settings->setting('clicksend_api_username') && $CI->mdl_settings->setting('clicksend_api_key'));
}

function clicksend_getAuth()
{
    $CI = &get_instance();

    return base64_encode($CI->mdl_settings->setting('clicksend_api_username') .":". $CI->mdl_settings->setting('clicksend_api_key'));
}

function clicksend_getPDFURL($invoice)
{
    return site_url('clicksend/view/generate_invoice_pdf/' . md5(clicksend_getAuth()) . "/" . $invoice->invoice_url_key);
}

function clicksendapi_request($options = []) {
    if(empty($options)){
        return ["error" => true, "msg" => "options are empty"];
    }

    if(empty($options["method"])) $options["method"] = "GET";
    if(empty($options["action"])){
        return ["error" => true, "msg" => "no action set"];
    }
    if(empty($options["api_version"])) $options["api_version"] = "v3";
    if(empty($options["auth"])){
        return ["error" => true, "msg" => "no auth set"];
    }


    if(!empty($options["headers"])){
        $headers = $options["headers"];
    }else{
        $headers = [];
    }
    if(!empty($options["auth"])){
        $headers["Authorization"] = "Basic ".$options["auth"];
    }

    $clicksend_api_url = 'https://rest.clicksend.com/';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $clicksend_api_url.$options["api_version"]."/".$options["action"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    switch($options["method"]){
        case "GET":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options["body"]));
            $headers["Content-Type"] = "application/json";
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options["body"]));
            $headers["Content-Type"] = "application/json";
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        default:
            return ["error" => true, "msg" => "Method not found"];
    }

    $header = [] ;
    foreach($headers as $key => $value){
        $header[] = "$key: $value";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $response = json_decode(curl_exec($ch), true);
    if (curl_errno($ch)) {
        return ["error" => true, "response" => $response, "msg" => curl_error($ch)];
    }
    curl_close($ch);
    return ["error" => false, "response" => $response];
}