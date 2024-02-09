<?php

use types\AbstractType;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Mdl_Payment_Methods
 */
class Mdl_Payment_Methods extends Response_Model
{
    public $table = 'ip_payment_methods';
    public $primary_key = 'ip_payment_methods.payment_method_id';

    /**
     * @return array
     */
    public function types(): array
    {
        return array(
            '1' => array(
                'label' => trans('custom'),
            ),
            '2' => array(
                'label' => trans('qr_code'),
            ),
            '3' => array(
                'label' => trans('qr_code_swiss'),
                'class' => new \types\QrCodeSwiss(),
            )
        );
    }

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function order_by()
    {
        $this->db->order_by('ip_payment_methods.payment_method_name');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'payment_method_name' => array(
                'field' => 'payment_method_name',
                'label' => trans('payment_method'),
                'rules' => 'required'
            ),
            'payment_method_type_id' => array(
                'field' => 'payment_method_type_id',
                'label' => trans('payment_method_type'),
                'rules' => 'required'
            )
        );
    }

}
