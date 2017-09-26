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
 * Class Mdl_Projects
 */
class Mdl_Warehouses_Products extends Response_Model
{
    public $table = 'ip_warehouses_products';
    public $primary_key = 'ip_warehouses_products.war_pro_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_warehouses_products.war_pro_id');
    }

    public function get_latest()
    {
        $this->db->order_by('ip_warehouses_products.war_pro_id', 'DESC');
        return $this;
    }
    
    public function set_products_warehouse($product)
    {
        $this->db->trans_begin();
        
        $this->db->insert($this->table, $product);
        
        if ($this->db->trans_status() === FALSE) {
        	$this->db->trans_rollback();
        	return false;
        } else {        	
        	$this->db->trans_commit();
        	return true;
		}
    }

}
