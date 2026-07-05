<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Merchant_responses_model extends CI_Model
{
    private const TABLE = 'ip_merchant_responses';

    public function create_payment_response(
        int $invoiceId,
        MerchantResponseDriver $driver,
        string $message,
        string $reference,
        bool $successful,
    ): int {
        $status = $successful ? MerchantResponseStatus::Accepted : MerchantResponseStatus::Rejected;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => $invoiceId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $message,
            'merchant_response_reference'  => $reference,
            'merchant_response_successful' => (int) $successful,
            'direction'                    => MerchantResponseDirection::Out->value,
            'record_type'                  => MerchantResponseType::Payment->value,
            'status'                       => $status->value,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_outbound(
        int $merchantClientId,
        int $invoiceId,
        array $providerResponse,
        MerchantResponseDriver $driver,
        ?string $errorCode = null,
        ?string $errorDetail = null,
    ): int {
        $status = MerchantResponseStatus::tryFrom($providerResponse['status'] ?? '') ?? MerchantResponseStatus::Sent;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => $invoiceId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $providerResponse['message'] ?? null,
            'merchant_response_reference'  => $providerResponse['external_id'] ?? null,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::Out->value,
            'record_type'                  => MerchantResponseType::OutboundStatus->value,
            'status'                       => $status->value,
            'http_code'                    => $providerResponse['http_code'] ?? null,
            'error_code'                   => $errorCode,
            'error_detail'                 => $errorDetail,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_inbound(
        int $merchantClientId,
        array $providerResponse,
        MerchantResponseDriver $driver,
    ): int {
        $status = MerchantResponseStatus::tryFrom($providerResponse['status'] ?? '') ?? MerchantResponseStatus::Received;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $providerResponse['message'] ?? null,
            'merchant_response_reference'  => $providerResponse['external_id'] ?? null,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::In->value,
            'record_type'                  => MerchantResponseType::IncomingInvoice->value,
            'status'                       => $status->value,
            'http_code'                    => $providerResponse['http_code'] ?? null,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function save_status(
        int $invoiceId,
        array $status,
        MerchantResponseDriver $driver,
        array $lastResponse = [],
    ): int {
        $resolvedStatus = MerchantResponseStatus::tryFrom($status['status'] ?? '') ?? MerchantResponseStatus::Unknown;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => $invoiceId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $status['message'] ?? null,
            'merchant_response_reference'  => $status['external_id'] ?? $lastResponse['merchant_response_reference'] ?? null,
            'merchant_response_successful' => $resolvedStatus->isSuccessful(),
            'merchant_client_id'           => $lastResponse['merchant_client_id'] ?? null,
            'direction'                    => MerchantResponseDirection::Out->value,
            'record_type'                  => MerchantResponseType::OutboundStatus->value,
            'status'                       => $resolvedStatus->value,
            'http_code'                    => $status['http_code'] ?? null,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_inbound_item(
        int $merchantClientId,
        array $invoice,
        MerchantResponseDriver $driver,
        ?string $peppolParticipantId = null,
        ?PeppolDocumentType $peppolDocumentType = null,
    ): int {
        $status     = MerchantResponseStatus::tryFrom($invoice['status'] ?? '') ?? MerchantResponseStatus::Received;
        $externalId = $invoice['id'] ?? $invoice['external_id'] ?? null;

        // Deduplication: skip if this external ID already exists for this provider.
        if ($externalId !== null) {
            $existing = $this->db
                ->where('merchant_client_id', $merchantClientId)
                ->where('merchant_response_reference', $externalId)
                ->where('direction', MerchantResponseDirection::In->value)
                ->count_all_results(self::TABLE);

            if ($existing > 0) {
                return 0;
            }
        }

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $invoice['message'] ?? null,
            'merchant_response_reference'  => $externalId,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::In->value,
            'record_type'                  => MerchantResponseType::IncomingInvoice->value,
            'status'                       => $status->value,
            'http_code'                    => $invoice['http_code'] ?? null,
            'peppol_participant_id'        => $peppolParticipantId,
            'peppol_document_type'         => $peppolDocumentType?->value,
            'created_at'                   => date('Y-m-d H:i:s'),
            'raw_payload'                  => json_encode($invoice),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_event_item(
        int $merchantClientId,
        array $event,
        MerchantResponseDriver $driver,
        ?string $peppolParticipantId = null,
        ?PeppolDocumentType $peppolDocumentType = null,
    ): int {
        $status = MerchantResponseStatus::tryFrom($event['status'] ?? '') ?? MerchantResponseStatus::Unknown;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => $event['message'] ?? $event['event_type'] ?? null,
            'merchant_response_reference'  => $event['invoice_id'] ?? $event['external_id'] ?? $event['id'] ?? null,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::In->value,
            'record_type'                  => MerchantResponseType::InvoiceEvent->value,
            'status'                       => $status->value,
            'http_code'                    => $event['http_code'] ?? null,
            'peppol_participant_id'        => $peppolParticipantId,
            'peppol_document_type'         => $peppolDocumentType?->value,
            'created_at'                   => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function get_incoming(): array
    {
        return $this->db
            ->where('record_type', MerchantResponseType::IncomingInvoice->value)
            ->order_by('created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function get_events(): array
    {
        return $this->db
            ->where('record_type', MerchantResponseType::InvoiceEvent->value)
            ->order_by('created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function get_last_response_by_invoice(int $invoiceId): array
    {
        return $this->db
            ->where('invoice_id', $invoiceId)
            ->where('direction', MerchantResponseDirection::Out->value)
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get(self::TABLE)
            ->row_array() ?: [];
    }

    public function get_by_invoice(int $invoiceId): array
    {
        return $this->db
            ->where('invoice_id', $invoiceId)
            ->order_by('created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function get_outbound_by_invoice(int $invoiceId): array
    {
        return $this->db
            ->where('invoice_id', $invoiceId)
            ->where('direction', MerchantResponseDirection::Out->value)
            ->order_by('created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function get_by_client(int $clientId): array
    {
        return $this->db
            ->select(self::TABLE . '.*, ip_invoices.invoice_number, ip_invoices.invoice_date_created')
            ->join('ip_invoices', 'ip_invoices.invoice_id = ' . self::TABLE . '.invoice_id', 'left')
            ->where('ip_invoices.client_id', $clientId)
            ->where(self::TABLE . '.direction', MerchantResponseDirection::Out->value)
            ->order_by(self::TABLE . '.created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }
}
