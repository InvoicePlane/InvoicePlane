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
 * Class Mdl_Invoices_Recurring
 */
class Mdl_Invoices_Recurring extends Response_Model
{
    public $table = 'ip_invoices_recurring';
    public $primary_key = 'ip_invoices_recurring.invoice_recurring_id';
    public $recur_frequencies = array(
        '7D' => 'calendar_week_1',
        '14D' => 'calendar_week_2',
        '21D' => 'calendar_week_3',
        '28D' => 'calendar_week_4',
        '1M' => 'calendar_month_1',
        '2M' => 'calendar_month_2',
        '3M' => 'calendar_month_3',
        '4M' => 'calendar_month_4',
        '5M' => 'calendar_month_5',
        '6M' => 'calendar_month_6',
        '1Y' => 'calendar_year_1',
    );

    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS ip_invoices.*,
            ip_clients.client_name,
            ip_invoices_recurring.*,
            IF(recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00', 'active', 'inactive') AS recur_status", false);
    }

    public function default_join()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_invoices_recurring.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'rules' => 'required'
            ),
            'recur_start_date' => array(
                'field' => 'recur_start_date',
                'label' => trans('start_date'),
                'rules' => 'required'
            ),
            'recur_end_date' => array(
                'field' => 'recur_end_date',
                'label' => trans('end_date')
            ),
            'recur_frequency' => array(
                'field' => 'recur_frequency',
                'label' => trans('every'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['recur_start_date'] = date_to_mysql($db_array['recur_start_date']);
        $db_array['recur_next_date'] = $db_array['recur_start_date'];

        if ($db_array['recur_end_date']) {
            $db_array['recur_end_date'] = date_to_mysql($db_array['recur_end_date']);
        } else {
            $db_array['recur_end_date'] = '0000-00-00';
        }

        return $db_array;
    }

    /**
     * @param $invoice_recurring_id
     */
    public function stop($invoice_recurring_id)
    {
        $db_array = array(
            'recur_end_date' => date('Y-m-d'),
            'recur_next_date' => '0000-00-00'
        );

        $this->db->where('invoice_recurring_id', $invoice_recurring_id);
        $this->db->update('ip_invoices_recurring', $db_array);
    }

    /**
     * Sets filter to only recurring invoices which should be generated now
     * @return \Mdl_Invoices_Recurring
     */
    public function active()
    {
        $this->filter_where("recur_next_date <= date(NOW()) AND (recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00')");
        return $this;
    }

    /**
     * @param $invoice_recurring_id
     */
    public function set_next_recur_date($invoice_recurring_id)
    {
        $invoice_recurring = $this->where('invoice_recurring_id', $invoice_recurring_id)->get()->row();

        $recur_next_date = increment_date($invoice_recurring->recur_next_date, $invoice_recurring->recur_frequency);

        $db_array = array(
            'recur_next_date' => $recur_next_date
        );

        $this->db->where('invoice_recurring_id', $invoice_recurring_id);
        $this->db->update('ip_invoices_recurring', $db_array);
    }

}
