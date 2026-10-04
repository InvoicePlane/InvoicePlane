<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Mdl_Supplier_invoice_items extends CI_Model
{
    public function get_by_invoice_id(int $invoiceId): array
    {
        return $this->db
            ->where('supplier_invoice_id', $invoiceId)
            ->order_by('supplier_invoice_item_id', 'ASC')
            ->get('ip_supplier_invoice_items')
            ->result_array();
    }

    public function replace_for_invoice(int $invoiceId, array $items): void
    {
        $this->db->where('supplier_invoice_id', $invoiceId)->delete('ip_supplier_invoice_items');

        foreach ($items as $item) {
            $name = trim((string) ($item['item_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $this->db->insert('ip_supplier_invoice_items', [
                'supplier_invoice_id' => $invoiceId,
                'item_name' => $name,
                'item_description' => trim((string) ($item['item_description'] ?? '')) ?: null,
                'quantity' => (float) ($item['quantity'] ?? 1),
                'unit_price' => ($item['unit_price'] ?? '') === '' ? null : (float) $item['unit_price'],
                'tax_rate' => ($item['tax_rate'] ?? '') === '' ? null : (float) $item['tax_rate'],
                'subtotal' => ($item['subtotal'] ?? '') === '' ? null : (float) $item['subtotal'],
                'tax_total' => ($item['tax_total'] ?? '') === '' ? null : (float) $item['tax_total'],
                'total' => ($item['total'] ?? '') === '' ? null : (float) $item['total'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
