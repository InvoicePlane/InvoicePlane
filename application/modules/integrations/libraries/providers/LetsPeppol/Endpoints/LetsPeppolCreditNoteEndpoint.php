<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolCreditNoteEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * POST {credit_note_endpoint}  (multipart).
     *
     * Request:
     *   file      file    PDF credit note document (UBL or PEPPOL BIS)
     *   metadata  string  JSON-encoded metadata object (optional)
     *
     * Response (JSON):
     *   id      string  external credit note ID assigned by LetsPeppol
     *   status  string  processing|sent|error
     */
    public function send(string $documentPath, array $metadata): array
    {
        $settings = $this->client->getSettings();

        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new \RuntimeException('Credit note document not found: ' . $documentPath);
        }

        if (empty($settings['credit_note_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol credit note endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['credit_note_endpoint']);

        $payload = [
            'file' => new \CURLFile($documentPath, 'application/pdf', basename($documentPath)),
        ];

        if ( ! empty($metadata)) {
            $payload['metadata'] = json_encode($metadata, JSON_THROW_ON_ERROR);
        }

        $response = ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::POST, $url, $payload, multipart: true),
            ['credit_note', 'data']
        );

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'LetsPeppol accepted the credit note but returned no external ID.';
        }

        return $response;
    }

    /**
     * GET {credit_note_status_endpoint}  →  /v1/credit-notes/{id}.
     *
     * Response (JSON):
     *   id      string  credit note external ID
     *   status  string  processing|sent|rejected|error
     */
    public function status(string $externalId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['credit_note_status_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol credit note status endpoint configuration.');
        }

        $url      = $this->client->buildUrl($settings['credit_note_status_endpoint'], $externalId);
        $response = ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::GET, $url),
            ['credit_note', 'data']
        );

        return array_merge($response, ['external_id' => $externalId]);
    }
}
