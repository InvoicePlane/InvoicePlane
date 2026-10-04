<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Supplier_invoices extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('supplier_invoices/Mdl_supplier_invoices');
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_items');
        $this->load->model('supplier_invoices/Mdl_suppliers');
    }

    public function index(): void
    {
        $status = trim((string) $this->input->get('status')) ?: null;
        $this->layout->set([
            'supplier_invoices' => $this->Mdl_supplier_invoices->get_all($status),
            'selected_status' => $status,
            'statuses' => Mdl_Supplier_invoices::STATUSES,
        ]);
        $this->layout->buffer('content', 'supplier_invoices/index');
        $this->layout->render();
    }

    public function view($invoiceId): void
    {
        $invoice = $this->Mdl_supplier_invoices->get_by_id((int) $invoiceId);
        if ($invoice === []) {
            show_404();
        }

        $this->layout->set([
            'invoice' => $invoice,
            'items' => $this->Mdl_supplier_invoice_items->get_by_invoice_id((int) $invoiceId),
            'history' => $this->Mdl_supplier_invoices->get_status_history((int) $invoiceId),
            'statuses' => Mdl_Supplier_invoices::STATUSES,
        ]);
        $this->layout->buffer('content', 'supplier_invoices/view');
        $this->layout->render();
    }

    public function status($invoiceId): void
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        try {
            $this->Mdl_supplier_invoices->update_status(
                (int) $invoiceId,
                trim((string) $this->input->post('status')),
                trim((string) $this->input->post('comment')) ?: null
            );
            $this->session->set_flashdata('alert_success', 'Supplier invoice status updated.');
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice status update failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Unable to update the supplier invoice status.');
        }

        redirect('supplier_invoices/view/' . (int) $invoiceId);
    }
}
