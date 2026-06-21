<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolInvoiceEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    public function send(string $documentPath, array $metadata): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol invoice endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_endpoint']);

        $payload = [
            'file' => new CURLFile($documentPath, 'application/pdf', basename($documentPath)),
        ];

        if (!empty($metadata)) {
            $payload['metadata'] = json_encode($metadata);
        }

        return $this->client->request(RequestMethod::POST, $url, $payload, multipart: true);
    }

    public function status(string $externalId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_status_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol invoice status endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_status_endpoint'], $externalId);
        $response = $this->client->request(RequestMethod::GET, $url);

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function incoming(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['incoming_invoices_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol incoming invoices endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['incoming_invoices_endpoint']);

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }

    public function events(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['invoice_events_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol invoice events endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['invoice_events_endpoint']);

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }
}
