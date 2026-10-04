<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Mdl_Suppliers extends CI_Model
{
    public function get_all(): array
    {
        return $this->db
            ->order_by('supplier_name', 'ASC')
            ->get('ip_suppliers')
            ->result_array();
    }

    public function get_by_id(int $supplierId): array
    {
        return $this->db
            ->where('supplier_id', $supplierId)
            ->get('ip_suppliers')
            ->row_array() ?: [];
    }

    public function save_supplier(?int $supplierId, array $data): int
    {
        $now = date('Y-m-d');
        $values = [
            'supplier_name' => trim((string) ($data['supplier_name'] ?? '')),
            'supplier_company' => trim((string) ($data['supplier_company'] ?? '')) ?: null,
            'supplier_vat_id' => trim((string) ($data['supplier_vat_id'] ?? '')) ?: null,
            'supplier_tax_code' => trim((string) ($data['supplier_tax_code'] ?? '')) ?: null,
            'supplier_peppol_id' => trim((string) ($data['supplier_peppol_id'] ?? '')) ?: null,
            'supplier_email' => trim((string) ($data['supplier_email'] ?? '')) ?: null,
            'supplier_address_1' => trim((string) ($data['supplier_address_1'] ?? '')) ?: null,
            'supplier_address_2' => trim((string) ($data['supplier_address_2'] ?? '')) ?: null,
            'supplier_city' => trim((string) ($data['supplier_city'] ?? '')) ?: null,
            'supplier_state' => trim((string) ($data['supplier_state'] ?? '')) ?: null,
            'supplier_zip' => trim((string) ($data['supplier_zip'] ?? '')) ?: null,
            'supplier_country' => strtoupper(trim((string) ($data['supplier_country'] ?? ''))) ?: null,
            'supplier_active' => (int) ($data['supplier_active'] ?? 1) === 1 ? 1 : 0,
            'supplier_date_modified' => $now,
        ];

        if ($supplierId === null) {
            $values['supplier_date_created'] = $now;
            $this->db->insert('ip_suppliers', $values);

            return (int) $this->db->insert_id();
        }

        $this->db->where('supplier_id', $supplierId)->update('ip_suppliers', $values);

        return $supplierId;
    }
}
