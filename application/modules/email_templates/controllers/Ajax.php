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

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function get_content()
    {
        $this->load->model('email_templates/mdl_email_templates');

        $id = $this->input->post('email_template_id');

        // get_by_id() returns null for an unknown id, but json_encode_ajax()
        // requires array|object — encode the miss directly rather than crash.
        $template = $this->mdl_email_templates->get_by_id($id);
        if ($template === null) {
            $this->output->set_content_type('application/json')->set_output('null');

            return;
        }

        $this->json_encode_ajax($template);
    }
}
