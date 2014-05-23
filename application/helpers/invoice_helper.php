<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

function invoice_logo()
{
    $CI = & get_instance();
	
    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }
    return '';
}

function invoice_logo_pdf()
{
    $CI = & get_instance();

    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . getcwd() . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }
    return '';
}

?>