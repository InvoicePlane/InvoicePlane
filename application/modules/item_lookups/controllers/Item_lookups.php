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

class Item_Lookups extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_item_lookups');
    }

    public function index($page = 0)
    {
        $this->mdl_item_lookups->paginate(site_url('item_lookups/index'), $page);
        $item_lookups = $this->mdl_item_lookups->result();

        $this->layout->set('item_lookups', $item_lookups);
        $this->layout->buffer('content', 'item_lookups/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('item_lookups');
        }

        if ($this->mdl_item_lookups->run_validation()) {
            $this->mdl_item_lookups->save($id);
            redirect('item_lookups');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_item_lookups->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->buffer('content', 'item_lookups/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_item_lookups->delete($id);
        redirect('item_lookups');
    }

}
