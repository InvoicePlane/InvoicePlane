<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */
class Mdl_Car_Custom extends MY_Model
{
    public $table = 'ip_car_custom';
    public $primary_key = 'ip_car_custom.car_custom_id';
    public function save_custom($car_id, $db_array)
    {
        $car_custom_id = NULL;
        $db_array['car_id'] = $car_id;
        $car_custom = $this->where('car_id', $car_id)->get();
        if ($car_custom->num_rows()) {
            $car_custom_id = $car_custom->row()->car_custom_id;
        }
        parent::save($car_custom_id, $db_array);
    }
}
