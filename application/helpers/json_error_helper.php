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

function json_errors()
{
	// Ok, gotta think of a better name for this function. It doesn't return 
	// json itself but is called from something which will.
	
	$return = array();
	
	foreach (array_keys($_POST) as $key)
	{
		if (form_error($key))
		{
			$return[$key] = form_error($key);
		}
	}
	
	return $return;
}

?>