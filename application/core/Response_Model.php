<?php
if (!defined('BASEPATH')) {
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

/**
 * Class Response_Model
 */
class Response_Model extends Form_Validation_Model
{

    /**
     * @param null|int   $id
     * @param null|array $db_array
     *
     * @return null|int
     */
    public function save($id = null, $db_array = null)
    {
        if ($id) {
            $this->session->set_flashdata('alert_success', trans('record_successfully_updated'));
            parent::save($id, $db_array);
        } else {
            $this->session->set_flashdata('alert_success', trans('record_successfully_created'));
            $id = parent::save(null, $db_array);
        }

        return $id;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->session->set_flashdata('alert_success', trans('record_successfully_deleted'));
    }
  
    /**
     * Helper method for SQL_CALC_FOUND_ROWS
     *
     * @return Either 'SQL_CALC_FOUND_ROWS ' as string with trailing space for mysql or 
     *         an empty string when using any other database.
     */
    public function sqlCalcFoundRowsHelper()
    {
        // Check if database is already loaded
        if (!$this->db) {
            $this->load->database();
        }
        // For MySQLi driver, use SQL_CALC_FOUND_ROWS
        if ($this->db->dbdriver == 'mysqli') {
            return 'SQL_CALC_FOUND_ROWS ';
        }
        return '';
    }
}
