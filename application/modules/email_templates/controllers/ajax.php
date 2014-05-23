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

class Ajax extends Admin_Controller {

    public $ajax_controller = TRUE;
    
    public function get_content()
    {
        $this->load->model('email_templates/mdl_email_templates');
        
        echo $this->mdl_email_templates->get_by_id($this->input->post('email_template_id'))->email_template_body;
    }
    
}