<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class Mdl_Supplier_invoice_attachments extends CI_Model
{
    public function get_by_invoice_id(int $invoiceId): array
    {
        return $this->db
            ->where('supplier_invoice_id', $invoiceId)
            ->order_by('created_at', 'DESC')
            ->get('ip_supplier_invoice_attachments')
            ->result_array();
    }

    public function get_by_id(int $attachmentId): array
    {
        return $this->db
            ->where('supplier_invoice_attachment_id', $attachmentId)
            ->get('ip_supplier_invoice_attachments')
            ->row_array() ?: [];
    }

    public function save_attachment(int $invoiceId, array $data): int
    {
        $this->db->insert('ip_supplier_invoice_attachments', [
            'supplier_invoice_id' => $invoiceId,
            'file_name' => $data['file_name'],
            'storage_path' => $data['storage_path'],
            'mime_type' => $data['mime_type'],
            'file_size' => (int) $data['file_size'],
            'sha256' => $data['sha256'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }
}
