<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Mdl_Supplier_invoices extends CI_Model
{
    public const STATUSES = ['received', 'approved', 'paid', 'rejected'];

    public function get_all(?string $status = null): array
    {
        $this->db
            ->select('ip_supplier_invoices.*, ip_suppliers.supplier_name')
            ->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_supplier_invoices.supplier_id', 'left')
            ->order_by('supplier_invoice_date', 'DESC')
            ->order_by('supplier_invoice_id', 'DESC');

        if ($status !== null && in_array($status, self::STATUSES, true)) {
            $this->db->where('status', $status);
        }

        return $this->db->get('ip_supplier_invoices')->result_array();
    }

    public function get_by_id(int $invoiceId): array
    {
        return $this->db
            ->select('ip_supplier_invoices.*, ip_suppliers.supplier_name')
            ->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_supplier_invoices.supplier_id', 'left')
            ->where('supplier_invoice_id', $invoiceId)
            ->get('ip_supplier_invoices')
            ->row_array() ?: [];
    }

    public function save_invoice(?int $invoiceId, array $data, array $items = []): int
    {
        $values = [
            'supplier_id' => (int) ($data['supplier_id'] ?? 0) ?: null,
            'external_reference' => trim((string) ($data['external_reference'] ?? '')) ?: null,
            'supplier_invoice_number' => trim((string) ($data['supplier_invoice_number'] ?? '')) ?: null,
            'supplier_invoice_date' => $data['supplier_invoice_date'] ?: null,
            'supplier_due_date' => $data['supplier_due_date'] ?: null,
            'currency_code' => strtoupper(trim((string) ($data['currency_code'] ?? 'EUR'))),
            'subtotal' => ($data['subtotal'] ?? '') === '' ? null : (float) $data['subtotal'],
            'tax_total' => ($data['tax_total'] ?? '') === '' ? null : (float) $data['tax_total'],
            'total' => ($data['total'] ?? '') === '' ? null : (float) $data['total'],
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
        $this->Mdl_supplier_invoice_items->replace_for_invoice($invoiceId, $items);
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
}
