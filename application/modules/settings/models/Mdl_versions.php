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
 * Class Mdl_Versions
 */
class Mdl_Versions extends Response_Model
{

    public $table = 'ip_versions';

    public $primary_key = 'ip_versions.version_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_versions.version_date_applied DESC, ip_versions.version_file DESC');
    }

    /**
     * Returns the latest version from the database
     *
     * @return string
     */
    public function get_current_version()
    {
        $current_version = $this->mdl_versions->limit(1)->get()->row()->version_file;
        return str_replace('.sql', '', substr($current_version, strpos($current_version, '_') + 1));
    }
}
