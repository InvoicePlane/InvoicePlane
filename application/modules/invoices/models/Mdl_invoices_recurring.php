<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Invoices_Recurring extends Response_Model
{
    public $table = 'ip_invoices_recurring';

    public $primary_key = 'ip_invoices_recurring.invoice_recurring_id';

    public $recur_frequencies = [
        '1D'  => 'calendar_day_1',
        '2D'  => 'calendar_day_2',
        '3D'  => 'calendar_day_3',
        '4D'  => 'calendar_day_4',
        '5D'  => 'calendar_day_5',
        '6D'  => 'calendar_day_6',
        '15D' => 'calendar_day_15',
        '30D' => 'calendar_day_30',
        '7D'  => 'calendar_week_1',
        '14D' => 'calendar_week_2',
        '21D' => 'calendar_week_3',
        '28D' => 'calendar_week_4',
        '1M'  => 'calendar_month_1',
        '2M'  => 'calendar_month_2',
        '3M'  => 'calendar_month_3',
        '4M'  => 'calendar_month_4',
        '5M'  => 'calendar_month_5',
        '6M'  => 'calendar_month_6',
        '7M'  => 'calendar_month_7',
        '8M'  => 'calendar_month_8',
        '9M'  => 'calendar_month_9',
        '10M' => 'calendar_month_10',
        '11M' => 'calendar_month_11',
        '1Y'  => 'calendar_year_1',
        '2Y'  => 'calendar_year_2',
        '3Y'  => 'calendar_year_3',
        '4Y'  => 'calendar_year_4',
        '5Y'  => 'calendar_year_5',
    ];

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_invoices.*,
            ip_clients.client_title,
            ip_clients.client_name,
            ip_clients.client_surname,
            ip_invoices_recurring.*,
            IF(recur_end_date > date(NOW()) OR recur_end_date IS NULL, "active", "inactive") AS recur_status', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('recur_status ASC, recur_next_date ASC');
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
        return [
            'invoice_id' => [
                'field' => 'invoice_id',
                'rules' => 'required',
            ],
            'recur_start_date' => [
                'field' => 'recur_start_date',
                'label' => trans('start_date'),
                'rules' => 'required',
            ],
            'recur_end_date' => [
                'field' => 'recur_end_date',
                'label' => trans('end_date'),
            ],
            'recur_frequency' => [
                'field' => 'recur_frequency',
                'label' => trans('every'),
                'rules' => 'required',
            ],
            'generate_if_unpaid' => [
                'field' => 'generate_if_unpaid',
            ],
        ];
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['recur_start_date'] = date_to_mysql($db_array['recur_start_date']);
        $db_array['recur_next_date']  = $db_array['recur_start_date'];

        $db_array['recur_end_date'] = $db_array['recur_end_date'] ? date_to_mysql($db_array['recur_end_date']) : null;

        return $db_array;
    }

    /**
     * @param $invoice_recurring_id
     */
    public function stop($invoice_recurring_id)
    {
        $db_array = [
            'recur_end_date'  => date('Y-m-d'),
            'recur_next_date' => null,
        ];

        $this->db->where('invoice_recurring_id', $invoice_recurring_id);
        $this->db->update('ip_invoices_recurring', $db_array);
    }

    /**
     * Sets filter to only recurring invoices which should be generated now.
     *
     * @return \Mdl_Invoices_Recurring
     */
    public function active()
    {
        // Base date filter: only invoices due to recur now and not ended
        $this->filter_where('recur_next_date <= date(NOW()) AND (recur_end_date > date(NOW()) OR recur_end_date IS NULL)');

        // Apply payment-based filtering: exclude recurring invoices with unpaid generated invoices
        // when generate_if_unpaid = 0
        // Note: The NOT EXISTS subquery uses hardcoded table/column names (not user input) so SQL injection is not a risk
        $this->db->group_start();
        $this->db->where('ip_invoices_recurring.generate_if_unpaid', 1);
        $this->db->or_group_start();
        $this->db->where('ip_invoices_recurring.generate_if_unpaid', 0);
        $this->db->where('NOT EXISTS (
            SELECT 1
            FROM ip_invoices AS generated_inv
            JOIN ip_invoice_amounts AS inv_amt ON inv_amt.invoice_id = generated_inv.invoice_id
            WHERE generated_inv.invoice_recurring_id = ip_invoices_recurring.invoice_recurring_id
              AND inv_amt.invoice_balance > 0
        )', null, false);
        $this->db->group_end();
        $this->db->group_end();

        return $this;
    }

    /**
     * @param $invoice_recurring_id
     */
    public function set_next_recur_date($invoice_recurring_id)
    {
        $invoice_recurring = $this->where('invoice_recurring_id', $invoice_recurring_id)->get()->row();

        $recur_next_date = increment_date($invoice_recurring->recur_next_date, $invoice_recurring->recur_frequency);

        $db_array = [
            'recur_next_date' => $recur_next_date,
        ];

        $this->db->where('invoice_recurring_id', $invoice_recurring_id);
        $this->db->update('ip_invoices_recurring', $db_array);
    }
}
