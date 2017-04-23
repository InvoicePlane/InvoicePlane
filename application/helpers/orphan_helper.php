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
 * Deletes orphaned entries in the database
 */
function delete_orphans()
{
    $CI =& get_instance();

    $queries = array(
        'DELETE FROM ip_invoices WHERE client_id NOT IN (SELECT client_id FROM ip_clients)',
        'DELETE FROM ip_quotes WHERE client_id NOT IN (SELECT client_id FROM ip_clients)',
        'DELETE FROM ip_invoice_amounts WHERE invoice_id NOT IN (SELECT invoice_id FROM ip_invoices)',
        'DELETE FROM ip_quote_amounts WHERE quote_id NOT IN (SELECT quote_id FROM ip_quotes)',
        'DELETE FROM ip_payments WHERE invoice_id NOT IN (SELECT invoice_id FROM ip_invoices)',
        'DELETE FROM ip_client_custom WHERE client_id NOT IN (SELECT client_id FROM ip_clients)',
        'DELETE FROM ip_invoice_custom WHERE invoice_id NOT IN (SELECT invoice_id FROM ip_invoices)',
        'DELETE FROM ip_user_custom WHERE user_id NOT IN (SELECT user_id FROM ip_users)',
        'DELETE FROM ip_payment_custom WHERE payment_id NOT IN (SELECT payment_id FROM ip_payments)',
        'DELETE FROM ip_quote_custom WHERE quote_id NOT IN (SELECT quote_id FROM ip_quotes)',
        'DELETE FROM ip_invoice_items WHERE invoice_id NOT IN (SELECT invoice_id FROM ip_invoices)',
        'DELETE FROM ip_invoice_item_amounts WHERE item_id NOT IN (SELECT item_id FROM ip_invoice_items)',
        'DELETE FROM ip_quote_items WHERE quote_id NOT IN (SELECT quote_id FROM ip_quotes)',
        'DELETE FROM ip_quote_item_amounts WHERE item_id NOT IN (SELECT item_id FROM ip_quote_items)',
        'DELETE FROM ip_client_notes WHERE client_id NOT IN (SELECT client_id FROM ip_clients)'
    );

    foreach ($queries as $query) {
        $CI->db->query($query);
    }
}
