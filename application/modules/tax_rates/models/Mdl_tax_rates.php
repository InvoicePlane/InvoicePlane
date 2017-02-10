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
 * Class Mdl_Tax_Rates
 */
class Mdl_Tax_Rates extends Response_Model
{
    public $table = 'ip_tax_rates';
    public $primary_key = 'ip_tax_rates.tax_rate_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_tax_rates.tax_rate_percent');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'tax_rate_name' => array(
                'field' => 'tax_rate_name',
                'label' => trans('tax_rate_name'),
                'rules' => 'required'
            ),
            'tax_rate_percent' => array(
                'field' => 'tax_rate_percent',
                'label' => trans('tax_rate_percent'),
                'rules' => 'required'
            )
        );
    }

}
