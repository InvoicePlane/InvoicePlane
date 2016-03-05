<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

    public function validation_rules()
    {
        return array(
            'invoice_group_name' => array(
                'field' => 'invoice_group_name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'invoice_group_identifier_format' => array(
                'field' => 'invoice_group_identifier_format',
                'label' => lang('identifier_format'),
                'rules' => 'required'
            ),
            'invoice_group_next_id' => array(
                'field' => 'invoice_group_next_id',
                'label' => lang('next_id'),
                'rules' => 'required'
            ),
            'invoice_group_left_pad' => array(
                'field' => 'invoice_group_left_pad',
                'label' => lang('left_pad'),
                'rules' => 'required'
            ),
            'invoice_group_pdf_template' => array(
                'field' => 'invoice_group_pdf_template',
                'label' => lang('default_pdf_template')            )
        );
    }

    public function generate_invoice_number($invoice_group_id, $client_id, $set_next = true)
    {
        $invoice_group = $this->get_by_id($invoice_group_id);

        $invoice_identifier = $this->parse_identifier_format(
            $invoice_group->invoice_group_identifier_format,
            $invoice_group->invoice_group_next_id,
            $invoice_group->invoice_group_left_pad,
            $client_id
        );

        if ($set_next) {
            $this->set_next_invoice_number($invoice_group_id);
        }

        return $invoice_identifier;
    }

    private function parse_identifier_format($identifier_format, $next_id, $left_pad, $client_id)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $identifier_format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                $replace = $this->parse_identifier_variable($var, $next_id, $left_pad, $client_id);
                $identifier_format = str_replace('{{{' . $var . '}}}', $replace, $identifier_format);
            }
        }

        return $identifier_format;
    }

    private function parse_identifier_variable($name, $next_id, $left_pad, $client_id)
    {
        if ($name == 'year') {
            return date('Y');
        }

        if ($name == 'month') {
            return date('m');
        }

        if ($name == 'day') {
            return date('d');
        }

        if ($name == 'id') {
            return str_pad($next_id, $left_pad, '0', STR_PAD_LEFT);
        }

        if (strpos($name, 'client:') === 0) {
            $label = substr($name, 7);

            $this->load->model('custom_fields/mdl_custom_fields');
            $field = $this->mdl_custom_fields->by_table('ip_client_custom')
                          ->where('custom_field_label', $label)->get()->row();

            if (!$field) {
                return '';
            }

            $this->load->model('custom_fields/mdl_client_custom');
            $value_row = $this->mdl_client_custom->where('client_id', $client_id)->get()->row();

            if (!$value_row) {
                return '';
            }

            return $value_row->{$field->custom_field_column};
        }

        return '';
    }

    public function set_next_invoice_number($invoice_group_id)
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('invoice_group_next_id', 'invoice_group_next_id+1', false);
        $this->db->update($this->table);
    }

}
