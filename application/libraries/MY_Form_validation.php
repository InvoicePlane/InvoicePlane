<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Form_validation extends CI_Form_validation
{
    public $CI;

    public function is_unique($str, $field)
    {
        if (substr_count($field, '.') == 3) {
            list($table, $field, $id_field, $id_val) = explode('.', $field);
            $query = $this->CI->db->limit(1)->where($field, $str)->where($id_field . ' != ', $id_val)->get($table);
        } else {
            list($table, $field) = explode('.', $field);
            $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
        }

        return $query->num_rows() === 0;
    }

    /**
     * Valid Email taken from CI 3
     *
     * @param   string
     * @return  bool
     */

    public function valid_email($str)
    {
        if (function_exists('idn_to_ascii') && $atpos = strpos($str, '@')) {
            $str = substr($str, 0, ++$atpos) . idn_to_ascii(substr($str, $atpos));
        }
        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }
}
