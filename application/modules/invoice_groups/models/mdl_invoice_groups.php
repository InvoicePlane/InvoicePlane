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

class Mdl_Invoice_Groups extends Response_Model {

    public $table       = 'fi_invoice_groups';
    public $primary_key = 'fi_invoice_groups.invoice_group_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    public function default_order_by()
    {
        $this->db->order_by('fi_invoice_groups.invoice_group_name');
    }

    public function validation_rules()
    {
        return array(
            'invoice_group_name'         => array(
                'field' => 'invoice_group_name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'invoice_group_prefix'       => array(
                'field' => 'invoice_group_prefix',
                'label' => lang('prefix')
            ),
            'invoice_group_next_id'      => array(
                'field' => 'invoice_group_next_id',
                'label' => lang('next_id'),
                'rules' => 'required'
            ),
            'invoice_group_left_pad'     => array(
                'field' => 'invoice_group_left_pad',
                'label' => lang('left_pad'),
                'rules' => 'required'
            ),
            'invoice_group_prefix_year'  => array(
                'field' => 'invoice_group_prefix_year',
                'label' => lang('year_prefix'),
                'rules' => 'required'
            ),
            'invoice_group_prefix_month' => array(
                'field' => 'invoice_group_prefix_month',
                'label' => lang('month_prefix'),
                'rules' => 'required'
            )
        );
    }

    public function generate_invoice_number($invoice_group_id, $set_next = TRUE)
    {
        $invoice_group = $this->get_by_id($invoice_group_id);

        $invoice_number = $invoice_group->invoice_group_prefix;

        if ($invoice_group->invoice_group_prefix_year)
        {
            $invoice_number .= date('Y');
        }

        if ($invoice_group->invoice_group_prefix_month)
        {
            $invoice_number .= date('m');
        }

        if ($invoice_group->invoice_group_left_pad)
        {
            $invoice_id = str_pad($invoice_group->invoice_group_next_id, $invoice_group->invoice_group_left_pad, '0', STR_PAD_LEFT);
        }
        else
        {
            $invoice_id = $invoice_group->invoice_group_next_id;
        }

        $invoice_number .= $invoice_id;

        if ($set_next)
        {
            $this->set_next_invoice_number($invoice_group_id);
        }

        return $invoice_number;
    }

    public function set_next_invoice_number($invoice_group_id)
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('invoice_group_next_id', 'invoice_group_next_id+1', FALSE);
        $this->db->update($this->table);
    }

}

?>