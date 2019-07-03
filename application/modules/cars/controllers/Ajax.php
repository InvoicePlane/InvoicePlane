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
class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;
    public function add()
    {
		
		
		
        $this->load->model('cars/mdl_cars');
        if ($this->mdl_cars->run_validation()) {
            $this->mdl_cars->save();
			/*still need to implement the save function*/
            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }
        echo json_encode($response);
    }
}
