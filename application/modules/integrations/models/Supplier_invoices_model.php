<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/supplier_invoices/libraries/SupplierInvoiceDocumentParser.php';

final class Supplier_invoices_model extends CI_Model
{
    private const INVOICE_TABLE  = 'ip_supplier_invoices';
    private const SUPPLIER_TABLE = 'ip_suppliers';

    private const STATUSES = ['received', 'approved', 'paid', 'rejected'];

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');
    }

    public function import_from_incoming_response(int $responseId): int
    {
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

        $existing = $this->db
            ->where('incoming_response_id', $responseId)
            ->get(self::INVOICE_TABLE)
            ->row_array();

        if ($existing !== []) {
            return (int) $existing['supplier_invoice_id'];
        }

        $document = $this->documentPath($incoming['document_path'] ?? null);
        $parsed = (new SupplierInvoiceDocumentParser())->parse($document);
        $supplierId = $this->findOrCreateSupplier(array_merge($incoming, $parsed['supplier']));
        $now        = date('Y-m-d H:i:s');
        $reference  = $this->scalarValue($incoming['merchant_response_reference'] ?? null);
        $invoice    = $parsed['invoice'];
        $number     = $this->scalarValue($invoice['supplier_invoice_number'] ?? null) ?? $reference;
        if ($number === null) {
            throw new RuntimeException('The supplier invoice number is missing from the document.');
        }

        $this->db->trans_start();
        $this->db->insert(self::INVOICE_TABLE, [
            'supplier_id'             => $supplierId,
            'incoming_response_id'    => $responseId,
            'merchant_client_id'      => $incoming['merchant_client_id'] ?? null,
            'external_reference'      => $reference,
            'supplier_invoice_number' => $number,
            'supplier_invoice_date'   => $invoice['supplier_invoice_date'] ?? null,
            'supplier_due_date'       => $invoice['supplier_due_date'] ?? null,
            'currency_code'           => $invoice['currency_code'] ?? 'EUR',
            'subtotal'                => $invoice['subtotal'] ?? null,
            'tax_total'               => $invoice['tax_total'] ?? null,
            'total'                   => $invoice['total'] ?? null,
            'status'                  => 'received',
            'document_path'           => $incoming['document_path'] ?? null,
            'document_name'           => $incoming['document_name'] ?? null,
            'document_mime_type'      => $incoming['document_mime_type'] ?? null,
            'document_sha256'         => $incoming['document_sha256'] ?? null,
            'raw_payload'             => $incoming['raw_payload'] ?? null,
            'created_at'              => $now,
            'updated_at'              => $now,
        ]);

        $inserted = $this->db->affected_rows() === 1;
        if ( ! $inserted) {
            $this->db->trans_complete();
            throw new RuntimeException('Unable to create the supplier invoice record.');
        }

        $invoiceId = (int) $this->db->insert_id();
        foreach ($parsed['items'] as $item) {
            $item['supplier_invoice_id'] = $invoiceId;
            $item['created_at'] = $now;
            $this->db->insert('ip_supplier_invoice_items', $item);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            throw new RuntimeException('Unable to import the structured supplier invoice data.');
        }

        return $invoiceId;
    }

    public function get_by_id(int $supplierInvoiceId): array
    {
        return $this->db
            ->select(self::INVOICE_TABLE . '.*, ' . self::SUPPLIER_TABLE . '.supplier_name')
            ->join(self::SUPPLIER_TABLE, self::SUPPLIER_TABLE . '.supplier_id = ' . self::INVOICE_TABLE . '.supplier_id', 'left')
            ->where('supplier_invoice_id', $supplierInvoiceId)
            ->get(self::INVOICE_TABLE)
            ->row_array() ?: [];
    }

    public function get_all(): array
    {
        return $this->db
            ->select(self::INVOICE_TABLE . '.*, ' . self::SUPPLIER_TABLE . '.supplier_name')
            ->join(self::SUPPLIER_TABLE, self::SUPPLIER_TABLE . '.supplier_id = ' . self::INVOICE_TABLE . '.supplier_id', 'left')
            ->order_by(self::INVOICE_TABLE . '.supplier_invoice_date', 'DESC')
            ->order_by(self::INVOICE_TABLE . '.supplier_invoice_id', 'DESC')
            ->get(self::INVOICE_TABLE)
            ->result_array();
    }

    public function get_by_incoming_response_ids(): array
    {
        $rows = $this->db
            ->select('supplier_invoice_id, incoming_response_id, status')
            ->get(self::INVOICE_TABLE)
            ->result_array();
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[(int) $row['incoming_response_id']] = $row;
        }

        return $indexed;
    }

    public function update_status(int $supplierInvoiceId, string $status): bool
    {
        if ( ! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid supplier invoice status.');
        }

        return $this->db
            ->where('supplier_invoice_id', $supplierInvoiceId)
            ->update(self::INVOICE_TABLE, [
                'status'     => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    private function findOrCreateSupplier(array $incoming): int
    {
        $participantId = $this->scalarValue($incoming['peppol_participant_id'] ?? null);
        $vatId = $this->scalarValue($incoming['supplier_vat_id'] ?? null);
        $name = $this->scalarValue($incoming['supplier_name'] ?? null) ?? 'Unknown supplier';
        $existing = $participantId !== null
            ? $this->db->where('supplier_peppol_id', $participantId)->get(self::SUPPLIER_TABLE)->row_array()
            : ($vatId !== null
                ? $this->db->where('supplier_vat_id', $vatId)->get(self::SUPPLIER_TABLE)->row_array()
                : $this->db->where('supplier_name', $name)->get(self::SUPPLIER_TABLE)->row_array());
        if ($existing !== []) {
            return (int) $existing['supplier_id'];
        }

        $now = date('Y-m-d');
        $this->db->insert(self::SUPPLIER_TABLE, [
            'supplier_name'        => $name,
            'supplier_company'     => $this->scalarValue($incoming['supplier_company'] ?? null),
            'supplier_vat_id'      => $vatId,
            'supplier_peppol_id'   => $participantId,
            'supplier_email'       => $this->scalarValue($incoming['supplier_email'] ?? null),
            'supplier_address_1'   => $this->scalarValue($incoming['supplier_address_1'] ?? null),
            'supplier_city'        => $this->scalarValue($incoming['supplier_city'] ?? null),
            'supplier_zip'         => $this->scalarValue($incoming['supplier_zip'] ?? null),
            'supplier_country'     => $this->scalarValue($incoming['supplier_country'] ?? null),
            'supplier_active'      => 1,
            'supplier_date_created' => $now,
            'supplier_date_modified' => $now,
        ]);

        if ($this->db->affected_rows() !== 1) {
            throw new RuntimeException('Unable to create the supplier record.');
        }

        return (int) $this->db->insert_id();
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

    private function scalarValue(mixed $value): ?string
    {
        if ( ! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 255);
    }
}
