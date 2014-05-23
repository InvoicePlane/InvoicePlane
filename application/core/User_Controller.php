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

class User_Controller extends Base_Controller {

	public function __construct($required_key, $required_val)
	{
		parent::__construct();

		if ($this->session->userdata($required_key) <> $required_val)
		{
			redirect('sessions/login');
		}
	}

}

?>
