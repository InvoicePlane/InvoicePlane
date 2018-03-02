<?php

/**
 * Class Test
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
 */
class Access extends Base_Controller
{
    public function index()
    {
        $this->load->model('mdl_test');

        // Test the model relation structure
        // @Dev: Model database structure needs to be setup adn filled with test data manually!
        $test = $this->mdl_test->get()->result();
        $test_row = $this->mdl_test->get()->row();
        $test_array = $this->mdl_test->where($this->mdl_test->primary_key, 1)
            ->get()->result_array();

        echo '<pre>';
        print_r($test);
        print_r($test_row);
        print_r($test_array);
        echo '</pre>';
    }
}
