<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . 'modules/supplier_invoices/libraries/SupplierInvoiceDocumentParser.php';
require_once APPPATH . 'modules/supplier_invoices/libraries/SupplierInvoiceTotalsCalculator.php';
require_once APPPATH . 'modules/supplier_invoices/libraries/FrenchSupplierInvoiceDataValidator.php';

#[AllowDynamicProperties]
class Mdl_Supplier_invoices extends CI_Model
{
    public const STATUSES = ['received', 'approved', 'paid', 'rejected'];

    public function search(array $filters, int $offset, int $perPage): array
    {
        $this->applySearchFilters($filters);
        $total = $this->db->count_all_results('ip_supplier_invoices');

        $this->applySearchFilters($filters);
        $invoices = $this->db
            ->select('ip_supplier_invoices.*, ip_suppliers.supplier_name')
            ->order_by('supplier_invoice_date', 'DESC')
            ->order_by('supplier_invoice_id', 'DESC')
            ->limit($perPage, $offset)
            ->get('ip_supplier_invoices')
            ->result_array();

        foreach ($invoices as &$invoice) {
            $invoice['balance'] = $this->balance($invoice);
        }

        return ['rows' => $invoices, 'total' => $total];
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');
        $this->load->model('supplier_invoices/Mdl_suppliers');
        $this->load->model('supplier_invoices/Mdl_supplier_invoice_items');
    }

    public function get_all(?string $status = null): array
    {
        $this->db
            ->select('ip_supplier_invoices.*, ip_suppliers.supplier_name')
            ->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_supplier_invoices.supplier_id', 'left')
            ->where('ip_supplier_invoices.archived_at IS NULL')
            ->order_by('supplier_invoice_date', 'DESC')
            ->order_by('supplier_invoice_id', 'DESC');

        if ($status !== null && in_array($status, self::STATUSES, true)) {
            $this->db->where('status', $status);
        }

        $invoices = $this->db->get('ip_supplier_invoices')->result_array();
        foreach ($invoices as &$invoice) {
            $invoice['balance'] = $this->balance($invoice);
        }

        return $invoices;
    }

    public function get_by_id(int $invoiceId): array
    {
        $invoice = $this->db
            ->select('ip_supplier_invoices.*, ip_suppliers.supplier_name')
            ->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_supplier_invoices.supplier_id', 'left')
            ->where('supplier_invoice_id', $invoiceId)
            ->get('ip_supplier_invoices')
            ->row_array() ?: [];

        if ($invoice !== []) {
            $invoice['balance'] = $this->balance($invoice);
        }

        return $invoice;
    }

    public function archive(int $invoiceId, ?int $userId = null): bool
    {
        $invoice = $this->get_by_id($invoiceId);
        if ($invoice === [] || $invoice['archived_at'] !== null) {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('supplier_invoice_id', $invoiceId)->update('ip_supplier_invoices', [
            'archived_at' => date('Y-m-d H:i:s'),
            'archived_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->record_status($invoiceId, $invoice['status'], $invoice['status'], 'Invoice archived.');
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function restore(int $invoiceId): bool
    {
        $invoice = $this->get_by_id($invoiceId);
        if ($invoice === [] || $invoice['archived_at'] === null) {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('supplier_invoice_id', $invoiceId)->update('ip_supplier_invoices', [
            'archived_at' => null,
            'archived_by' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->record_status($invoiceId, $invoice['status'], $invoice['status'], 'Invoice restored from archive.');
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function has_duplicate_number(int $supplierId, string $invoiceNumber, ?int $invoiceId = null): bool
    {
        $this->db
            ->where('supplier_id', $supplierId)
            ->where('supplier_invoice_number', trim($invoiceNumber));
        if ($invoiceId !== null) {
            $this->db->where('supplier_invoice_id !=', $invoiceId);
        }

        return $this->db->count_all_results('ip_supplier_invoices') > 0;
    }

    public function get_by_incoming_response_ids(): array
    {
        $rows = $this->db
            ->select('supplier_invoice_id, incoming_response_id, status')
            ->where('incoming_response_id IS NOT NULL')
            ->get('ip_supplier_invoices')
            ->result_array();
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[(int) $row['incoming_response_id']] = $row;
        }

        return $indexed;
    }

    public function import_from_incoming_response(int $responseId): int
    {
        $existing = $this->db
            ->where('incoming_response_id', $responseId)
            ->get('ip_supplier_invoices')
            ->row_array();
        if ($existing !== []) {
            return (int) $existing['supplier_invoice_id'];
        }

        $incoming = $this->db
            ->where('merchant_response_id', $responseId)
            ->where('direction', 'in')
            ->where('record_type', 'incoming_invoice')
            ->where('document_validation_status', 'valid')
            ->get('ip_merchant_responses')
            ->row_array();
        if ($incoming === []) {
            throw new RuntimeException('A validated incoming invoice document is required.');
        }

        $parsed = (new SupplierInvoiceDocumentParser())->parse($this->documentPath($incoming['document_path'] ?? null));
        $frenchValidationErrors = (new FrenchSupplierInvoiceDataValidator())->validate($parsed);
        if ($frenchValidationErrors !== []) {
            throw new RuntimeException(implode(' ', $frenchValidationErrors));
        }
        $supplierData = array_merge(
            $incoming,
            array_filter($parsed['supplier'], static fn ($value): bool => $value !== null && $value !== '')
        );
        $supplierId = $this->Mdl_suppliers->find_or_create_from_document($supplierData);
        $invoice = $parsed['invoice'];
        $reference = $this->scalar($incoming['merchant_response_reference'] ?? null);
        $number = $this->scalar($invoice['supplier_invoice_number'] ?? null) ?? $reference;
        if ($number === null) {
            throw new RuntimeException('The supplier invoice number is missing from the document.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();
        $this->db->insert('ip_supplier_invoices', [
            'supplier_id' => $supplierId,
            'incoming_response_id' => $responseId,
            'merchant_client_id' => $incoming['merchant_client_id'] ?? null,
            'external_reference' => $reference,
            'supplier_invoice_number' => $number,
            'supplier_invoice_date' => $invoice['supplier_invoice_date'] ?? null,
            'supplier_due_date' => $invoice['supplier_due_date'] ?? null,
            'currency_code' => $invoice['currency_code'] ?? 'EUR',
            'subtotal' => $invoice['subtotal'] ?? null,
            'tax_total' => $invoice['tax_total'] ?? null,
            'total' => $invoice['total'] ?? null,
            'status' => 'received',
            'document_path' => $incoming['document_path'] ?? null,
            'document_name' => $incoming['document_name'] ?? null,
            'document_mime_type' => $incoming['document_mime_type'] ?? null,
            'document_sha256' => $incoming['document_sha256'] ?? null,
            'raw_payload' => $incoming['raw_payload'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $invoiceId = (int) $this->db->insert_id();

        foreach ($parsed['items'] as $item) {
            $item['supplier_invoice_id'] = $invoiceId;
            $item['created_at'] = $now;
            $this->db->insert('ip_supplier_invoice_items', $item);
        }
        $this->record_status($invoiceId, null, 'received', 'Imported from PDP.');
        $this->db->trans_complete();

        if ($this->db->trans_status() === false || $invoiceId <= 0) {
            throw new RuntimeException('Unable to import the incoming supplier invoice.');
        }

        return $invoiceId;
    }

    public function save_invoice(?int $invoiceId, array $data, array $items = []): int
    {
        $supplierId = (int) ($data['supplier_id'] ?? 0);
        $invoiceNumber = trim((string) ($data['supplier_invoice_number'] ?? ''));
        if ($supplierId > 0 && $invoiceNumber !== '' && $this->has_duplicate_number($supplierId, $invoiceNumber, $invoiceId)) {
            throw new InvalidArgumentException('A supplier invoice with this number already exists.');
        }

        $calculated = (new SupplierInvoiceTotalsCalculator())->calculate($items);
        if ($calculated['items'] === []) {
            $calculated['subtotal'] = ($data['subtotal'] ?? '') === '' ? null : (float) $data['subtotal'];
            $calculated['tax_total'] = ($data['tax_total'] ?? '') === '' ? null : (float) $data['tax_total'];
            $calculated['total'] = ($data['total'] ?? '') === '' ? null : (float) $data['total'];
        }
        $values = [
            'supplier_id' => $supplierId ?: null,
            'external_reference' => trim((string) ($data['external_reference'] ?? '')) ?: null,
            'supplier_invoice_number' => trim((string) ($data['supplier_invoice_number'] ?? '')) ?: null,
            'supplier_invoice_date' => $data['supplier_invoice_date'] ?: null,
            'supplier_due_date' => $data['supplier_due_date'] ?: null,
            'currency_code' => strtoupper(trim((string) ($data['currency_code'] ?? 'EUR'))),
            'subtotal' => $calculated['subtotal'],
            'tax_total' => $calculated['tax_total'],
            'total' => $calculated['total'],
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->trans_start();
        if ($invoiceId === null) {
            $values['status'] = 'received';
            $values['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('ip_supplier_invoices', $values);
            $invoiceId = (int) $this->db->insert_id();
            $this->record_status($invoiceId, null, 'received', 'Invoice created manually.');
        } else {
            $this->db->where('supplier_invoice_id', $invoiceId)->update('ip_supplier_invoices', $values);
        }

        $this->load->model('supplier_invoices/Mdl_supplier_invoice_items');
        $this->Mdl_supplier_invoice_items->replace_for_invoice($invoiceId, $calculated['items']);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new RuntimeException('Unable to save the supplier invoice.');
        }

        return $invoiceId;
    }

    public function update_status(int $invoiceId, string $status, ?string $comment = null): bool
    {
        if ( ! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid supplier invoice status.');
        }

        $invoice = $this->get_by_id($invoiceId);
        if ($invoice === []) {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('supplier_invoice_id', $invoiceId)->update('ip_supplier_invoices', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->record_status($invoiceId, $invoice['status'], $status, $comment);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_status_history(int $invoiceId): array
    {
        return $this->db
            ->where('supplier_invoice_id', $invoiceId)
            ->order_by('created_at', 'DESC')
            ->get('ip_supplier_invoice_status_history')
            ->result_array();
    }

    private function record_status(int $invoiceId, ?string $oldStatus, string $newStatus, ?string $comment): void
    {
        $this->db->insert('ip_supplier_invoice_status_history', [
            'supplier_invoice_id' => $invoiceId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function documentPath(?string $relativePath): string
    {
        if ( ! is_string($relativePath) || $relativePath === '') {
            throw new RuntimeException('The incoming invoice has no archived document.');
        }

        $path = UPLOADS_ARCHIVE_FOLDER . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if ( ! is_file($path) || ! validate_file_in_directory($path, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The archived incoming invoice document is unavailable.');
        }

        return $path;
    }

    private function scalar(mixed $value): ?string
    {
        if ( ! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 255);
    }

    private function balance(array $invoice): ?float
    {
        if ($invoice['total'] === null) {
            return null;
        }

        return max(0, (float) $invoice['total'] - (float) ($invoice['amount_paid'] ?? 0));
    }

    private function applySearchFilters(array $filters): void
    {
        $term = trim((string) ($filters['q'] ?? ''));
        if ($term !== '') {
            $this->db->group_start()
                ->like('ip_suppliers.supplier_name', $term)
                ->or_like('ip_supplier_invoices.supplier_invoice_number', $term)
                ->or_like('ip_supplier_invoices.external_reference', $term)
                ->group_end();
        }

        if (isset($filters['status']) && in_array($filters['status'], self::STATUSES, true)) {
            $this->db->where('ip_supplier_invoices.status', $filters['status']);
        }
        if (! empty($filters['date_from'])) {
            $this->db->where('ip_supplier_invoices.supplier_invoice_date >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $this->db->where('ip_supplier_invoices.supplier_invoice_date <=', $filters['date_to']);
        }

        if (($filters['archived'] ?? 'active') === 'archived') {
            $this->db->where('ip_supplier_invoices.archived_at IS NOT NULL');
        } elseif (($filters['archived'] ?? 'active') !== 'all') {
            $this->db->where('ip_supplier_invoices.archived_at IS NULL');
        }

        $this->db->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_supplier_invoices.supplier_id', 'left');
    }
}
