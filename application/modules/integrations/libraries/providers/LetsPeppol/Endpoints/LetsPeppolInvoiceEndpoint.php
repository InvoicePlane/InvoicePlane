<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolInvoiceEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * POST {invoice_endpoint}  (multipart).
     *
     * Request:
     *   file      file    UBL or other supported e-invoice document
     *   metadata  string  JSON-encoded metadata object (optional)
     *
     * Response (JSON):
     *   id      string  external invoice ID assigned by LetsPeppol
     *   status  string  processing|sent|error
     */
    public function send(string $documentPath, array $metadata): array
    {
        $settings = $this->client->getSettings();

        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new \RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (empty($settings['invoice_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol invoice endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_endpoint']);

        $mimeType = $metadata['mime_type'] ?? 'application/octet-stream';
        $payload  = [
            'file' => new \CURLFile($documentPath, $mimeType, basename($documentPath)),
        ];

        if ( ! empty($metadata)) {
            $payload['metadata'] = json_encode($metadata, JSON_THROW_ON_ERROR);
        }

        $response = ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::POST, $url, $payload, multipart: true),
            ['invoice', 'data']
        );

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'LetsPeppol accepted the invoice but returned no external ID.';
        }

        return $response;
    }

    /**
     * GET {invoice_status_endpoint}  →  /v1/invoices/{id}.
     *
     * Response (JSON):
     *   id      string  invoice external ID
     *   status  string  processing|sent|rejected|error
     */
    public function status(string $externalId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_status_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol invoice status endpoint configuration.');
        }

        $url      = $this->client->buildUrl($settings['invoice_status_endpoint'], $externalId);
        $response = ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::GET, $url),
            ['invoice', 'data']
        );

        return array_merge($response, ['external_id' => $externalId]);
    }

    /**
     * GET {incoming_invoices_endpoint}?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of incoming invoice objects
     *   meta    object pagination metadata
     */
    public function incoming(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['incoming_invoices_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol incoming invoices endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['incoming_invoices_endpoint']);

        return ProviderResponseNormalizer::collection(
            $this->client->request(RequestMethod::GET, $url, query: $filters),
            ['invoices', 'items', 'data'],
            'invoices'
        );
    }

    /**
     * GET {invoice_events_endpoint}?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of invoice event objects (status changes, delivery attempts)
     */
    public function events(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_events_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol invoice events endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_events_endpoint']);

        return ProviderResponseNormalizer::collection(
            $this->client->request(RequestMethod::GET, $url, query: $filters),
            ['events', 'items', 'data'],
            'events'
        );
    }
}
