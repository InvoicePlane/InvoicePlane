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

class Mdl_Payment_Methods extends Response_Model
{
    public $table = 'ip_payment_methods';

    public $primary_key = 'ip_payment_methods.payment_method_id';

    public function __construct()
    {
        $this->load->helper('payment_methods/payment_method_types');
        $this->load->model('payment_methods/Mdl_qr_code');
        $this->load->model('payment_methods/Mdl_qr_code_swiss');
    }

    public function types(): array
    {
        return [
            0 => [
                'name' => 'custom',
                'label' => trans('payment_method_type_custom'),
            ],
            1 => [
                'name' => 'qr_code',
                'label' => trans('payment_method_type_qr_code'),
                'class' => $this->Mdl_qr_code,
            ],
            2 => [
                'name' => 'qr_code_swiss',
                'label' => trans('payment_method_type_qr_code_swiss'),
                'class' => $this->Mdl_qr_code_swiss,
            ],
        ];
    }

    public function default_select(): void
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function order_by(): void
    {
        $this->db->order_by('ip_payment_methods.payment_method_name');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'payment_method_name' => [
                'field' => 'payment_method_name',
                'label' => trans('payment_method'),
                'rules' => 'required',
            ],
            'payment_method_type_id' => [
                'field' => 'payment_method_type_id',
                'label' => trans('payment_method_type'),
                'rules' => 'required',
            ],
        ];
    }
}
