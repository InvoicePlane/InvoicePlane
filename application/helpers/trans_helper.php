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

function trans($line, $id = '')
{
    $CI =& get_instance();
    $lang_string = $CI->lang->line($line);

    // Fall back to default language if the current language has no translated string
    if (empty($lang_string)) {
        // Load the default language
        $CI->lang->is_loaded = array();
        $CI->lang->language = array();
        $CI->lang->load('ip', 'english');
        $CI->lang->load('form_validation', 'english');
        $CI->lang->load('custom', 'english');

        $lang_string = $CI->lang->line($line);

        // Load the user-selected language again
        $CI->lang->is_loaded = array();
        $CI->lang->language = array();
        $CI->lang->load('ip', $CI->mdl_settings->setting('default_language'));
        $CI->lang->load('form_validation', $CI->mdl_settings->setting('default_language'));
        $CI->lang->load('custom', $CI->mdl_settings->setting('default_language'));
    }

    // Fall back to the $line value if the default language has no translation either
    if (empty($lang_string)) {
        $lang_string = $line;
    }

    if ($id != '') {
        $lang_string = '<label for="' . $id . '">' . $lang_string . "</label>";
    }

    return $lang_string;
}
