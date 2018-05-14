<?php

/**
 * Class Test
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class Access extends Base_Controller
{
    /**
     * Access index with Bootstrap test HTML
     * Route: /test/access/
     */
    public function index()
    {
        $this->layout->render('test/index');
    }

    /**
     * Extending Test
     * Route: /test/access/extender
     */
    public function extender()
    {
        $this->layout->extend('footer', 'test/partials/footer');
        $this->layout->render('test/index');
    }

    /**
     * Join Test
     * Route: /test/access/join-test
     */
    public function joinTest()
    {
        $this->load->model('mdl_test');

        // Test the model relation structure
        // @Dev: Model database structure needs to be setup adn filled with test data manually!
        $test = $this->mdl_test->get()->result();
        $test_row = $this->mdl_test->get()->row();
        $test_array = $this->mdl_test->where($this->mdl_test->primary_key, 1)
            ->get()->resultArray();

        echo '<pre>';
        print_r($test);
        print_r($test_row);
        print_r($test_array);
        echo '</pre>';
    }
}
