<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Mdl_Items
 */
class Mdl_Items extends Response_Model
{

    public $table = 'ip_invoice_items';

    public $primary_key = 'ip_invoice_items.item_id';

    public $date_created_field = 'item_date_added';

    public function default_select()
    {
        $this->db->select('ip_invoice_item_amounts.*, ip_products.*, ip_invoice_items.*,
            item_tax_rates.tax_rate_percent AS item_tax_rate_percent,
            item_tax_rates.tax_rate_name AS item_tax_rate_name');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_invoice_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('ip_invoice_item_amounts', 'ip_invoice_item_amounts.item_id = ip_invoice_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_invoice_items.item_tax_rate_id', 'left');
        $this->db->join('ip_products', 'ip_products.product_id = ip_invoice_items.item_product_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_id' => [
                'field' => 'invoice_id',
                'label' => trans('invoice'),
                'rules' => 'required',
            ],
            'item_sku' => [
                'field' => 'item_sku',
                'label' => trans('item_sku'),
                'rules' => 'required|unique',
            ],
            'item_name' => [
                'field' => 'item_name',
                'label' => trans('item_name'),
                'rules' => 'required',
            ],
            'item_description' => [
                'field' => 'item_description',
                'label' => trans('description'),
            ],
            'item_quantity' => [
                'field' => 'item_quantity',
                'label' => trans('quantity'),
                'rules' => 'required',
            ],
            'item_price' => [
                'field' => 'item_price',
                'label' => trans('price'),
                'rules' => 'required',
            ],
            'item_tax_rate_id' => [
                'field' => 'item_tax_rate_id',
                'label' => trans('item_tax_rate'),
            ],
            'item_product_id' => [
                'field' => 'item_product_id',
                'label' => trans('original_product'),
            ],
            'item_date' => [
                'field' => 'item_date',
                'label' => trans('item_date'),
            ],
            'item_is_recurring' => [
                'field' => 'item_is_recurring',
                'label' => trans('recurring'),
            ],
        ];
    }

    /**
     * @param null $id
     * @param null $db_array
     *
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('invoices/mdl_item_amounts');
        $this->mdl_item_amounts->calculate($id);

        $this->load->model('invoices/mdl_invoice_amounts');

        if (is_object($db_array) && isset($db_array->invoice_id)) {
            $this->mdl_invoice_amounts->calculate($db_array->invoice_id);
        } elseif (is_array($db_array) && isset($db_array['invoice_id'])) {
            $this->mdl_invoice_amounts->calculate($db_array['invoice_id']);
        }

        return $id;
    }

    /**
     * @param int $item_id
     *
     * @return bool
     */
    public function delete($item_id)
    {
        // Get item:
        // the invoice id is needed to recalculate invoice amounts
        // and the task id to update status if the item refers a task
        $query = $this->db->get_where($this->table, ['item_id' => $item_id]);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        $invoice_id = $row->invoice_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_invoice_item_amounts');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        return true;
    }
}
