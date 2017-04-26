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
 * Class Reports
 */
class Reports extends Admin_Controller
{
    /**
     * Reports constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_reports');
    }

    public function sales_by_client()
    {
        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->sales_by_client($this->input->post('from_date'), $this->input->post('to_date')),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
            );

            $html = $this->load->view('reports/sales_by_client', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('sales_by_client'), true);
        }

        $this->layout->buffer('content', 'reports/sales_by_client_index')->render();
    }

    public function payment_history()
    {
        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->payment_history($this->input->post('from_date'), $this->input->post('to_date')),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
            );

            $html = $this->load->view('reports/payment_history', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('payment_history'), true);
        }

        $this->layout->buffer('content', 'reports/payment_history_index')->render();
    }

    public function invoice_aging()
    {
        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->invoice_aging()
            );

            $html = $this->load->view('reports/invoice_aging', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('invoice_aging'), true);
        }

        $this->layout->buffer('content', 'reports/invoice_aging_index')->render();
    }

    public function sales_by_year()
    {

        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->sales_by_year($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post('minQuantity'), $this->input->post('maxQuantity'), $this->input->post('checkboxTax')),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
            );

            $html = $this->load->view('reports/sales_by_year', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('sales_by_date'), true);
        }

        $this->layout->buffer('content', 'reports/sales_by_year_index')->render();
    }

}
