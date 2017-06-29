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
 * Class Mdl_Clients
 */
class Mdl_Clients extends Response_Model
{
    public $table = 'ip_clients';
    public $primary_key = 'ip_clients.client_id';
    public $date_created_field = 'client_date_created';
    public $date_modified_field = 'client_date_modified';

    public function default_select()
    {
        $this->db->select(
            'SQL_CALC_FOUND_ROWS ' . $this->table . '.*, ' .
            'CONCAT(' . $this->table . '.client_name, " ", ' . $this->table . '.client_surname) as client_fullname'
            , false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
    }

    public function validation_rules()
    {
        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => trans('client_name'),
                'rules' => 'required'
            ),
            'client_surname' => array(
                'field' => 'client_surname',
                'label' => trans('client_surname')
            ),
            'client_active' => array(
                'field' => 'client_active'
            ),
            'client_language' => array(
                'field' => 'client_language',
                'label' => trans('language'),
            ),
            'client_address_1' => array(
                'field' => 'client_address_1'
            ),
            'client_address_2' => array(
                'field' => 'client_address_2'
            ),
            'client_city' => array(
                'field' => 'client_city'
            ),
            'client_state' => array(
                'field' => 'client_state'
            ),
            'client_zip' => array(
                'field' => 'client_zip'
            ),
            'client_country' => array(
                'field' => 'client_country'
            ),
            'client_phone' => array(
                'field' => 'client_phone'
            ),
            'client_fax' => array(
                'field' => 'client_fax'
            ),
            'client_mobile' => array(
                'field' => 'client_mobile'
            ),
            'client_email' => array(
                'field' => 'client_email'
            ),
            'client_web' => array(
                'field' => 'client_web'
            ),
            'client_vat_id' => array(
                'field' => 'user_vat_id'
            ),
            'client_tax_code' => array(
                'field' => 'user_tax_code'
            ),
            // SUMEX
            'client_birthdate' => array(
                'field' => 'client_birthdate',
                'rules' => 'callback_convert_date'
            ),
            'client_gender' => array(
                'field' => 'client_gender'
            ),
            'client_avs' => array(
                'field' => 'client_avs',
                'label' => trans('sumex_ssn'),
                'rules' => 'callback_fix_avs'
            ),
            'client_insurednumber' => array(
                'field' => 'client_insurednumber',
                'label' => trans('sumex_insurednumber')
            ),
            'client_veka' => array(
                'field' => 'client_veka',
                'label' => trans('sumex_veka')
            ),
        );
    }

    /**
     * @param int $amount
     * @return mixed
     */
    function get_latest($amount = 10)
    {
        return $this->mdl_clients
            ->where('client_active', 1)
            ->order_by('client_id', 'DESC')
            ->limit($amount)
            ->get()
            ->result();
    }

    /**
     * @param $input
     * @return string
     */
    function fix_avs($input)
    {
        if ($input != "") {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            } else if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return "";
    }

    function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['client_active'])) {
            $db_array['client_active'] = 0;
        }

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns client_id of existing client
     *
     * @param $client_name
     * @return int|null
     */
    public function client_lookup($client_name)
    {
        $client = $this->mdl_clients->where('client_name', $client_name)->get();

        if ($client->num_rows()) {
            $client_id = $client->row()->client_id;
        } else {
            $db_array = array(
                'client_name' => $client_name
            );

            $client_id = parent::save(null, $db_array);
        }

        return $client_id;
    }

    public function with_total()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_total', false);
        return $this;
    }

    public function with_total_paid()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_paid', false);
        return $this;
    }

    public function with_total_balance()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_balance) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_balance', false);
        return $this;
    }

    public function is_inactive()
    {
        $this->filter_where('client_active', 0);
        return $this;
    }

    /**
     * Returns clients with their invoice balances if exist.
     *
     * @param $status (client status (1:active, 0:inactive))
     * @return $result array (query result)
     */
    public function with_total_balance_with_currency($status)
    {
        $query = "SELECT ip_invoices.invoice_currency, ip_invoices.client_id, ip_clients.*, CONCAT(ip_clients.client_name, ' ', ip_clients.client_surname) as client_fullname,
                      SUM(ip_invoice_amounts.invoice_balance) AS client_invoice_balance FROM ip_invoices JOIN ip_invoice_amounts ON ip_invoice_amounts.invoice_id = ip_invoices.invoice_id
                      RIGHT JOIN ip_clients ON ip_clients.client_id = ip_invoices.client_id
                      WHERE ip_clients.client_active IN ?
                      GROUP BY ip_invoices.invoice_currency, ip_clients.client_id
                      ORDER BY ip_clients.client_name";
        $result = $this->db->query($query, array($status))->result();
        return $result;
    }

    /**
     * Returns client with their invoice balance, paid amount and total amount.
     *
     * @param $client_id
     * @return $result array (query result)
     */
    public function get_invoice_amounts_for_client($client_id)
    {
        $query = "SELECT ip_invoices.invoice_currency, ip_invoices.client_id, ip_clients.*, CONCAT(ip_clients.client_name, ' ', ip_clients.client_surname) as client_fullname,
                      SUM(ip_invoice_amounts.invoice_balance) AS client_invoice_balance, SUM(ip_invoice_amounts.invoice_paid) AS client_invoice_paid, SUM(ip_invoice_amounts.invoice_total) AS client_invoice_total
                      FROM ip_invoices JOIN ip_invoice_amounts ON ip_invoice_amounts.invoice_id = ip_invoices.invoice_id
                      RIGHT JOIN ip_clients ON ip_clients.client_id = ip_invoices.client_id
                      WHERE ip_clients.client_id = ?
                      GROUP BY ip_invoices.invoice_currency";
        $result = $this->db->query($query, array($client_id))->result();
        return $result;
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function get_not_assigned_to_user($user_id)
    {
        $this->load->model('user_clients/mdl_user_clients');
        $clients = $this->mdl_user_clients->select('ip_user_clients.client_id')
            ->assigned_to($user_id)->get()->result();

        $assigned_clients = [];
        foreach ($clients as $client) {
            $assigned_clients[] = $client->client_id;
        }

        if (count($assigned_clients) > 0) {
            $this->where_not_in('ip_clients.client_id', $assigned_clients);
        }

        $this->is_active();
        return $this->get()->result();
    }

    public function is_active()
    {
        $this->filter_where('client_active', 1);
        return $this;
    }

}
