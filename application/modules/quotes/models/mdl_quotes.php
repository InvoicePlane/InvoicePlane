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

class Mdl_Quotes extends Response_Model
{
    public $table = 'ip_quotes';
    public $primary_key = 'ip_quotes.quote_id';
    public $date_modified_field = 'quote_date_modified';

    public function statuses()
    {
        return array(
            '1' => array(
                'label' => trans('draft'),
                'class' => 'draft',
                'href' => 'quotes/status/draft'
            ),
            '2' => array(
                'label' => trans('sent'),
                'class' => 'sent',
                'href' => 'quotes/status/sent'
            ),
            '3' => array(
                'label' => trans('viewed'),
                'class' => 'viewed',
                'href' => 'quotes/status/viewed'
            ),
            '4' => array(
                'label' => trans('approved'),
                'class' => 'approved',
                'href' => 'quotes/status/approved'
            ),
            '5' => array(
                'label' => trans('rejected'),
                'class' => 'rejected',
                'href' => 'quotes/status/rejected'
            ),
            '6' => array(
                'label' => trans('canceled'),
                'class' => 'canceled',
                'href' => 'quotes/status/canceled'
            )
        );
    }

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_quote_custom.*,
            ip_client_custom.*,
            ip_user_custom.*,
            ip_users.user_name,
			ip_users.user_company,
			ip_users.user_address_1,
			ip_users.user_address_2,
			ip_users.user_city,
			ip_users.user_state,
			ip_users.user_zip,
			ip_users.user_country,
			ip_users.user_phone,
			ip_users.user_fax,
			ip_users.user_mobile,
			ip_users.user_email,
			ip_users.user_web,
			ip_users.user_vat_id,
			ip_users.user_tax_code,
			ip_clients.*,
			ip_quote_amounts.quote_amount_id,
			IFnull(ip_quote_amounts.quote_item_subtotal, '0.00') AS quote_item_subtotal,
			IFnull(ip_quote_amounts.quote_item_tax_total, '0.00') AS quote_item_tax_total,
			IFnull(ip_quote_amounts.quote_tax_total, '0.00') AS quote_tax_total,
			IFnull(ip_quote_amounts.quote_total, '0.00') AS quote_total,
            ip_invoices.invoice_number,
			ip_quotes.*", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_quotes.quote_id DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_quotes.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_quotes.user_id');
        $this->db->join('ip_quote_amounts', 'ip_quote_amounts.quote_id = ip_quotes.quote_id', 'left');
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_quotes.invoice_id', 'left');
        $this->db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');
        $this->db->join('ip_user_custom', 'ip_user_custom.user_id = ip_users.user_id', 'left');
        $this->db->join('ip_quote_custom', 'ip_quote_custom.quote_id = ip_quotes.quote_id', 'left');
    }

    public function validation_rules()
    {
        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => trans('client'),
                'rules' => 'required'
            ),
            'quote_date_created' => array(
                'field' => 'quote_date_created',
                'label' => trans('quote_date'),
                'rules' => 'required'
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => trans('quote_group'),
                'rules' => 'required'
            ),
            'quote_password' => array(
                'field' => 'quote_password',
                'label' => trans('quote_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rule' => 'required'
            )
        );
    }

    public function validation_rules_save_quote()
    {
        return array(
            'quote_number' => array(
                'field' => 'quote_number',
                'label' => trans('quote') . ' #',
                'rules' => 'is_unique[ip_quotes.quote_number' . (($this->id) ? '.quote_id.' . $this->id : '') . ']'
            ),
            'quote_date_created' => array(
                'field' => 'quote_date_created',
                'label' => trans('date'),
                'rules' => 'required'
            ),
            'quote_date_expires' => array(
                'field' => 'quote_date_expires',
                'label' => trans('due_date'),
                'rules' => 'required'
            ),
            'quote_password' => array(
                'field' => 'quote_password',
                'label' => trans('quote_password')
            )
        );
    }

    public function create($db_array = null)
    {
        $quote_id = parent::save(null, $db_array);

        // Create an quote amount record
        $db_array = array(
            'quote_id' => $quote_id
        );

        $this->db->insert('ip_quote_amounts', $db_array);

        // Create the default invoice tax record if applicable
        if ($this->mdl_settings->setting('default_invoice_tax_rate')) {
            $db_array = array(
                'quote_id' => $quote_id,
                'tax_rate_id' => $this->mdl_settings->setting('default_invoice_tax_rate'),
                'include_item_tax' => $this->mdl_settings->setting('default_include_item_tax'),
                'quote_tax_rate_amount' => 0
            );

            $this->db->insert('ip_quote_tax_rates', $db_array);
        }

        return $quote_id;
    }

    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * Copies quote items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_quote($source_id, $target_id)
    {
        $this->load->model('quotes/mdl_quote_items');

        $quote_items = $this->mdl_quote_items->where('quote_id', $source_id)->get()->result();

        foreach ($quote_items as $quote_item) {
            $db_array = array(
                'quote_id' => $target_id,
                'item_tax_rate_id' => $quote_item->item_tax_rate_id,
                'item_name' => $quote_item->item_name,
                'item_description' => $quote_item->item_description,
                'item_quantity' => $quote_item->item_quantity,
                'item_price' => $quote_item->item_price,
                'item_order' => $quote_item->item_order
            );

            $this->mdl_quote_items->save(null, $db_array);
        }

        $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $source_id)->get()->result();

        foreach ($quote_tax_rates as $quote_tax_rate) {
            $db_array = array(
                'quote_id' => $target_id,
                'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                'include_item_tax' => $quote_tax_rate->include_item_tax,
                'quote_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount
            );

            $this->mdl_quote_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_quote_custom');
        $db_array = $this->mdl_quote_custom->where('quote_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['quote_custom_id']);
            $db_array['quote_id'] = $target_id;
            $this->mdl_quote_custom->save_custom($target_id, $db_array);
        }
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the client id for the submitted quote
        $this->load->model('clients/mdl_clients');
        $db_array['client_id'] = $this->mdl_clients->client_lookup($db_array['client_name']);
        unset($db_array['client_name']);

        $db_array['quote_date_created'] = date_to_mysql($db_array['quote_date_created']);
        $db_array['quote_date_expires'] = $this->get_date_due($db_array['quote_date_created']);

        $db_array['notes'] = $this->mdl_settings->setting('default_quote_notes');

        if (!isset($db_array['quote_status_id'])) {
            $db_array['quote_status_id'] = 1;
        }

        $generate_quote_number = $this->mdl_settings->setting('generate_quote_number_for_draft');

        if ($db_array['quote_status_id'] === 1 && $generate_quote_number == 1) {
            $db_array['quote_number'] = $this->get_quote_number($db_array['invoice_group_id']);
        } elseif ($db_array['quote_status_id'] != 1) {
            $db_array['quote_number'] = $this->get_quote_number($db_array['invoice_group_id']);
        } else {
            $db_array['quote_number'] = '';
        }

        // Generate the unique url key
        $db_array['quote_url_key'] = $this->get_url_key();

        return $db_array;
    }

    public function get_invoice_group_id($invoice_id)
    {
        $invoice = $this->get_by_id($invoice_id);
        return $invoice->invoice_group_id;
    }

    public function get_quote_number($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    public function get_date_due($quote_date_created)
    {
        $quote_date_expires = new DateTime($quote_date_created);
        $quote_date_expires->add(new DateInterval('P' . $this->mdl_settings->setting('quotes_expire_after') . 'D'));
        return $quote_date_expires->format('Y-m-d');
    }

    public function delete($quote_id)
    {
        parent::delete($quote_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function is_draft()
    {
        $this->filter_where('quote_status_id', 1);
        return $this;
    }

    public function is_sent()
    {
        $this->filter_where('quote_status_id', 2);
        return $this;
    }

    public function is_viewed()
    {
        $this->filter_where('quote_status_id', 3);
        return $this;
    }

    public function is_approved()
    {
        $this->filter_where('quote_status_id', 4);
        return $this;
    }

    public function is_rejected()
    {
        $this->filter_where('quote_status_id', 5);
        return $this;
    }

    public function is_canceled()
    {
        $this->filter_where('quote_status_id', 6);
        return $this;
    }

    // Used by guest module; includes only sent and viewed
    public function is_open()
    {
        $this->filter_where_in('quote_status_id', array(2, 3));
        return $this;
    }

    public function guest_visible()
    {
        $this->filter_where_in('quote_status_id', array(2, 3, 4, 5));
        return $this;
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_quotes.client_id', $client_id);
        return $this;
    }

    public function approve_quote_by_key($quote_url_key)
    {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    public function reject_quote_by_key($quote_url_key)
    {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    public function approve_quote_by_id($quote_id)
    {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    public function reject_quote_by_id($quote_id)
    {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    public function mark_viewed($quote_id)
    {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);

        $quote = $this->db->get('ip_quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->quote_status_id == 2) {
                $this->db->where('quote_id', $quote_id);
                $this->db->set('quote_status_id', 3);
                $this->db->update('ip_quotes');
            }
        }
    }

    public function mark_sent($quote_id)
    {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);

        $quote = $this->db->get('ip_quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->quote_status_id == 1) {
                $this->db->where('quote_id', $quote_id);
                $this->db->set('quote_status_id', 2);
                $this->db->update('ip_quotes');
            }
        }
    }

}
