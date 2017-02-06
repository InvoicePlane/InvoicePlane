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

function format_date($txt)
{
    return date_from_mysql($txt);
}

function format_text($txt)
{
    return $txt;
}

function format_singlechoice($txt)
{
    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');
    $el = $CI->cv->get_by_id($txt)->row();
    return $el->custom_values_value;
}

function format_multiplechoice($txt)
{
    $CI = get_instance();
    $CI->load->model('custom_values/mdl_custom_values', 'cv');

    $values = explode(",",$txt);
    $values = $CI->cv->where_in('custom_values_id', $values)->get()->result();
    $values_text = [];
    foreach($values as $value){
        $values_text[] = $value->custom_values_value;
    }
    return implode("\n",$values_text);
}

function format_boolean($txt){
  if($txt == "1")
  {
    return trans("true");
  }
  else if($txt == "0"){
    return trans("false");
  }
  return "";
}

function format_fallback($txt)
{
    return format_text($txt);
}
