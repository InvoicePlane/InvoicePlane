<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/IntegrationPayloadSanitizer.php';

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
        $status = MerchantResponseStatus::fromExternal(
            $providerResponse['status_code'] ?? $providerResponse['status'] ?? null,
            MerchantResponseStatus::Sent
        );
        $reference = $providerResponse['external_id'] ?? null;
        if ($reference === null || $reference === '') {
            $reference = 'invoice-' . $invoiceId . '-' . date('YmdHis');
        }

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => $invoiceId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => IntegrationPayloadSanitizer::text($providerResponse['message'] ?? null),
            'merchant_response_reference'  => $reference,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::Out->value,
            'record_type'                  => MerchantResponseType::OutboundStatus->value,
            'status'                       => $status->value,
            'http_code'                    => $providerResponse['http_code'] ?? null,
            'error_code'                   => $errorCode,
            'error_detail'                 => IntegrationPayloadSanitizer::text($errorDetail, 500),
            'created_at'                   => date('Y-m-d H:i:s'),
            'raw_payload'                  => IntegrationPayloadSanitizer::json($providerResponse),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_inbound(
        int $merchantClientId,
        array $providerResponse,
        MerchantResponseDriver $driver,
    ): int {
        $status = MerchantResponseStatus::fromExternal(
            $providerResponse['status_code'] ?? $providerResponse['status'] ?? null,
            MerchantResponseStatus::Received
        );

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => IntegrationPayloadSanitizer::text($providerResponse['message'] ?? null),
            'merchant_response_reference'  => $providerResponse['external_id'] ?? null,
            'merchant_response_successful' => $status->isSuccessful(),
            'merchant_client_id'           => $merchantClientId,
            'direction'                    => MerchantResponseDirection::In->value,
            'record_type'                  => MerchantResponseType::IncomingInvoice->value,
            'status'                       => $status->value,
            'http_code'                    => $providerResponse['http_code'] ?? null,
            'created_at'                   => date('Y-m-d H:i:s'),
            'raw_payload'                  => IntegrationPayloadSanitizer::json($providerResponse),
        ]);

        return (int) $this->db->insert_id();
    }

    public function save_status(
        int $invoiceId,
        array $status,
        MerchantResponseDriver $driver,
        array $lastResponse = [],
    ): int {
        $resolvedStatus = MerchantResponseStatus::fromExternal($status['status_code'] ?? $status['status'] ?? null);

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => $invoiceId,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => IntegrationPayloadSanitizer::text($status['message'] ?? null),
            'merchant_response_reference'  => $status['external_id'] ?? $lastResponse['merchant_response_reference'] ?? null,
            'merchant_response_successful' => $resolvedStatus->isSuccessful(),
            'merchant_client_id'           => $lastResponse['merchant_client_id'] ?? null,
            'direction'                    => MerchantResponseDirection::Out->value,
            'record_type'                  => MerchantResponseType::OutboundStatus->value,
            'status'                       => $resolvedStatus->value,
            'http_code'                    => $status['http_code'] ?? null,
            'created_at'                   => date('Y-m-d H:i:s'),
            'raw_payload'                  => IntegrationPayloadSanitizer::json($status),
        ]);

        return (int) $this->db->insert_id();
    }

    public function create_inbound_item(
        int $merchantClientId,
        array $invoice,
        MerchantResponseDriver $driver,
        ?string $peppolParticipantId = null,
        ?PeppolDocumentType $peppolDocumentType = null,
        array $document = [],
    ): int {
        $status = MerchantResponseStatus::fromExternal(
            $invoice['status_code'] ?? $invoice['status'] ?? null,
            MerchantResponseStatus::Received
        );
        $externalId = $invoice['id'] ?? $invoice['external_id'] ?? null;

        $existing = [];
        if ($externalId !== null) {
            $existing = $this->db
                ->where('merchant_client_id', $merchantClientId)
                ->where('merchant_response_reference', $externalId)
                ->where('direction', MerchantResponseDirection::In->value)
                ->where('record_type', MerchantResponseType::IncomingInvoice->value)
                ->get(self::TABLE)
                ->row_array() ?: [];
        }

        $document = array_intersect_key($document, array_flip([
            'document_path',
            'document_name',
            'document_mime_type',
            'document_size',
            'document_sha256',
            'document_profile',
            'document_validation_status',
            'document_validation_error',
        ]));
        if (array_key_exists('document_validation_error', $document)) {
            $document['document_validation_error'] = IntegrationPayloadSanitizer::text(
                $document['document_validation_error']
            );
        }

        $data = array_merge([
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => IntegrationPayloadSanitizer::text($invoice['message'] ?? null),
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
            'raw_payload'                  => IntegrationPayloadSanitizer::json($invoice),
        ], $document);

        if ($existing !== []) {
            unset($data['created_at']);

            $this->db
                ->where('merchant_response_id', $existing['merchant_response_id'])
                ->update(self::TABLE, $data);

            return (int) $existing['merchant_response_id'];
        }

        $this->db->insert(self::TABLE, $data);

        return (int) $this->db->insert_id();
    }

    public function has_valid_incoming_document(int $merchantClientId, string $externalId): bool
    {
        return $this->db
            ->where('merchant_client_id', $merchantClientId)
            ->where('merchant_response_reference', $externalId)
            ->where('direction', MerchantResponseDirection::In->value)
            ->where('record_type', MerchantResponseType::IncomingInvoice->value)
            ->where('document_validation_status', 'valid')
            ->count_all_results(self::TABLE) > 0;
    }

    public function create_event_item(
        int $merchantClientId,
        array $event,
        MerchantResponseDriver $driver,
        ?string $peppolParticipantId = null,
        ?PeppolDocumentType $peppolDocumentType = null,
    ): int {
        $status     = MerchantResponseStatus::fromExternal($event['status_code'] ?? $event['status'] ?? null);
        $rawPayload = IntegrationPayloadSanitizer::json($event);
        $eventHash  = hash('sha256', $rawPayload);

        $existing = $this->db
            ->where('merchant_client_id', $merchantClientId)
            ->where('record_type', MerchantResponseType::InvoiceEvent->value)
            ->where('event_hash', $eventHash)
            ->count_all_results(self::TABLE);

        if ($existing > 0) {
            return 0;
        }

        $message = $event['message']
            ?? $event['reason_message']
            ?? $event['reason']
            ?? $event['event_type']
            ?? $event['status_text']
            ?? $event['status_code']
            ?? $status->value;

        $this->db->insert(self::TABLE, [
            'invoice_id'                   => null,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => $driver->value,
            'merchant_response'            => IntegrationPayloadSanitizer::text($message),
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
            'raw_payload'                  => $rawPayload,
            'event_hash'                   => $eventHash,
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

    public function get_incoming_by_id(int $responseId): array
    {
        return $this->db
            ->where('merchant_response_id', $responseId)
            ->where('record_type', MerchantResponseType::IncomingInvoice->value)
            ->where('direction', MerchantResponseDirection::In->value)
            ->get(self::TABLE)
            ->row_array() ?: [];
    }

    public function get_events(): array
    {
        return $this->db
            ->select(self::TABLE . '.*, ip_merchant_clients.label AS merchant_client_label')
            ->join('ip_merchant_clients', 'ip_merchant_clients.id = ' . self::TABLE . '.merchant_client_id', 'left')
            ->where('record_type', MerchantResponseType::InvoiceEvent->value)
            ->order_by('created_at', 'DESC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function get_last_response_by_invoice(int $invoiceId, ?int $merchantClientId = null): array
    {
        $query = $this->db
            ->where('invoice_id', $invoiceId)
            ->where('direction', MerchantResponseDirection::Out->value);

        if ($merchantClientId !== null) {
            $query->where('merchant_client_id', $merchantClientId);
        }

        return $query
            ->order_by('created_at', 'DESC')
            ->order_by('merchant_response_id', 'DESC')
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
