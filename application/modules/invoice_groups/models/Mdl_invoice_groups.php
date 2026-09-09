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
                'rules' => 'required|in_list[0,1]|callback_validate_reset_monthly_format',
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
            if ($this->should_reset_monthly($invoice_group_id)) {
                $this->reset_invoice_number($invoice_group_id);
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
     * Check if invoice number should be reset based on monthly reset setting
     * Compares the current month with the month of the most recent invoice.
     *
     * @param int $invoice_group_id
     *
     * @return bool
     */
    public function should_reset_monthly(int $invoice_group_id): bool
    {
        // Get the most recent invoice for this invoice group
        $this->db->select('invoice_date_created');
        $this->db->from('ip_invoices');
        $this->db->where('invoice_group_id', $invoice_group_id);
        $this->db->order_by('invoice_date_created', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() === 0) {
            // No invoices yet, no need to reset
            return false;
        }

        $last_invoice = $query->row();
        
        // Guard against invalid date values
        if (empty($last_invoice->invoice_date_created)) {
            return false;
        }
        
        $timestamp = strtotime($last_invoice->invoice_date_created);
        if ($timestamp === false) {
            return false;
        }
        
        $last_invoice_month = date('Y-m', $timestamp);
        $current_month      = date('Y-m');

        // Reset if the last invoice was created in a different month
        return $last_invoice_month !== $current_month;
    }

    /**
     * Reset invoice number to 1.
     *
     * @param int $invoice_group_id
     *
     * @return void
     */
    public function reset_invoice_number(int $invoice_group_id): void
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('invoice_group_next_id', 1);
        $this->db->update($this->table);
    }

    /**
     * Validate that monthly reset is only enabled when identifier format includes month/year
     * to prevent duplicate invoice numbers.
     *
     * @param string $reset_monthly
     *
     * @return bool
     */
    public function validate_reset_monthly_format(string $reset_monthly): bool
    {
        // Only validate if monthly reset is enabled
        if ($reset_monthly != '1') {
            return true;
        }

        $format = $this->input->post('invoice_group_identifier_format');
        
        // Check if format includes month or year placeholders
        $has_month = str_contains($format, '{{{month}}}');
        $has_year = str_contains($format, '{{{year}}}') || str_contains($format, '{{{yy}}}');
        
        if ( ! $has_month && ! $has_year) {
            $this->form_validation->set_message(
                'validate_reset_monthly_format',
                'Monthly reset requires the identifier format to include {{{month}}} and/or {{{year}}} to prevent duplicate invoice numbers.'
            );

            return false;
        }

        return true;
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
                        $replace = str_pad($next_id, (int) $left_pad, '0', STR_PAD_LEFT);
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
