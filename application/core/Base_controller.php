<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * InvoicePlane
 *
 * @author    InvoicePlane Developers & Contributors
 * @copyright Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license   https://invoiceplane.com/license.txt
 * @link      https://invoiceplane.com
 */

/**
 * Class Base_Controller
 */
class Base_Controller extends MX_Controller
{

    /** @var bool */
    public $ajax_controller = false;

    /**
     * Base_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller && !$this->input->is_ajax_request()) {
            show_404();
        }

        // Globally disallow GET requests to delete methods
        $this->load->helper('url');
        if (strstr(current_url(), 'delete') && $this->input->method() !== 'post') {
            show_404();
        }

        $this->load->library('session');
    }
}
