<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Mdl_Supplier_invoice_payments extends CI_Model
{
    public function get_by_invoice_id(int $invoiceId): array
    {
        return $this->db
            ->where('supplier_invoice_id', $invoiceId)
            ->order_by('payment_date', 'DESC')
            ->order_by('supplier_invoice_payment_id', 'DESC')
            ->get('ip_supplier_invoice_payments')
            ->result_array();
    }

    public function add_payment(int $invoiceId, array $data): int
    {
        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        $this->db->trans_start();
        $invoice = $this->db
            ->query('SELECT * FROM ip_supplier_invoices WHERE supplier_invoice_id = ? FOR UPDATE', [$invoiceId])
            ->row_array();
        if ($invoice === []) {
            $this->db->trans_complete();
            throw new RuntimeException('Supplier invoice not found.');
        }

        $total = $invoice['total'] === null ? null : (float) $invoice['total'];
        $paid = (float) $invoice['amount_paid'];
        if ($total !== null && $paid + $amount > $total + 0.000001) {
            $this->db->trans_complete();
            throw new InvalidArgumentException('Payment amount exceeds the outstanding balance.');
        }

        $newPaid = $paid + $amount;
        $this->db->insert('ip_supplier_invoice_payments', [
            'supplier_invoice_id' => $invoiceId,
            'payment_date' => $data['payment_date'],
            'amount' => $amount,
            'currency_code' => strtoupper((string) ($data['currency_code'] ?? $invoice['currency_code'] ?? 'EUR')),
            'payment_method' => trim((string) ($data['payment_method'] ?? '')) ?: null,
            'reference' => trim((string) ($data['reference'] ?? '')) ?: null,
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentId = (int) $this->db->insert_id();

        $this->db->where('supplier_invoice_id', $invoiceId)->update('ip_supplier_invoices', [
            'amount_paid' => $newPaid,
            'status' => $total !== null && $newPaid >= $total - 0.000001 ? 'paid' : $invoice['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($total !== null && $newPaid >= $total - 0.000001 && $invoice['status'] !== 'paid') {
            $this->db->insert('ip_supplier_invoice_status_history', [
                'supplier_invoice_id' => $invoiceId,
                'old_status' => $invoice['status'],
                'new_status' => 'paid',
                'comment' => 'Automatically marked as paid after payment.',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false || $paymentId <= 0) {
            throw new RuntimeException('Unable to record the supplier invoice payment.');
        }

        return $paymentId;
    }
}
