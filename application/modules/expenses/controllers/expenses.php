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

class Expenses extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_expenses');
    }

    public function index($page = 0)
    {
        $this->mdl_expenses->paginate(site_url('expenses/index'), $page);
        $payments = $this->mdl_expenses->result();

        $this->layout->set(
            array(
                'expenses' => $payments,
                'filter_display' => TRUE,
                'filter_placeholder' => lang('filter_expenses'),
                'filter_method' => 'filter_expenses'
            )
        );

        $this->layout->buffer('content', 'expenses/index');
        $this->layout->render();
    }

    public function form($id = NULL)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('expenses');
        }

        $this->load->model('custom_fields/mdl_expense_custom');

        if ($this->mdl_expenses->run_validation()) {
            $id = $this->mdl_expenses->save($id);

            $this->mdl_expense_custom->save_custom($id, $this->input->post('custom'));

            redirect('expenses');
        }

        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_expenses->prep_form($id);

            if ($id and !$prep_form) {
                show_404();
            }

            $expense_custom = $this->mdl_expense_custom->where('expense_id', $id)->get();

            if ($expense_custom->num_rows()) {
                $expense_custom = $expense_custom->row();

                unset($expense_custom->expense_id, $expense_custom->expense_custom_id);

                foreach ($expense_custom as $key => $val) {
                    $this->mdl_expenses->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } else {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_expenses->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('clients/mdl_clients');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_custom_fields');

        $this->layout->set(
            array(
                'expense_id' => $id,
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'clients' => $this->mdl_clients->get()->result(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'custom_fields' => $this->mdl_custom_fields->by_table('ip_expense_custom')->get()->result(),
            )
        );

        if ($id) {
            $this->layout->set('payment', $this->mdl_expenses->where('ip_expenses.expense_id', $id)->get()->row());
        }

        $this->layout->buffer('content', 'expenses/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_expenses->delete($id);
        redirect('expenses');
    }

}
