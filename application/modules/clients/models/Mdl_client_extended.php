<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mdl_Client_Extended
 */
class Mdl_Client_Extended extends Response_Model
{
    public $table = 'ip_client_extended';
    public $primary_key = 'ip_client_extended.client_extended_id';

    public function insert_entry(
        $client_id,
        $salutation,
        $customer_no,
        $flags,
        $contact_person,
        $contract,
        $direct_debit,
        $bank_name,
        $bank_bic,
        $bank_iban,
        $payment_terms,
        $delivery_terms
    )
    {
        $data = array (
            'client_extended_client_id' => $client_id,
            'client_extended_salutation' => $salutation,
            'client_extended_customer_no' => $customer_no,
            'client_extended_flags' => $flags,
            'client_extended_contact_person' => $contact_person,
            'client_extended_contract' => $contract,
            'client_extended_direct_debit' => $direct_debit,
            'client_extended_bank_name' => $bank_name,
            'client_extended_bank_bic' => $bank_bic,
            'client_extended_bank_iban' => $bank_iban,
            'client_extended_payment_terms' => $payment_terms,
            'client_extended_delivery_terms' => $delivery_terms
        );
        $this->db->insert('ip_client_extended', $data);
    }

    public function get_by_clientid($client_id)
    {
        $this->db->where('client_extended_client_id', $client_id);
        $query = $this->db->get('ip_client_extended');
        $ret = $query->row();
        return ($ret);
    }

    public function update_entry(
        $client_id,
        $salutation,
        $customer_no,
        $flags,
        $contact_person,
        $contract,
        $direct_debit,
        $bank_name,
        $bank_bic,
        $bank_iban,
        $payment_terms,
        $delivery_terms
    )
    {
        $data = array (
            'client_extended_salutation' => $salutation,
            'client_extended_customer_no' => $customer_no,
            'client_extended_flags' => $flags,
            'client_extended_contact_person' => $contact_person,
            'client_extended_contract' => $contract,
            'client_extended_direct_debit' => $direct_debit,
            'client_extended_bank_name' => $bank_name,
            'client_extended_bank_bic' => $bank_bic,
            'client_extended_bank_iban' => $bank_iban,
            'client_extended_payment_terms' => $payment_terms,
            'client_extended_delivery_terms' => $delivery_terms
        );

        $this->db->update('ip_client_extended', $data, array('client_extended_client_id' => $client_id));
    }


    public function default_order_by()
    {
        $this->db->order_by('ip_client_extended.client_extended_client_id ASC');
    }

    public function validation_rules()
    {
        return array(
            'client_extended_client_id' => array(
                'field' => 'client_extended_client_id',
                'label' => trans('client'),
                'rules' => 'required'
            )
        );
    }

    /**
     * @param $input
     * @return string
     * Note by chrissie:
     * These validation rules now also apply here because the callback
     * now searches in this model instead of the other clients model
     */
    function fix_avs($input)
    {
        if ($input != '') {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            } else if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }
        return '';
    }

    function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        return $db_array;
    }

}
