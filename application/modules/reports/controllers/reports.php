<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Reports extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('mdl_reports');
	}

	public function sales_by_client()
	{
		if ($this->input->post('btn_submit'))
		{
			$data = array(
				'results' => $this->mdl_reports->sales_by_client($this->input->post('from_date'), $this->input->post('to_date'))
			);

			$html = $this->load->view('reports/sales_by_client', $data, TRUE);

			$this->load->helper('mpdf');

			pdf_create($html, lang('sales_by_client'), TRUE);
		}

		$this->layout->buffer('content', 'reports/sales_by_client_index')->render();
	}
	
	public function payment_history()
	{
		if ($this->input->post('btn_submit'))
		{
			$data = array(
				'results' => $this->mdl_reports->payment_history($this->input->post('from_date'), $this->input->post('to_date'))
			);

			$html = $this->load->view('reports/payment_history', $data, TRUE);

			$this->load->helper('mpdf');

			pdf_create($html, lang('payment_history'), TRUE);
		}

		$this->layout->buffer('content', 'reports/payment_history_index')->render();
	}
	
	public function invoice_aging()
	{
		if ($this->input->post('btn_submit'))
		{
			$data = array(
				'results' => $this->mdl_reports->invoice_aging()
			);

			$html = $this->load->view('reports/invoice_aging', $data, TRUE);

			$this->load->helper('mpdf');

			pdf_create($html, lang('invoice_aging'), TRUE);
		}

		$this->layout->buffer('content', 'reports/invoice_aging_index')->render();
	}

}

?>