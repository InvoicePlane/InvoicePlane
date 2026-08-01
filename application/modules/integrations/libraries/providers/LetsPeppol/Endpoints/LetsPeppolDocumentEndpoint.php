<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolDocumentEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * GET {document_endpoint}  →  /v1/documents/{id}.
     *
     * Response (JSON):
     *   id           string  document ID
     *   type         string  invoice|credit_note
     *   content      string  base64-encoded XML document body
     *   created_at   string  ISO 8601 timestamp
     */
    public function get(string $documentId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['document_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol document endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['document_endpoint'], $documentId);

        return ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::GET, $url),
            ['document', 'data']
        );
    }

    /**
     * GET {documents_endpoint}?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of document objects
     *   meta    object pagination metadata
     */
    public function list(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['documents_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol documents endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['documents_endpoint']);

        return ProviderResponseNormalizer::collection(
            $this->client->request(RequestMethod::GET, $url, query: $filters),
            ['documents', 'items', 'data'],
            'documents'
        );
    }
}
