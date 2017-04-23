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
 * Class Mdl_Invoice_Tax_Rates
 */
class Mdl_invoice_sumex extends Response_Model
{
    public $table = 'ip_invoice_sumex';
    public $primary_key = 'ip_invoice_sumex.sumex_id';

    public function default_select()
    {
        $this->db->select('ip_invoice_sumex.*');
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($id = null, $db_array = null)
    {
        $id = $this->where('sumex_invoice', $id)->get()->row()->sumex_id;
        parent::save($id, $db_array);
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'sumex_invoice' => array(
                'field' => 'sumex_invoice',
                'label' => trans('invoice'),
                'rules' => 'required'
            ),
            'sumex_reason' => array(
                'field' => 'sumex_reason',
                'label' => trans('reason'),
                'rules' => 'required|greater_than_equal_to[0]|less_than_equal_to[5]'
            ),
            'sumex_diagnosis' => array(
                'field' => 'sumex_diagnosis',
                'label' => trans('diagnosis')
            ),
            'sumex_observations' => array(
                'field' => 'sumex_observations',
                'label' => trans('sumex_observations')
            ),
            'sumex_treatmentstart' => array(
                'field' => 'sumex_treatmentstart',
                'label' => trans('start'),
                'rules' => 'required'
            ),
            'sumex_treatmentend' => array(
                'field' => 'sumex_treatmentend',
                'label' => trans('end'),
                'rules' => 'required'
            ),
            'sumex_casedate' => array(
                'field' => 'sumex_casedate',
                'label' => trans('case_date'),
                'rules' => 'required'
            ),
            'sumex_casenumber' => array(
                'field' => 'sumex_casenumber',
                'label' => trans('case_number')
            )
        );
    }

}
