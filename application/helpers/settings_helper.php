<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Get a setting value
 *
 * @param string $setting_key
 * @param mixed $default
 * @return string
 */
function get_setting($setting_key, $default = '')
{
    $CI = &get_instance();
    return $CI->mdl_settings->setting($setting_key, $default);
}
