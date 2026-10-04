<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'modules/supplier_invoices/libraries/SupplierInvoiceAccess.php';

#[AllowDynamicProperties]
class Supplier_invoices extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->supplier_invoice_access = new SupplierInvoiceAccess();
        $this->requireReadAccess();
        $this->load->model('supplier_invoices/Mdl_supplier_invoices');
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_items');
        $this->load->model('supplier_invoices/Mdl_suppliers');
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_payments');
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_attachments');
        $this->load->helper('file_security');
    }

    public function index($page = 0): void
    {
        $filters = [
            'q' => $this->getScalar('q'),
            'status' => $this->getScalar('status'),
            'date_from' => $this->getDateFilter('date_from'),
            'date_to' => $this->getDateFilter('date_to'),
            'archived' => in_array($this->getScalar('archived'), ['active', 'archived', 'all'], true)
                ? $this->getScalar('archived')
                : 'active',
        ];
        $page = max(0, (int) $page);
        $perPage = max(1, (int) get_setting('default_list_limit', 20));
        $result = $this->Mdl_supplier_invoices->search($filters, $page, $perPage);
        $query = http_build_query(array_filter($filters, static fn ($value): bool => $value !== ''));
        $this->load->library('pagination');
        $config = [
            'base_url' => site_url('supplier_invoices/index'),
            'total_rows' => $result['total'],
            'per_page' => $perPage,
            'suffix' => $query === '' ? '' : '?' . $query,
        ];
        if ($this->config->item('pagination_style')) {
            $config = array_merge($config, $this->config->item('pagination_style'));
        }
        $this->pagination->initialize($config);

        $this->layout->set([
            'supplier_invoices' => $result['rows'],
            'filters' => $filters,
            'statuses' => Mdl_Supplier_invoices::STATUSES,
            'pagination' => $this->pagination->create_links(),
        ]);
        $this->layout->buffer('content', 'supplier_invoices/index');
        $this->layout->render();
    }

    public function form($invoiceId = null): void
    {
        $this->requireWriteAccess();
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
            $errors = $this->validateInvoiceData($data, $invoiceId);

            if ($errors === []) {
                try {
                    $savedId = $this->Mdl_supplier_invoices->save_invoice($invoiceId, $data, $items);
                    $this->session->set_flashdata('alert_success', trans('supplier_invoice_saved'));
                    redirect('supplier_invoices/view/' . $savedId);
                } catch (Throwable $e) {
                    log_message('error', 'Supplier invoice save failed: ' . sanitize_for_logging($e->getMessage()));
                    $errors[] = trans('unable_to_save_supplier_invoice');
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
        $this->requireWriteAccess();
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
                $errors[] = trans('supplier_name_required');
            }

            if ($errors === []) {
                try {
                    $this->Mdl_suppliers->save_supplier($supplierId, $supplier);
                    $this->session->set_flashdata('alert_success', trans('supplier_saved'));
                    redirect('supplier_invoices/suppliers');
                } catch (Throwable $e) {
                    log_message('error', 'Supplier save failed: ' . sanitize_for_logging($e->getMessage()));
                    $errors[] = trans('unable_to_save_supplier');
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
            'attachments' => $this->Mdl_supplier_invoice_attachments->get_by_invoice_id((int) $invoiceId),
            'history' => $this->Mdl_supplier_invoices->get_status_history((int) $invoiceId),
            'statuses' => Mdl_Supplier_invoices::STATUSES,
        ]);
        $this->layout->buffer('content', 'supplier_invoices/view');
        $this->layout->render();
    }

    public function status($invoiceId): void
    {
        $this->requireWriteAccess();
        if ( ! $this->ensure_valid_post_request('supplier_invoices/view/' . (int) $invoiceId)) {
            return;
        }

        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        try {
            $this->Mdl_supplier_invoices->update_status(
                (int) $invoiceId,
                trim((string) $this->input->post('status')),
                trim((string) $this->input->post('comment')) ?: null
            );
            $this->session->set_flashdata('alert_success', trans('supplier_invoice_status_updated'));
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice status update failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('unable_to_update_supplier_invoice_status'));
        }

        redirect('supplier_invoices/view/' . (int) $invoiceId);
    }

    public function archive($invoiceId): void
    {
        if ( ! $this->supplier_invoice_access->canArchive((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
        if ( ! $this->ensure_valid_post_request('supplier_invoices/view/' . (int) $invoiceId)) {
            return;
        }

        try {
            if ( ! $this->Mdl_supplier_invoices->archive((int) $invoiceId, (int) $this->session->userdata('user_id'))) {
                throw new RuntimeException('Supplier invoice is already archived or does not exist.');
            }
            $this->session->set_flashdata('alert_success', trans('supplier_invoice_archived'));
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice archive failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('unable_to_archive_supplier_invoice'));
        }

        redirect('supplier_invoices/view/' . (int) $invoiceId);
    }

    public function restore($invoiceId): void
    {
        if ( ! $this->supplier_invoice_access->canArchive((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
        if ( ! $this->ensure_valid_post_request('supplier_invoices/view/' . (int) $invoiceId)) {
            return;
        }

        try {
            if ( ! $this->Mdl_supplier_invoices->restore((int) $invoiceId)) {
                throw new RuntimeException('Supplier invoice is already active or does not exist.');
            }
            $this->session->set_flashdata('alert_success', trans('supplier_invoice_restored'));
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice restore failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('unable_to_restore_supplier_invoice'));
        }

        redirect('supplier_invoices/view/' . (int) $invoiceId);
    }

    public function payment($invoiceId): void
    {
        $this->requirePaymentAccess();
        if ( ! $this->ensure_valid_post_request('supplier_invoices/view/' . (int) $invoiceId)) {
            return;
        }

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
            $this->session->set_flashdata('alert_success', trans('supplier_invoice_payment_recorded'));
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice payment failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', $e->getMessage());
        }

        redirect('supplier_invoices/view/' . $invoiceId);
    }

    public function upload_attachment($invoiceId): void
    {
        $this->requireAttachmentAccess();
        if ( ! $this->ensure_valid_post_request('supplier_invoices/view/' . (int) $invoiceId)) {
            return;
        }

        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $invoiceId = (int) $invoiceId;
        if ($this->Mdl_supplier_invoices->get_by_id($invoiceId) === []) {
            show_404();
        }

        try {
            $file = $_FILES['attachment'] ?? null;
            if ( ! is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                throw new InvalidArgumentException('A valid attachment is required.');
            }
            if ( ! is_string($file['tmp_name']) || ! is_uploaded_file($file['tmp_name'])) {
                throw new InvalidArgumentException('The uploaded attachment is invalid.');
            }
            if ((int) $file['size'] <= 0 || (int) $file['size'] > 15 * 1024 * 1024) {
                throw new InvalidArgumentException('Attachments must be smaller than 15 MB.');
            }

            $originalName = is_string($file['name'] ?? null) ? basename($file['name']) : '';
            if ( ! validate_safe_filename($originalName)['valid']) {
                throw new InvalidArgumentException('The attachment filename is invalid.');
            }
            $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'xml', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
            if ( ! in_array($extension, $allowedExtensions, true)) {
                throw new InvalidArgumentException('This attachment type is not supported.');
            }

            $mimeType = function_exists('finfo_open')
                ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name'])
                : 'application/octet-stream';
            $allowedMimeTypes = ['application/pdf', 'application/xml', 'text/xml', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if ( ! in_array($mimeType, $allowedMimeTypes, true)) {
                throw new InvalidArgumentException('The attachment content type is not supported.');
            }

            $directory = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'supplier-invoices' . DIRECTORY_SEPARATOR . $invoiceId;
            if ( ! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
                throw new RuntimeException('Unable to create the attachment directory.');
            }
            if ( ! validate_file_in_directory($directory, UPLOADS_ARCHIVE_FOLDER)) {
                throw new RuntimeException('The attachment directory is outside the archive.');
            }
            $hash = hash_file('sha256', $file['tmp_name']);
            $storedName = $hash . '.' . $extension;
            $storedPath = $directory . DIRECTORY_SEPARATOR . $storedName;
            if (is_file($storedPath)) {
                throw new InvalidArgumentException('This attachment is already registered.');
            }
            if ( ! move_uploaded_file($file['tmp_name'], $storedPath) || ! chmod($storedPath, 0640)) {
                throw new RuntimeException('Unable to store the attachment.');
            }

            $relativePath = 'supplier-invoices/' . $invoiceId . '/' . $storedName;
            $attachmentId = $this->Mdl_supplier_invoice_attachments->save_attachment($invoiceId, [
                'file_name' => sanitize_filename_for_header($originalName),
                'storage_path' => $relativePath,
                'mime_type' => $mimeType,
                'file_size' => (int) $file['size'],
                'sha256' => $hash,
            ]);
            if ($attachmentId <= 0) {
                @unlink($storedPath);
                throw new RuntimeException('Unable to register the attachment.');
            }
            $this->session->set_flashdata('alert_success', trans('attachment_uploaded'));
        } catch (Throwable $e) {
            log_message('error', 'Supplier invoice attachment upload failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', $e->getMessage());
        }

        redirect('supplier_invoices/view/' . $invoiceId);
    }

    public function download_document($invoiceId): void
    {
        $invoice = $this->Mdl_supplier_invoices->get_by_id((int) $invoiceId);
        if ($invoice === []) {
            show_404();
        }

        $this->streamAttachment($invoice['document_path'], $invoice['document_name'], $invoice['document_mime_type']);
    }

    public function download_attachment($attachmentId): void
    {
        $attachment = $this->Mdl_supplier_invoice_attachments->get_by_id((int) $attachmentId);
        if ($attachment === []) {
            show_404();
        }

        $this->streamAttachment($attachment['storage_path'], $attachment['file_name'], $attachment['mime_type']);
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
                'subtotal' => $this->arrayPostScalar('item_subtotal', $index),
                'tax_total' => $this->arrayPostScalar('tax_total', $index),
                'total' => $this->arrayPostScalar('item_total', $index),
            ];
        }

        return $items;
    }

    private function validateInvoiceData(array $data, ?int $invoiceId = null): array
    {
        $errors = [];
        if ($data['supplier_id'] <= 0 || $this->Mdl_suppliers->get_by_id($data['supplier_id']) === []) {
            $errors[] = trans('valid_supplier_required');
        }
        if ($data['supplier_invoice_number'] === '') {
            $errors[] = trans('invoice_number_required');
        }
        if ($errors === [] && $this->Mdl_supplier_invoices->has_duplicate_number(
            $data['supplier_id'],
            $data['supplier_invoice_number'],
            $invoiceId
        )) {
            $errors[] = trans('duplicate_supplier_invoice_number');
        }
        if ($data['supplier_invoice_date'] === '') {
            $errors[] = trans('invoice_date_required');
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

    private function getScalar(string $key): string
    {
        $value = $this->input->get($key);

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function getDateFilter(string $key): string
    {
        $value = $this->getScalar($key);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1 ? $value : '';
    }

    private function streamAttachment(?string $relativePath, ?string $filename, ?string $mimeType): void
    {
        if ( ! is_string($relativePath) || $relativePath === '') {
            show_404();
        }

        $path = UPLOADS_ARCHIVE_FOLDER . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if ( ! is_file($path) || ! validate_file_in_directory($path, UPLOADS_ARCHIVE_FOLDER)) {
            show_404();
        }

        $content = file_get_contents($path);
        if ($content === false) {
            show_404();
        }
        $safeMimeType = in_array($mimeType, ['application/pdf', 'application/xml', 'text/xml', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)
            ? $mimeType
            : 'application/octet-stream';
        $safeFilename = sanitize_filename_for_header($filename ?: basename($path));

        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_header('X-Content-Type-Options: nosniff')
            ->set_header('Content-Length: ' . strlen($content))
            ->set_content_type($safeMimeType)
            ->set_header('Content-Disposition: attachment; filename="' . $safeFilename . '"')
            ->set_output($content);
    }

    private function requireReadAccess(): void
    {
        if ( ! $this->supplier_invoice_access->canRead((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
    }

    private function requireWriteAccess(): void
    {
        if ( ! $this->supplier_invoice_access->canWrite((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
    }

    private function requirePaymentAccess(): void
    {
        if ( ! $this->supplier_invoice_access->canManagePayments((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
    }

    private function requireAttachmentAccess(): void
    {
        if ( ! $this->supplier_invoice_access->canManageAttachments((int) $this->session->userdata('user_type'))) {
            show_error(trans('access_denied'), 403);
        }
    }
}
