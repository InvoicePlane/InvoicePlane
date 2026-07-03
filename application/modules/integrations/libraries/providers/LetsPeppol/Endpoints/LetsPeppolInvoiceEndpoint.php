<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolInvoiceEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * POST {invoice_endpoint}  (multipart).
     *
     * Request:
     *   file      file    PDF invoice document (UBL or PEPPOL BIS)
     *   metadata  string  JSON-encoded metadata object (optional)
     *
     * Response (JSON):
     *   id      string  external invoice ID assigned by LetsPeppol
     *   status  string  processing|sent|error
     */
    public function send(string $documentPath, array $metadata): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol invoice endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_endpoint']);

        $payload = [
            'file' => new \CURLFile($documentPath, 'application/pdf', basename($documentPath)),
        ];

        if ( ! empty($metadata)) {
            $payload['metadata'] = json_encode($metadata);
        }

        return $this->client->request(RequestMethod::POST, $url, $payload, multipart: true);
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
        $response = $this->client->request(RequestMethod::GET, $url);

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

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
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

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }
}
