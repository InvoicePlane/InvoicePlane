<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pdp_model extends CI_Model
{
    public function get_settings(): array
    {
        $row = $this->db->order_by('id', 'DESC')->get('ip_pdp_settings')->row_array();
        return $row ?: array('provider' => 'superpdp', 'auth_type' => 'oauth2_client_credentials', 'enabled' => 0);
    }

    public function save_settings(array $data): bool
    {
        // Compatibilite migrations : on ignore les champs absents si l'utilisateur
        // n'a pas encore applique toutes les migrations SQL du module.
        foreach (array_keys($data) as $field) {
            if (!$this->db->field_exists($field, 'ip_pdp_settings')) {
                unset($data[$field]);
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $existing = $this->db->order_by('id', 'DESC')->get('ip_pdp_settings')->row_array();
        if ($existing) {
            return (bool) $this->db->where('id', $existing['id'])->update('ip_pdp_settings', $data);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        return (bool) $this->db->insert('ip_pdp_settings', $data);
    }

    public function create_transmission(int $invoiceId, string $provider, array $request = array()): int
    {
        $row = array(
            'invoice_id' => $invoiceId,
            'provider' => $provider,
            'status' => 'pending',
            'request_json' => json_encode($request),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($this->db->field_exists('file_path', 'ip_pdp_transmissions')) {
            $row['file_path'] = $request['file'] ?? null;
        }
        if ($this->db->field_exists('file_name', 'ip_pdp_transmissions')) {
            $row['file_name'] = $request['file_name'] ?? (isset($request['file']) ? basename($request['file']) : null);
        }
        if ($this->db->field_exists('file_sha256', 'ip_pdp_transmissions')) {
            $row['file_sha256'] = $request['sha256'] ?? null;
        }

        $this->db->insert('ip_pdp_transmissions', $row);
        return (int) $this->db->insert_id();
    }

    public function update_transmission(int $id, array $response): bool
    {
        $row = array(
            // Pour SuperPDP, external_id contient l'id distant numerique retourne par /v1.beta/invoices.
            'external_id' => $response['external_id'] ?? null,
            'status' => $response['status'] ?? 'error',
            'message' => $response['message'] ?? null,
            'http_code' => $response['http_code'] ?? null,
            'response_json' => json_encode($response),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $optional = array(
            'provider_external_id' => $response['provider_external_id'] ?? $response['external_id'] ?? null,
            'invoiceplane_external_id' => $response['invoiceplane_external_id'] ?? null,
            'status_code' => $response['status_code'] ?? null,
            'status_text' => $response['status_text'] ?? null,
            'direction' => $response['direction'] ?? null,
        );

        foreach ($optional as $field => $value) {
            if ($this->db->field_exists($field, 'ip_pdp_transmissions')) {
                $row[$field] = $value;
            }
        }

        return (bool) $this->db->where('id', $id)->update('ip_pdp_transmissions', $row);
    }

    public function transmissions(int $limit = 50): array
    {
        return $this->db->order_by('id', 'DESC')->limit($limit)->get('ip_pdp_transmissions')->result_array();
    }

    public function latest_for_invoice(int $invoiceId): ?array
    {
        $row = $this->db->where('invoice_id', $invoiceId)->order_by('id', 'DESC')->get('ip_pdp_transmissions')->row_array();
        return $row ?: null;
    }

    public function save_incoming(array $invoice): int
    {
        $this->db->insert('ip_pdp_incoming_invoices', array(
            'provider' => $invoice['provider'] ?? null,
            'external_id' => $invoice['external_id'] ?? $invoice['id'] ?? null,
            'supplier_name' => $invoice['supplier_name'] ?? null,
            'supplier_siren' => $invoice['supplier_siren'] ?? null,
            'invoice_number' => $invoice['invoice_number'] ?? null,
            'issue_date' => $invoice['issue_date'] ?? null,
            'amount_total' => $invoice['amount_total'] ?? null,
            'currency' => $invoice['currency'] ?? 'EUR',
            'status' => $invoice['status'] ?? 'received',
            'raw_json' => json_encode($invoice),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        return (int) $this->db->insert_id();
    }
}
