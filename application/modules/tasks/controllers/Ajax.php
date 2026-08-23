<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * @param null|int $invoice_id
     */
    public function modal_task_lookups($invoice_id = null)
    {
        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $data = [
            'default_item_tax_rate' => $default_item_tax_rate !== '' ?: 0,
            'tasks'                 => [],
        ];

        if ( ! empty($invoice_id)) {
            $this->load->model('mdl_tasks');
            $data['tasks'] = $this->mdl_tasks->get_tasks_to_invoice($invoice_id);
        }

        $this->layout->load_view('tasks/modal_task_lookups', $data);
    }

    public function process_task_selections()
    {
        $this->load->model('mdl_tasks');

        $task_ids = $this->input->post('task_ids') ?? [];
        // CI3's where_in() throws on an empty array rather than matching nothing.
        $tasks = $task_ids === [] ? [] : $this->mdl_tasks->where_in('task_id', $task_ids)->get()->result();
        foreach ($tasks as $task) {
            $task->task_price = format_amount($task->task_price);
        }

        $this->json_encode_ajax($tasks);
    }
}
