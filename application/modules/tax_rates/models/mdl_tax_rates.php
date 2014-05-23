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

class Mdl_Tax_Rates extends Response_Model {

	public $table = 'fi_tax_rates';
	public $primary_key = 'fi_tax_rates.tax_rate_id';
    
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }
	
	public function default_order_by()
	{
		$this->db->order_by('fi_tax_rates.tax_rate_percent');
	}

	public function validation_rules()
	{
		return array(
			'tax_rate_name' => array(
				'field' => 'tax_rate_name',
				'label' => lang('tax_rate_name'),
				'rules' => 'required'
			),
			'tax_rate_percent' => array(
				'field' => 'tax_rate_percent',
				'label' => lang('tax_rate_percent'),
				'rules' => 'required'
			)
		);
	}

}

?>