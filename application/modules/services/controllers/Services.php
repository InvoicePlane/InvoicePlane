<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2024 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Tax_Rates
 */
class Services extends Admin_Controller
{
    /**
     * Tax_Rates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_services');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_services->paginate(site_url('services/index'), $page);
        $services = $this->mdl_services->result();

        $this->layout->set('services', $services);
        $this->layout->buffer('content', 'services/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('services');
        }
        if ($this->mdl_services->run_validation()) {
            $db_array = $this->mdl_services->db_array();

            $this->mdl_services->save($id, $db_array);

            redirect('services');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_services->prep_form($id)) {
                show_404();
            }
	}

        $this->layout->buffer('content', 'services/form');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form_client($client_id, $id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('services');
        }
        $this->layout->set(
            array(
                'client_id' => $client_id
            )
        );

        if ($this->input->post('client_id') && $id) {
            $this->db->query ('INSERT INTO ip_client_services VALUES (' . $this->input->post('client_id').', '. $id .')');

	}

        if ($this->mdl_services->run_validation()) {
            $db_array = $this->mdl_services->db_array();

            $this->mdl_services->save($id, $db_array);

            if ($this->input->post('client_id')) {
                $this->db->query ('INSERT INTO ip_client_services VALUES (' . $this->input->post('client_id') .', '. $this->db->insert_id() .')');
		redirect('clients/form/'. $this->input->post('client_id'));
	    }
            else
                redirect('services');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_services->prep_form($id)) {
                show_404();
            }
	}

	$this->layout->buffer('content', 'services/form/');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_services->delete($id);
        redirect('services');
    }

}
