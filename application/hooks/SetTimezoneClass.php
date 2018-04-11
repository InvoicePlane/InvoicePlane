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
 * Class SetTimezoneClass
 */
class  SetTimezoneClass
{
    /**
     * Set UTC as the current timezone if no one was set in the PHP ini
     */
    public function setTimezone()
    {
        $this->CI->load->helper('settings');
        $this->CI->load->model('settings/mdl_settings');
        
        if (get_setting('default_timezone')) {
            date_default_timezone_set(get_setting('default_timezone'));
            //ini_set('date.timezone', get_setting('default_timezone'));
        } else {
            if (!ini_get('date.timezone')) {
                date_default_timezone_set('UTC');
                //ini_set('date.timezone', 'UTC');
            }
        }
    }
}
