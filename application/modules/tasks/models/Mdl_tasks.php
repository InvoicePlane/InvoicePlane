<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Mdl_Tasks
 */
class Mdl_Tasks extends Response_Model
{
    public $table = 'ip_tasks';
    public $primary_key = 'ip_tasks.task_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *,
          (CASE WHEN DATEDIFF(NOW(), task_finish_date) > 0 THEN 1 ELSE 0 END) is_overdue
        ', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_projects.project_name, ip_tasks.task_name');
    }

    public function default_join()
    {
        $this->db->join('ip_projects', 'ip_projects.project_id = ip_tasks.project_id', 'left');
    }

    public function get_latest()
    {
        $this->db->order_by('ip_tasks.task_id', 'DESC');
        return $this;
    }

    /**
     * @param string $match
     */
    public function by_task($match)
    {
        $this->db->like('task_name', $match);
        $this->db->or_like('task_description', $match);
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'task_name' => array(
                'field' => 'task_name',
                'label' => trans('task_name'),
                'rules' => 'required'
            ),
            'task_description' => array(
                'field' => 'task_description',
                'label' => trans('task_description'),
                'rules' => ''
            ),
            'task_price' => array(
                'field' => 'task_price',
                'label' => trans('task_price'),
                'rules' => 'required'
            ),
            'task_finish_date' => array(
                'field' => 'task_finish_date',
                'label' => trans('task_finish_date'),
                'rules' => 'required'
            ),
            'project_id' => array(
                'field' => 'project_id',
                'label' => trans('project'),
                'rules' => ''
            ),
            'task_status' => array(
                'field' => 'task_status',
                'label' => lang('status')
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'numeric'
            ),
        );
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['task_finish_date'] = date_to_mysql($db_array['task_finish_date']);
        $db_array['task_price'] = standardize_amount($db_array['task_price']);

        return $db_array;
    }

    /**
     * @param null|integer $id
     * @return bool
     */
    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('task_finish_date', date('Y-m-d'));
            parent::set_form_value('task_price', get_setting('default_hourly_rate'));
        }

        return true;
    }

    /**
     * @param integer $task_id
     * @return array
     */
    public function get_invoice_for_task($task_id)
    {
        if (!$task_id) {
            return null;
        }

        $invoice_item = $this->db->select('ip_invoice_items.invoice_id')
            ->from('ip_invoice_items')
            ->where('ip_invoice_items.item_task_id', $task_id)
            ->get()->result();

        if (empty($invoice_item) || !isset($invoice_item->invoice_id)) {
            return null;
        }

        $this->load->model('invoices/mdl_invoices');

        return $this->mdl_invoices->get_by_id($invoice_item->invoice_id);
    }

    /**
     * @param integer $invoice_id
     * @return array
     */
    public function get_tasks_to_invoice($invoice_id)
    {
        $result = array();

        if (!$invoice_id) {
            return $result;
        }

        // Get tasks without any project
        $query = $this->db->select($this->table . '.*')
            ->from($this->table)
            ->where($this->table . '.project_id', 0)
            ->where($this->table . '.task_status', 3)
            ->order_by($this->table . '.task_finish_date', 'ASC')
            ->order_by($this->table . '.task_name', 'ASC')
            ->get();

        foreach ($query->result() as $row) {
            $result[] = $row;
        }

        // Get tasks for this invoice
        $query = $this->db->select($this->table . '.*, ip_projects.project_name')
            ->from($this->table)
            ->join('ip_projects', 'ip_projects.project_id = ' . $this->table . '.project_id')
            ->join('ip_invoices', 'ip_invoices.client_id = ip_projects.client_id')
            ->where('ip_invoices.invoice_id', $invoice_id)
            ->where($this->table . '.task_status', 3)
            ->order_by($this->table . '.task_finish_date', 'ASC')
            ->order_by('ip_projects.project_name', 'ASC')
            ->order_by($this->table . '.task_name', 'ASC')
            ->get();

        foreach ($query->result() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param integer $invoice_id
     */
    public function update_on_invoice_delete($invoice_id)
    {
        if (!$invoice_id) {
            return;
        }
        $query = $this->db->select($this->table . '.*')
            ->from($this->table)
            ->join('ip_invoice_items', 'ip_invoice_items.item_task_id = ' . $this->table . '.task_id')
            ->where('ip_invoice_items.invoice_id', $invoice_id)
            ->get();

        foreach ($query->result() as $task) {
            $this->update_status(3, $task->task_id);
        }
    }

    /**
     * @param integer $new_status
     * @param integer $task_id
     */
    public function update_status($new_status, $task_id)
    {
        $statuses_ok = $this->statuses();
        if (isset($statuses_ok[$new_status])) {
            parent::save($task_id, array('task_status' => $new_status));
        }
    }

    /**
     * @return array
     */
    public function statuses()
    {
        return array(
            '1' => array(
                'label' => trans('not_started'),
                'class' => 'draft'
            ),
            '2' => array(
                'label' => trans('in_progress'),
                'class' => 'viewed'
            ),
            '3' => array(
                'label' => trans('complete'),
                'class' => 'sent'
            ),
            '4' => array(
                'label' => trans('invoiced'),
                'class' => 'paid'
            )
        );
    }

    /**
     * @param integer $project_id
     */
    public function update_on_project_delete($project_id)
    {
        if (!$project_id) {
            return;
        }

        $query = $this->db->select($this->table . '.*')
            ->from($this->table)
            ->where($this->table . '.project_id', $project_id)
            ->get();

        foreach ($query->result() as $task) {
            parent::save($task->task_id, array('task_project_id' => null));
        }
    }
}
