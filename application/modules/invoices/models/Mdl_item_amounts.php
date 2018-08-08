<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

include(__DIR__ . '/ItemInterface.php');
include(__DIR__ . '/ItemFactory.php');
include(__DIR__ . '/ItemBase.php');
include(__DIR__ . '/ItemLegacy.php');
include(__DIR__ . '/Item.php');

/**
 * Class Mdl_Item_Amounts
 */
class Mdl_Item_Amounts extends CI_Model
{

    /**
     * @param $item_id
     */
    public function calculate($item_id)
    {

        $this->load->model('invoices/mdl_items');
        $item = $this->mdl_items->get_by_id($item_id);

        $ItemFactory = new ItemFactory();
        $Item = $ItemFactory->get_item($item);
        $db_array = $Item->get_values();

        $this->db->where('item_id', $item_id);

        if ($this->db->get('ip_invoice_item_amounts')->num_rows()) {
            $this->db->where('item_id', $item_id);
            $this->db->update('ip_invoice_item_amounts', $db_array);
        } else {
            $this->db->insert('ip_invoice_item_amounts', $db_array);
        }
    }
}
