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
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Ajax extends Admin_Controller {

    public $ajax_controller = TRUE;

    public function modal_product_lookups()
    {
        $this->load->model('mdl_products');
        
        $data = array(
            'products' => $this->mdl_products->get()->result()
        );

        $this->layout->load_view('products/modal_product_lookups', $data);
    }
    
    public function process_product_selections()
    {
        $this->load->model('mdl_products');
        
        $products = $this->mdl_products->where_in('product_id', $this->input->post('product_ids'))->get()->result();
		
		foreach ($products as $product)
		{
			$product->product_price = format_amount($product->product_price);
		}
        
		//header('Content-Type: application/json');
        echo json_encode($products);
    }

}

?>