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

class Tasks extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_tasks');

    }

    public function index($page = 0)
    {
        $this->mdl_tasks->paginate(site_url('tasks/index'), $page);
        $tasks = $this->mdl_tasks->result();

        $this->layout->set('tasks', $tasks);
        $this->layout->set('task_statuses', $this->mdl_tasks->statuses());
        $this->layout->buffer('content', 'tasks/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('tasks');
        }

        if ($this->mdl_tasks->run_validation()) {
            $this->mdl_tasks->save($id);
            redirect('tasks');
        }

        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_tasks->prep_form($id);
            if ($id and !$prep_form) {
                show_404();
            }
        }

        $this->load->model('projects/mdl_projects');

        $this->layout->set(
            array(
                'projects' => $this->mdl_projects->get()->result(),
                'task_statuses' => $this->mdl_tasks->statuses()
            )
        );
        $this->layout->buffer('content', 'tasks/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_tasks->delete($id);
        redirect('tasks');
    }

    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);
        return $this;
    }

}

?>