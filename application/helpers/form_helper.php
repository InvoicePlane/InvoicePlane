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

/**
 * Returns $_POST data for the given input name from the last request if it was saved
 * @param $field_name
 * @param mixed $default_value
 * @return mixed
 */
function old1($field_name, $default_value = null)
{
    $CI = &get_instance();
    $form_input = $CI->session->flashdata('old_form_input');

    if ($form_input && isset($form_input[$field_name])) {
        return $form_input[$field_name];
    } else {
        return $default_value;
    }
}

function all_form_errors($as_html)
{
    $return = ($as_html ? '<ul>' : array());

    foreach (array_keys($_POST) as $key) {
        if (form_error($key)) {
            if ($as_html) {
                $return .= '<li>' . form_error($key) . '</li>';
            } else {
                $return[$key] = form_error($key);
            }
        }
    }

    if ($as_html) {
        $return .= '</ul>';
    }

    return $return;
}
