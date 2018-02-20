<?php

/**
 * Class IP_Form_Validation
 *
 * @package      InvoicePlane
 * @author       InvoicePlane Developers & Contributors
 * @copyright    Copyright (c) 2012 - 2018, InvoicePlane (https://invoiceplane.com/)
 * @license      http://opensource.org/licenses/MIT	MIT License
 * @link         https://invoiceplane.com
 */
class IP_Form_Validation extends CI_Form_validation
{
    public $CI;

    /**
     * @param string $str
     * @param string $field
     * @return bool
     */
    public function is_unique($str, $field)
    {
        if (substr_count($field, '.') == 3) {
            list($table, $field, $id_field, $id_val) = explode('.', $field);
            $query = $this->CI->db->limit(1)->where($field, $str)->where($id_field . ' != ', $id_val)->get($table);
        } else {
            list($table, $field) = explode('.', $field);
            $query = $this->CI->db->limit(1)->get_where($table, [$field => $str]);
        }

        return $query->num_rows() === 0;
    }

    /**
     * @param string $module
     * @param string $group
     * @return bool
     */
    function run($module = '', $group = '')
    {
        (is_object($module)) AND $this->CI = &$module;
        return parent::run($group);
    }
}
