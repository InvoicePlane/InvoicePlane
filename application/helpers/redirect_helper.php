<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

function redirect_to($fallback_url_string, $redirect = true)
{
    $CI = &get_instance();

    $redirect_url = ($CI->session->userdata('redirect_to')) ? $CI->session->userdata('redirect_to') : $fallback_url_string;

    if ($redirect) {
        redirect($redirect_url);
    }
    return $redirect_url;
}

function redirect_to_set()
{
    $CI = &get_instance();
    $CI->session->set_userdata('redirect_to', $CI->uri->uri_string());
}
