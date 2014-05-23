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

function delete_orphans()
{
    $CI =& get_instance();
    
    $queries = array(
        'DELETE FROM fi_invoices WHERE client_id NOT IN (SELECT client_id FROM fi_clients)',
        'DELETE FROM fi_quotes WHERE client_id NOT IN (SELECT client_id FROM fi_clients)',
        'DELETE FROM fi_invoice_amounts WHERE invoice_id NOT IN (SELECT invoice_id FROM fi_invoices)',
        'DELETE FROM fi_quote_amounts WHERE quote_id NOT IN (SELECT quote_id FROM fi_quotes)',
        'DELETE FROM fi_payments WHERE invoice_id NOT IN (SELECT invoice_id FROM fi_invoices)',
        'DELETE FROM fi_client_custom WHERE client_id NOT IN (SELECT client_id FROM fi_clients)',
        'DELETE FROM fi_invoice_custom WHERE invoice_id NOT IN (SELECT invoice_id FROM fi_invoices)',
        'DELETE FROM fi_user_custom WHERE user_id NOT IN (SELECT user_id FROM fi_users)',
        'DELETE FROM fi_payment_custom WHERE payment_id NOT IN (SELECT payment_id FROM fi_payments)',
        'DELETE FROM fi_quote_custom WHERE quote_id NOT IN (SELECT quote_id FROM fi_quotes)',
        'DELETE FROM fi_invoice_items WHERE invoice_id NOT IN (SELECT invoice_id FROM fi_invoices)',
        'DELETE FROM fi_invoice_item_amounts WHERE item_id NOT IN (SELECT item_id FROM fi_invoice_items)',
        'DELETE FROM fi_quote_items WHERE quote_id NOT IN (SELECT quote_id FROM fi_quotes)',
        'DELETE FROM fi_quote_item_amounts WHERE item_id NOT IN (SELECT item_id FROM fi_quote_items)',
        'DELETE FROM fi_client_notes WHERE client_id NOT IN (SELECT client_id FROM fi_clients)'
    );
    
    foreach ($queries as $query)
    {
        $CI->db->query($query);
    }
}

?>