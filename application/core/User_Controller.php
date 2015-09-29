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

class User_Controller extends Base_Controller
{

    public function __construct($required_key, $required_val)
    {
        parent::__construct();

        if (ENVIRONMENT == 'testing') {
            return;
        }

        if ($this->session->userdata($required_key) <> $required_val) {
            redirect('sessions/login');
        }
    }

}

?>
