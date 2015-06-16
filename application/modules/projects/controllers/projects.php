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

class Projects extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_projects');
    }

    public function index($page = 0)
    {
        $this->mdl_projects->paginate(site_url('projects/index'), $page);
        $projects = $this->mdl_projects->result();

        $this->layout->set('projects', $projects);
        $this->layout->buffer('content', 'projects/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('projects');
        }

        if ($this->mdl_projects->run_validation()) {
            $this->mdl_projects->save($id);
            redirect('projects');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_projects->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('clients/mdl_clients');

        $this->layout->set(
            array(
                'clients' => $this->mdl_clients->where('client_active', 1)->get()->result()
            )
        );

        $this->layout->buffer('content', 'projects/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_projects->delete($id);
        redirect('projects');
    }

}

?>