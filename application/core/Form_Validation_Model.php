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

class Form_Validation_Model extends MY_Model {
    
    public function __construct()
    {
       parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }
    
}

?>