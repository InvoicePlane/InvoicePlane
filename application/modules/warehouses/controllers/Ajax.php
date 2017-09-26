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
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function set_products_warehouse()
    {
        $this->load->model('warehouses/mdl_warehouses_products');
        $this->load->model('products/mdl_products');
        
        $products = $this->input->post('products');
        
        $response = array(
            'success' => 1
        );
        
        for ($i = 0; $i < count($products); $i++) {
            $product = $this->mdl_products
                    ->where('ip_products.product_id', $products[$i]['product_id'])
                    ->get()->row();
            
            $aux = array(
                'user_id' => $this->session->userdata('user_id'),
                'war_pro_date' => date("Y-m-d H:i:s"),
                'war_pro_type' => $this->input->post('war_pro_type'),
                'product_id' => $products[$i]['product_id'],
                'product_qty' => $products[$i]['product_qty']
            );
            
            if ($this->input->post('war_pro_type') == 0) {
                $aux['warehouse_id'] = $this->input->post('warehouse_id');
            }
            
            if ($this->mdl_warehouses_products->set_products_warehouse($aux)) { 
                
                if ($this->input->post('war_pro_type') == 0) {
                    //PRODUCTS ENTRY
                    $qty = $product->product_qty + $products[$i]['product_qty'];
                    $aux_product['warehouse_id'] = $aux['warehouse_id'];
                } else {
                    //PRODUCTS EXIT
                    $qty = $product->product_qty - $products[$i]['product_qty'];
                    
                    if ($qty < 0) {
                        $qty = 0;
                    }
                }
                
                $aux_product['product_qty'] = $qty;
                
                $this->mdl_products->save($product->product_id, $aux_product);
                
            } else {
                $this->load->helper('json_error');
                $response['success'] = 0;
                $response['validation_errors'] = json_errors();
            }
        }
        
        echo json_encode($response);
    }

    public function modal_warehouse_products_entry()
    {
        $this->load->model('warehouses/mdl_warehouses');
        $this->load->model('products/mdl_products');

        $data = array(
            'warehouses' => $this->mdl_warehouses->get()->result(),
            'warehouse' => $this->mdl_warehouses->get_by_id($this->input->post('warehouse_id')),
            'products' => $this->mdl_products->get()->result(),
        );

        $this->layout->load_view('warehouses/modal_warehouse_products_entry', $data);
    }
    
    public function modal_warehouse_products_exit()
    {
        $this->load->model('warehouses/mdl_warehouses');
        $this->load->model('products/mdl_products');

        $data = array(
            'warehouses' => $this->mdl_warehouses->get()->result(),
            'warehouse' => $this->mdl_warehouses->get_by_id($this->input->post('warehouse_id')),
            'products' => $this->mdl_products->get()->result(),
        );

        $this->layout->load_view('warehouses/modal_warehouse_products_exit', $data);
    }

}
