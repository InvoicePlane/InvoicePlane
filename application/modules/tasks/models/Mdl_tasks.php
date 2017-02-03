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
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
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

    public function by_task($match)
    {
        $this->db->like('task_name', $match);
        $this->db->or_like('task_description', $match);
    }

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
                'label' => trans('status')
            )
        );
    }


    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['task_finish_date'] = date_to_mysql($db_array['task_finish_date']);
        $db_array['task_price'] = standardize_amount($db_array['task_price']);

        return $db_array;
    }

    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('task_finish_date', date('Y-m-d'));
        }

        return true;
    }

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

}

?>