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
    //public $ajax_controller = TRUE;
    public function modal_product_lookups()
    {
        //$filter_family  = $this->input->get('filter_family');
        $filter_product = $this->input->get('filter_product');

        $this->load->model('mdl_products');
        $this->load->model('families/mdl_families');

        // Apply filters
        /*
        if((int)$filter_family) {
            $products = $this->mdl_products->by_family($filter_family);
        }
        */

        if (!empty($filter_product)) {
            $products = $this->mdl_products->by_product($filter_product);
        }
        $products = $this->mdl_products->get();
        $products = $this->mdl_products->result();

        $families = $this->mdl_families->get()->result();

        $data = array(
            'products' => $products,
            'families' => $families,
            'filter_product' => $filter_product,
            //'filter_family'  => $filter_family,
        );

        $this->layout->load_view('products/modal_product_lookups', $data);
    }

    public function process_product_selections()
    {
        $this->load->model('mdl_products');

        $products = $this->mdl_products->where_in('product_id', $this->input->post('product_ids'))->get()->result();

        foreach ($products as $product) {
            $product->product_price = format_amount($product->product_price);
        }

        echo json_encode($products);
    }

}
