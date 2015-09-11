<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Ajax extends Admin_Controller
{
    public function modal_task_lookups($invoice_id = null)
    {
        $data['tasks'] = array();
        $this->load->model('mdl_tasks');

        if (!empty($invoice_id)) {
            $data['tasks'] = $this->mdl_tasks->get_tasks_to_invoice($invoice_id);
        }
        $this->layout->load_view('tasks/modal_task_lookups', $data);
    }

    public function process_task_selections()
    {
        $this->load->model('mdl_tasks');

        $tasks = $this->mdl_tasks->where_in('task_id', $this->input->post('task_ids'))->get()->result();
        foreach ($tasks as $task) {
            $task->task_price = format_amount($task->task_price);
        }

        echo json_encode($tasks);
    }
}
