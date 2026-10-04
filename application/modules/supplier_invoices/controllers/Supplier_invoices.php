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
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_payments');
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

    public function form($invoiceId = null): void
    {
        $invoiceId = $invoiceId === null ? null : (int) $invoiceId;
        $invoice = $invoiceId === null ? [] : $this->Mdl_supplier_invoices->get_by_id($invoiceId);
        if ($invoiceId !== null && $invoice === []) {
            show_404();
        }

        $items = $invoiceId === null ? [] : $this->Mdl_supplier_invoice_items->get_by_invoice_id($invoiceId);
        if ($this->input->method() === 'post') {
            if ($this->input->post('btn_cancel')) {
                redirect('supplier_invoices');
            }

            $data = $this->invoicePostData();
            $items = $this->itemsPostData();
            $errors = $this->validateInvoiceData($data);

            if ($errors === []) {
                try {
                    $savedId = $this->Mdl_supplier_invoices->save_invoice($invoiceId, $data, $items);
                    $this->session->set_flashdata('alert_success', 'Supplier invoice saved.');
                    redirect('supplier_invoices/view/' . $savedId);
                } catch (Throwable $e) {
                    log_message('error', 'Supplier invoice save failed: ' . sanitize_for_logging($e->getMessage()));
                    $errors[] = 'Unable to save the supplier invoice.';
                }
            }

            $invoice = array_merge($invoice, $data);
            $this->layout->set('form_errors', $errors);
        }

        $this->layout->set([
            'invoice' => $invoice,
            'items' => $items,
            'suppliers' => $this->Mdl_suppliers->get_all(),
        ]);
        $this->layout->buffer('content', 'supplier_invoices/form');
        $this->layout->render();
    }

    public function suppliers(): void
    {
        $this->layout->set('suppliers', $this->Mdl_suppliers->get_all());
        $this->layout->buffer('content', 'supplier_invoices/suppliers');
        $this->layout->render();
    }

    public function supplier_form($supplierId = null): void
    {
        $supplierId = $supplierId === null ? null : (int) $supplierId;
        $supplier = $supplierId === null ? [] : $this->Mdl_suppliers->get_by_id($supplierId);
        if ($supplierId !== null && $supplier === []) {
            show_404();
        }

        $errors = [];
        if ($this->input->method() === 'post') {
            if ($this->input->post('btn_cancel')) {
                redirect('supplier_invoices/suppliers');
            }

            $supplier = array_merge($supplier, $this->supplierPostData());
            if (trim((string) ($supplier['supplier_name'] ?? '')) === '') {
                $errors[] = 'Supplier name is required.';
            }

            if ($errors === []) {
                try {
                    $this->Mdl_suppliers->save_supplier($supplierId, $supplier);
                    $this->session->set_flashdata('alert_success', 'Supplier saved.');
                    redirect('supplier_invoices/suppliers');
                } catch (Throwable $e) {
                    log_message('error', 'Supplier save failed: ' . sanitize_for_logging($e->getMessage()));
                    $errors[] = 'Unable to save the supplier.';
                }
            }
        }

        $this->layout->set(['supplier' => $supplier, 'form_errors' => $errors]);
        $this->layout->buffer('content', 'supplier_invoices/supplier_form');
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
            'payments' => $this->Mdl_supplier_invoice_payments->get_by_invoice_id((int) $invoiceId),
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

    public function payment($invoiceId): void
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $invoiceId = (int) $invoiceId;
        if ($this->Mdl_supplier_invoices->get_by_id($invoiceId) === []) {
            show_404();
        }

        try {
            $amount = $this->postScalar('amount');
            if ($amount === '' || ! is_numeric(str_replace(',', '.', $amount))) {
                throw new InvalidArgumentException('A valid payment amount is required.');
            }

            $paymentDate = $this->postScalar('payment_date');
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $paymentDate) !== 1) {
                throw new InvalidArgumentException('A valid payment date is required.');
            }

            $this->Mdl_supplier_invoice_payments->add_payment($invoiceId, [
                'amount' => str_replace(',', '.', $amount),
                'payment_date' => $paymentDate,
                'currency_code' => $this->postScalar('currency_code'),
                'payment_method' => $this->postScalar('payment_method'),
                'reference' => $this->postScalar('reference'),
                'notes' => $this->postScalar('notes'),
            ]);
            $this->session->set_flashdata('alert_success', 'Supplier invoice payment recorded.');
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice payment failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', $e->getMessage());
        }

        redirect('supplier_invoices/view/' . $invoiceId);
    }

    private function invoicePostData(): array
    {
        return [
            'supplier_id' => (int) $this->input->post('supplier_id'),
            'supplier_invoice_number' => $this->postScalar('supplier_invoice_number'),
            'external_reference' => $this->postScalar('external_reference'),
            'supplier_invoice_date' => $this->postScalar('supplier_invoice_date'),
            'supplier_due_date' => $this->postScalar('supplier_due_date'),
            'currency_code' => $this->postScalar('currency_code') ?: 'EUR',
            'subtotal' => $this->postScalar('subtotal'),
            'tax_total' => $this->postScalar('tax_total'),
            'total' => $this->postScalar('total'),
            'notes' => $this->postScalar('notes'),
        ];
    }

    private function itemsPostData(): array
    {
        $names = $this->input->post('item_name');
        if ( ! is_array($names)) {
            return [];
        }

        $items = [];
        foreach ($names as $index => $name) {
            if ( ! is_scalar($name)) {
                continue;
            }

            $items[] = [
                'item_name' => trim((string) $name),
                'item_description' => $this->arrayPostScalar('item_description', $index),
                'quantity' => $this->arrayPostScalar('quantity', $index) ?: 1,
                'unit_price' => $this->arrayPostScalar('unit_price', $index),
                'tax_rate' => $this->arrayPostScalar('tax_rate', $index),
                'subtotal' => $this->arrayPostScalar('subtotal', $index),
                'tax_total' => $this->arrayPostScalar('tax_total', $index),
                'total' => $this->arrayPostScalar('item_total', $index),
            ];
        }

        return $items;
    }

    private function validateInvoiceData(array $data): array
    {
        $errors = [];
        if ($data['supplier_id'] <= 0 || $this->Mdl_suppliers->get_by_id($data['supplier_id']) === []) {
            $errors[] = 'A valid supplier is required.';
        }
        if ($data['supplier_invoice_number'] === '') {
            $errors[] = 'Invoice number is required.';
        }
        if ($data['supplier_invoice_date'] === '') {
            $errors[] = 'Invoice date is required.';
        }

        return $errors;
    }

    private function supplierPostData(): array
    {
        $fields = [
            'supplier_name', 'supplier_company', 'supplier_vat_id', 'supplier_tax_code',
            'supplier_peppol_id', 'supplier_email', 'supplier_address_1', 'supplier_address_2',
            'supplier_city', 'supplier_state', 'supplier_zip', 'supplier_country',
        ];
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->postScalar($field);
        }
        $data['supplier_active'] = (int) $this->input->post('supplier_active') === 1 ? 1 : 0;

        return $data;
    }

    private function postScalar(string $key): string
    {
        $value = $this->input->post($key);

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function arrayPostScalar(string $key, $index): string
    {
        $values = $this->input->post($key);
        $value = is_array($values) ? ($values[$index] ?? '') : '';

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
