<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolCreditNoteEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    public function send(string $documentPath, array $metadata): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['credit_note_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol credit note endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['credit_note_endpoint']);

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

        if (empty($settings['credit_note_status_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol credit note status endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['credit_note_status_endpoint'], $externalId);
        $response = $this->client->request(RequestMethod::GET, $url);

        return array_merge($response, ['external_id' => $externalId]);
    }
}
