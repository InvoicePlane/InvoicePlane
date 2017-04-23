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
 * Class Mdl_Quote_Items
 */
class Mdl_Quote_Items extends Response_Model
{
    public $table = 'ip_quote_items';
    public $primary_key = 'ip_quote_items.item_id';
    public $date_created_field = 'item_date_added';

    public function default_select()
    {
        $this->db->select('ip_quote_item_amounts.*, ip_quote_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_quote_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('ip_quote_item_amounts', 'ip_quote_item_amounts.item_id = ip_quote_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_quote_items.item_tax_rate_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'quote_id' => array(
                'field' => 'quote_id',
                'label' => trans('quote'),
                'rules' => 'required'
            ),
            'item_name' => array(
                'field' => 'item_name',
                'label' => trans('item_name'),
                'rules' => 'required'
            ),
            'item_description' => array(
                'field' => 'item_description',
                'label' => trans('description')
            ),
            'item_quantity' => array(
                'field' => 'item_quantity',
                'label' => trans('quantity'),
            ),
            'item_price' => array(
                'field' => 'item_price',
                'label' => trans('price'),
            ),
            'item_tax_rate_id' => array(
                'field' => 'item_tax_rate_id',
                'label' => trans('item_tax_rate')
            ),
            'item_product_id' => array(
                'field' => 'item_product_id',
                'label' => trans('original_product')
            ),
        );
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('quotes/mdl_quote_item_amounts');
        $this->mdl_quote_item_amounts->calculate($id);

        $this->load->model('quotes/mdl_quote_amounts');

        if (is_object($db_array) && isset($db_array->quote_id)) {
            $this->mdl_quote_amounts->calculate($db_array->quote_id);
        } elseif (is_array($db_array) && isset($db_array['quote_id'])) {
            $this->mdl_quote_amounts->calculate($db_array['quote_id']);
        }

        return $id;
    }

    /**
     * @param int $item_id
     */
    public function delete($item_id)
    {
        // Get the quote id so we can recalculate quote amounts
        $this->db->select('quote_id');
        $this->db->where('item_id', $item_id);
        $quote_id = $this->db->get('ip_quote_items')->row()->quote_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_quote_item_amounts');

        // Recalculate quote amounts
        $this->load->model('quotes/mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);
    }

}
