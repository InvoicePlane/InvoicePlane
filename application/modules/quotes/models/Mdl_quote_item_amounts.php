<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

include(__DIR__ . '/QuoteItemInterface.php');
include(__DIR__ . '/QuoteItemFactory.php');
include(__DIR__ . '/QuoteItemBase.php');
include(__DIR__ . '/QuoteItemLegacy.php');
include(__DIR__ . '/QuoteItem.php');

/*
 * quotePlane
 *
 * @author		quotePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 quotePlane.com
 * @license		https://quoteplane.com/license.txt
 * @link		https://quoteplane.com
 */

/**
 * Class Mdl_Quote_Item_Amounts
 */
class Mdl_Quote_Item_Amounts extends CI_Model
{
    /**
     * @param $item_id
     */
    public function calculate($item_id)
    {
        $this->load->model('quotes/mdl_quote_items');
        $item = $this->mdl_quote_items->get_by_id($item_id);

        $ItemFactory = new QuoteItemFactory();
        $Item = $ItemFactory->get_item($item);
        $db_array = $Item->get_values();

        $this->db->where('item_id', $item_id);

        if ($this->db->get('ip_quote_item_amounts')->num_rows()) {
            $this->db->where('item_id', $item_id);
            $this->db->update('ip_quote_item_amounts', $db_array);
        } else {
            $this->db->insert('ip_quote_item_amounts', $db_array);
        }
    }
}
