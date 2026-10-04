<?php

defined('BASEPATH') || exit('No direct script access allowed');

final class Supplier_invoices_model extends CI_Model
{
    private const INVOICE_TABLE  = 'ip_supplier_invoices';
    private const SUPPLIER_TABLE = 'ip_suppliers';

    private const STATUSES = ['received', 'approved', 'paid', 'rejected'];

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

        $supplierId = $this->findOrCreateSupplier($incoming);
        $now        = date('Y-m-d H:i:s');
        $reference  = $this->scalarValue($incoming['merchant_response_reference'] ?? null);

        $this->db->insert(self::INVOICE_TABLE, [
            'supplier_id'             => $supplierId,
            'incoming_response_id'    => $responseId,
            'merchant_client_id'      => $incoming['merchant_client_id'] ?? null,
            'external_reference'      => $reference,
            'supplier_invoice_number' => $reference,
            'supplier_invoice_date'   => $incoming['merchant_response_date'] ?? null,
            'currency_code'           => 'EUR',
            'status'                  => 'received',
            'document_path'           => $incoming['document_path'] ?? null,
            'document_name'           => $incoming['document_name'] ?? null,
            'document_mime_type'      => $incoming['document_mime_type'] ?? null,
            'document_sha256'         => $incoming['document_sha256'] ?? null,
            'raw_payload'             => $incoming['raw_payload'] ?? null,
            'created_at'              => $now,
            'updated_at'              => $now,
        ]);

        if ($this->db->affected_rows() !== 1) {
            throw new RuntimeException('Unable to create the supplier invoice record.');
        }

        return (int) $this->db->insert_id();
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
        $query         = $this->db->where('supplier_peppol_id', $participantId);

        if ($participantId === null) {
            $query = $this->db->where('supplier_name', 'Unknown supplier');
        }

        $existing = $query->get(self::SUPPLIER_TABLE)->row_array();
        if ($existing !== []) {
            return (int) $existing['supplier_id'];
        }

        $now = date('Y-m-d');
        $this->db->insert(self::SUPPLIER_TABLE, [
            'supplier_name'        => $participantId ?? 'Unknown supplier',
            'supplier_peppol_id'   => $participantId,
            'supplier_active'      => 1,
            'supplier_date_created' => $now,
            'supplier_date_modified' => $now,
        ]);

        if ($this->db->affected_rows() !== 1) {
            throw new RuntimeException('Unable to create the supplier record.');
        }

        return (int) $this->db->insert_id();
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
