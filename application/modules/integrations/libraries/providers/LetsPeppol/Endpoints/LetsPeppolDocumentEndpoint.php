<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolDocumentEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    public function get(string $documentId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['document_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol document endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['document_endpoint'], $documentId);

        return $this->client->request(RequestMethod::GET, $url);
    }

    public function list(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['documents_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol documents endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['documents_endpoint']);

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }
}
