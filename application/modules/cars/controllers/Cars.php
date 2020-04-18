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
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */
class Cars extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_cars');
    }
    public function index($page = 0)
    {
        $this->mdl_cars->paginate(site_url('cars/index'), $page);
        $cars = $this->mdl_cars->result();
        $this->layout->set(
            array(
                'cars' => $cars,
                'filter_display' => TRUE,
                'filter_placeholder' => lang('filter_cars'),
                'filter_method' => 'filter_cars'
            )
        );
        $this->layout->buffer('content', 'cars/index');
        $this->layout->render();
    }
	
	
    public function form($id = NULL)
    {
		
        if ($this->input->post('btn_cancel')) 
		{
            redirect('cars');
        }
        $this->load->model('custom_fields/mdl_car_custom');
		
        if ($this->mdl_cars->run_validation())
		{
            $id = $this->mdl_cars->save($id);
            $this->mdl_car_custom->save_custom($id, $this->input->post('custom'));
            redirect('cars');
        }
        if (!$this->input->post('btn_submit')) 
		{
            $prep_form = $this->mdl_cars->prep_form($id);
            if ($id and !$prep_form) {
                show_404();
            }
            $car_custom = $this->mdl_car_custom->where('car_id', $id)->get();
            if ($car_custom->num_rows()) {
                $car_custom = $car_custom->row();
                unset($car_custom->car_id, $car_custom->car_custom_id);
                foreach ($car_custom as $key => $val) {
                    $this->mdl_cars->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } 
		else 
		{
            if ($this->input->post('custom')) 
			{
                foreach ($this->input->post('custom') as $key => $val) 
				{
                    $this->mdl_cars->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }
		
		
        $this->load->model('clients/mdl_clients');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->layout->set(
            array(
                'car_id' => $id,
                'clients' => $this->mdl_clients->get()->result(),
                'custom_fields' => $this->mdl_custom_fields->by_table('ip_car_custom')->get()->result(),
            )
        );
        $this->layout->buffer('content', 'cars/form');
        $this->layout->render();
    }
    public function delete($id)
    {
        $this->mdl_cars->delete($id);
        redirect('cars');
    }
	public function remove($id=NULL)
	{
		if($id!=NULL)
		{
			$this->mdl_cars->delete($id);	
			redirect('cars');
		}
		else
		{
			redirect('cars');
		}
	}
}
