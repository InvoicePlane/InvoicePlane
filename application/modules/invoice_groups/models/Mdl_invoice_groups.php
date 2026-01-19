<?php

if ( ! defined('BASEPATH')) {
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

#[AllowDynamicProperties]
class Mdl_Invoice_Groups extends Response_Model
{
    public $table = 'ip_invoice_groups';

    public $primary_key = 'ip_invoice_groups.invoice_group_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_invoice_groups.invoice_group_name');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_group_name' => [
                'field' => 'invoice_group_name',
                'label' => trans('name'),
                'rules' => 'required',
            ],
            'invoice_group_identifier_format' => [
                'field' => 'invoice_group_identifier_format',
                'label' => trans('identifier_format'),
                'rules' => 'required',
            ],
            'invoice_group_next_id' => [
                'field' => 'invoice_group_next_id',
                'label' => trans('next_id'),
                'rules' => 'required',
            ],
            'invoice_group_left_pad' => [
                'field' => 'invoice_group_left_pad',
                'label' => trans('left_pad'),
                'rules' => 'required',
            ],
            'invoice_group_reset_monthly' => [
                'field' => 'invoice_group_reset_monthly',
                'label' => trans('reset_monthly'),
                'rules' => 'required|in_list[0,1]',
            ],
        ];
    }

    /**
     * @param      $invoice_group_id
     * @param bool $set_next
     *
     * @return mixed
     */
    public function generate_invoice_number($invoice_group_id, $set_next = true)
    {
        $invoice_group = $this->get_by_id($invoice_group_id);

        // Check if monthly reset is enabled and reset if needed
        if ($invoice_group->invoice_group_reset_monthly == 1) {
            $current_month = date('Y-m');
            
            // If last reset month is different from current month, reset the counter
            if ($invoice_group->invoice_group_last_reset_month !== $current_month) {
                $this->reset_invoice_number($invoice_group_id, $current_month);
                // Refresh the invoice group data after reset
                $invoice_group = $this->get_by_id($invoice_group_id);
            }
        }

        $invoice_identifier = $this->parse_identifier_format(
            $invoice_group->invoice_group_identifier_format,
            $invoice_group->invoice_group_next_id,
            $invoice_group->invoice_group_left_pad
        );

        if ($set_next) {
            $this->set_next_invoice_number($invoice_group_id);
        }

        return $invoice_identifier;
    }

    /**
     * @param $invoice_group_id
     */
    public function set_next_invoice_number($invoice_group_id)
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('invoice_group_next_id', 'invoice_group_next_id+1', false);
        $this->db->update($this->table);
    }

    /**
     * Reset invoice number to 1 and update last reset month
     *
     * @param $invoice_group_id
     * @param $current_month
     */
    public function reset_invoice_number($invoice_group_id, $current_month)
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('invoice_group_next_id', 1);
        $this->db->set('invoice_group_last_reset_month', $current_month);
        $this->db->update($this->table);
    }

    /**
     * @param $identifier_format
     * @param $next_id
     * @param $left_pad
     *
     * @return mixed
     */
    private function parse_identifier_format($identifier_format, string $next_id, int $left_pad)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $identifier_format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'year':
                        $replace = date('Y');
                        break;
                    case 'yy':
                        $replace = date('y');
                        break;
                    case 'month':
                        $replace = date('m');
                        break;
                    case 'day':
                        $replace = date('d');
                        break;
                    case 'id':
                        $replace = mb_str_pad($next_id, max($left_pad, 2), '0', STR_PAD_LEFT);
                        break;
                    default:
                        $replace = '';
                }

                $identifier_format = str_replace('{{{' . $var . '}}}', $replace, $identifier_format);
            }
        }

        return $identifier_format;
    }
}
