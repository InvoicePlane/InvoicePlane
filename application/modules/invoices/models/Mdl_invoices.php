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
 * Class Mdl_Invoices
 */
class Mdl_Invoices extends Response_Model
{
    public $table = 'ip_invoices';
    public $primary_key = 'ip_invoices.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    /**
     * @return array
     */
    public function statuses()
    {
        return array(
            '1' => array(
                'label' => trans('draft'),
                'class' => 'draft',
                'href' => 'invoices/status/draft'
            ),
            '2' => array(
                'label' => trans('sent'),
                'class' => 'sent',
                'href' => 'invoices/status/sent'
            ),
            '3' => array(
                'label' => trans('viewed'),
                'class' => 'viewed',
                'href' => 'invoices/status/viewed'
            ),
            '4' => array(
                'label' => trans('paid'),
                'class' => 'paid',
                'href' => 'invoices/status/paid'
            )
        );
    }

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS
            ip_quotes.*,
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
            ip_users.user_subscribernumber,
            ip_users.user_iban,
            ip_users.user_gln,
            ip_users.user_rcc,
			ip_clients.*,
            ip_invoice_sumex.*,
			ip_invoice_amounts.invoice_amount_id,
			IFnull(ip_invoice_amounts.invoice_item_subtotal, '0.00') AS invoice_item_subtotal,
			IFnull(ip_invoice_amounts.invoice_item_tax_total, '0.00') AS invoice_item_tax_total,
			IFnull(ip_invoice_amounts.invoice_tax_total, '0.00') AS invoice_tax_total,
			IFnull(ip_invoice_amounts.invoice_total, '0.00') AS invoice_total,
			IFnull(ip_invoice_amounts.invoice_paid, '0.00') AS invoice_paid,
			IFnull(ip_invoice_amounts.invoice_balance, '0.00') AS invoice_balance,
			ip_invoice_amounts.invoice_sign AS invoice_sign,
            (CASE WHEN ip_invoices.invoice_status_id NOT IN (1,4) AND DATEDIFF(NOW(), invoice_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
			DATEDIFF(NOW(), invoice_date_due) AS days_overdue,
            (CASE (SELECT COUNT(*) FROM ip_invoices_recurring WHERE ip_invoices_recurring.invoice_id = ip_invoices.invoice_id and ip_invoices_recurring.recur_next_date <> '0000-00-00') WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,
			ip_invoices.*", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_invoices.invoice_id DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_invoices.user_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_invoice_sumex', 'sumex_invoice = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_quotes', 'ip_quotes.invoice_id = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_payments', 'ip_payments.invoice_id = ip_invoices.invoice_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'client_id' => array(
                'field' => 'client_id',
                'label' => trans('client'),
                'rules' => 'required'
            ),
            'invoice_date_created' => array(
                'field' => 'invoice_date_created',
                'label' => trans('invoice_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => trans('invoice_group'),
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => trans('invoice_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rule' => 'required'
            ),
            'payment_method' => array(
                'field' => 'payment_method',
                'label' => trans('payment_method')
            ),
        );
    }

    /**
     * @return array
     */
    public function validation_rules_save_invoice()
    {
        return array(
            'invoice_number' => array(
                'field' => 'invoice_number',
                'label' => trans('invoice') . ' #',
                'rules' => 'is_unique[ip_invoices.invoice_number' . (($this->id) ? '.invoice_id.' . $this->id : '') . ']'
            ),
            'invoice_date_created' => array(
                'field' => 'invoice_date_created',
                'label' => trans('date'),
                'rules' => 'required'
            ),
            'invoice_date_due' => array(
                'field' => 'invoice_date_due',
                'label' => trans('due_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => trans('invoice_password')
            )
        );
    }

    /**
     * @param null $db_array
     * @param bool $include_invoice_tax_rates
     * @return int|null
     */
    public function create($db_array = null, $include_invoice_tax_rates = true)
    {

        $invoice_id = parent::save(null, $db_array);

        $inv = $this->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
        $invoice_group = $inv->invoice_group_id;

        // Create an invoice amount record
        $db_array = array(
            'invoice_id' => $invoice_id
        );

        $this->db->insert('ip_invoice_amounts', $db_array);

        if ($include_invoice_tax_rates) {
            // Create the default invoice tax record if applicable
            if (get_setting('default_invoice_tax_rate')) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => get_setting('default_invoice_tax_rate'),
                    'include_item_tax' => get_setting('default_include_item_tax', 0),
                    'invoice_tax_rate_amount' => 0
                );

                $this->db->insert('ip_invoice_tax_rates', $db_array);
            }
        }
        $invgroup = $this->mdl_invoice_groups->where('invoice_group_id', $invoice_group)->get()->row();
        if (preg_match("/sumex/i", $invgroup->invoice_group_name)) {
            // If the Invoice Group includes "Sumex", make the invoice a Sumex one
            $db_array = array(
                'sumex_invoice' => $invoice_id
            );
            $this->db->insert('ip_invoice_sumex', $db_array);
        }

        return $invoice_id;
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => $invoice_item->item_quantity,
                'item_price' => $invoice_item->item_price,
                'item_order' => $invoice_item->item_order
            );

            $this->mdl_items->save(null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => $invoice_tax_rate->invoice_tax_rate_amount
            );

            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_credit_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => -$invoice_item->item_quantity,
                'item_price' => $invoice_item->item_price,
                'item_order' => $invoice_item->item_order
            );

            $this->mdl_items->save(null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => -$invoice_tax_rate->invoice_tax_rate_amount
            );

            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the client id for the submitted invoice
        $this->load->model('clients/mdl_clients');

        // Check if is SUMEX
        $this->load->model('invoice_groups/mdl_invoice_groups');

        $db_array['invoice_date_created'] = date_to_mysql($db_array['invoice_date_created']);
        $db_array['invoice_date_due'] = $this->get_date_due($db_array['invoice_date_created']);
        $db_array['invoice_terms'] = get_setting('default_invoice_terms');

        if (!isset($db_array['invoice_status_id'])) {
            $db_array['invoice_status_id'] = 1;
        }

        $generate_invoice_number = get_setting('generate_invoice_number_for_draft');

        if ($db_array['invoice_status_id'] === 1 && $generate_invoice_number == 1) {
            $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        } elseif ($db_array['invoice_status_id'] != 1) {
            $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        } else {
            $db_array['invoice_number'] = '';
        }

        // Set default values
        $db_array['payment_method'] = (empty($db_array['payment_method']) ? 0 : $db_array['payment_method']);

        // Generate the unique url key
        $db_array['invoice_url_key'] = $this->get_url_key();

        return $db_array;
    }

    /**
     * @param string $invoice_date_created
     * @return string
     */
    public function get_date_due($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . get_setting('invoices_due_after') . 'D'));
        return $invoice_date_due->format('Y-m-d');
    }

    /**
     * @param $invoice_group_id
     * @return mixed
     */
    public function get_invoice_number($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    /**
     * @return string
     */
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * @param $invoice_id
     * @return mixed
     */
    public function get_invoice_group_id($invoice_id)
    {
        $invoice = $this->get_by_id($invoice_id);
        return $invoice->invoice_group_id;
    }

    /**
     * @param int $parent_invoice_id
     * @return mixed
     */
    public function get_parent_invoice_number($parent_invoice_id)
    {
        $parent_invoice = $this->get_by_id($parent_invoice_id);
        return $parent_invoice->invoice_number;
    }

    /**
     * @return mixed
     */
    public function get_custom_values($id)
    {
        $this->load->module('custom_fields/Mdl_invoice_custom');
        return $this->invoice_custom->get_by_invid($id);
    }


    /**
     * @param int $invoice_id
     */
    public function delete($invoice_id)
    {
        parent::delete($invoice_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    // Used from the guest module, excludes draft and paid
    public function is_open()
    {
        $this->filter_where_in('invoice_status_id', array(2, 3));
        return $this;
    }

    // Used to check if the invoice is Sumex
    public function is_sumex()
    {
        $this->where('sumex_id is NOT NULL', null, FALSE);
        return $this;
    }

    public function guest_visible()
    {
        $this->filter_where_in('invoice_status_id', array(2, 3, 4));
        return $this;
    }

    public function is_draft()
    {
        $this->filter_where('invoice_status_id', 1);
        return $this;
    }

    public function is_sent()
    {
        $this->filter_where('invoice_status_id', 2);
        return $this;
    }

    public function is_viewed()
    {
        $this->filter_where('invoice_status_id', 3);
        return $this;
    }

    public function is_paid()
    {
        $this->filter_where('invoice_status_id', 4);
        return $this;
    }

    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);
        return $this;
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_invoices.client_id', $client_id);
        return $this;
    }

    /**
     * @param $invoice_id
     */
    public function mark_viewed($invoice_id)
    {
        $invoice = $this->get_by_id($invoice_id);

        if (!empty($invoice)) {
            if ($invoice->invoice_status_id == 2) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 3);
                $this->db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 3) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        }
    }

    /**
     * @param $invoice_id
     */
    public function mark_sent($invoice_id)
    {
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        if (!empty($invoice)) {
            if ($invoice->invoice_status_id == 1) {
                // Generate new invoice number if applicable
                if (get_setting('generate_invoice_number_for_draft') == 0) {
                    $invoice_number = $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id);
                } else {
                    $invoice_number = $invoice->invoice_number;
                }

                // Set new date and save
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 2);
                $this->db->set('invoice_number', $invoice_number);
                $this->db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 2) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        }
    }

}
