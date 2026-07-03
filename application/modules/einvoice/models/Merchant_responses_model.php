<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Merchant_responses_model extends CI_Model
{
    public function create_outbound(int $merchantClientId, int $invoiceId, array $providerResponse, array $requestData = []): int
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            'merchant_client_id' => $merchantClientId,
            'direction'          => 'out',
            'record_type'        => 'outbound_status',
            'invoice_id'         => $invoiceId,
            'external_id'        => $providerResponse['external_id'] ?? null,
            'status'             => $providerResponse['status'] ?? 'sent',
            'message'            => $providerResponse['message'] ?? null,
            'http_code'          => $providerResponse['http_code'] ?? null,
            'request_json'       => json_encode($requestData),
            'response_json'      => json_encode($providerResponse),
            'created_at'         => $now,
            'updated_at'         => $now,
        ];

        $this->db->insert('ip_einvoice_responses', $data);

        return (int) $this->db->insert_id();
    }

    public function create_inbound(int $merchantClientId, array $providerResponse): int
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            'merchant_client_id' => $merchantClientId,
            'direction'          => 'in',
            'record_type'        => 'incoming_invoice',
            'invoice_id'         => null,
            'external_id'        => $providerResponse['external_id'] ?? null,
            'status'             => $providerResponse['status'] ?? 'received',
            'message'            => $providerResponse['message'] ?? null,
            'http_code'          => $providerResponse['http_code'] ?? null,
            'response_json'      => json_encode($providerResponse),
            'created_at'         => $now,
            'updated_at'         => $now,
        ];

        $this->db->insert('ip_einvoice_responses', $data);

        return (int) $this->db->insert_id();
    }

    public function get_incoming()
    {
        return $this->db
            ->where('record_type', 'incoming_invoice')
            ->order_by('created_at', 'DESC')
            ->get('ip_einvoice_responses')
            ->result_array();
    }

    public function get_events()
    {
        return $this->db
            ->where('record_type', 'invoice_event')
            ->order_by('created_at', 'DESC')
            ->get('ip_einvoice_responses')
            ->result_array();
    }

    public function get_last_response_by_invoice($invoiceId)
    {
        return $this->db
            ->where('invoice_id', $invoiceId)
            ->where('direction', 'out')
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get('ip_einvoice_responses')
            ->row_array();
    }

    public function save_status(int $invoiceId, array $status, array $lastResponse = [])
    {
        $data = [
            'merchant_client_id' => $lastResponse['merchant_client_id'] ?? null,
            'direction'          => 'out',
            'record_type'        => 'outbound_status',
            'invoice_id'         => $invoiceId,
            'external_id'        => $status['external_id'] ?? $lastResponse['external_id'] ?? null,
            'status'             => $status['status'] ?? 'unknown',
            'message'            => $status['message'] ?? null,
            'http_code'          => $status['http_code'] ?? null,
            'request_json'       => null,
            'response_json'      => json_encode($status),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('ip_einvoice_responses', $data);

        return (int) $this->db->insert_id();
    }

    public function get_by_invoice($invoiceId)
    {
        return $this->db
            ->where('invoice_id', (int) $invoiceId)
            ->order_by('created_at', 'DESC')
            ->get('ip_einvoice_responses')
            ->result_array();
    }

    public function create_inbound_item($merchantClientId, array $invoice)
    {
        $data = [
            'merchant_client_id' => (int) $merchantClientId,
            'direction'          => 'in',
            'record_type'        => 'incoming_invoice',
            'invoice_id'         => null,
            'external_id'        => $invoice['id'] ?? $invoice['external_id'] ?? null,
            'status'             => $invoice['status'] ?? 'received',
            'message'            => $invoice['message'] ?? null,
            'http_code'          => $invoice['http_code'] ?? null,
            'request_json'       => null,
            'response_json'      => json_encode($invoice),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('ip_einvoice_responses', $data);

        return (int) $this->db->insert_id();
    }

    public function create_event_item($merchantClientId, array $event)
    {
        $data = [
            'merchant_client_id' => (int) $merchantClientId,
            'direction'          => 'in',
            'record_type'        => 'invoice_event',
            'invoice_id'         => null,
            'external_id'        => $event['invoice_id'] ?? $event['external_id'] ?? $event['id'] ?? null,
            'status'             => $event['status'] ?? $event['type'] ?? 'event',
            'message'            => $event['message'] ?? $event['event_type'] ?? null,
            'http_code'          => $event['http_code'] ?? null,
            'request_json'       => null,
            'response_json'      => json_encode($event),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('ip_einvoice_responses', $data);

        return (int) $this->db->insert_id();
    }
}
