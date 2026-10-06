<?php

defined('BASEPATH') || exit('No direct script access allowed');

class SuperPdpClient extends AbstractRestProvider
{
    protected bool $mergeRequestDebug = true;

    protected static function definition(): array
    {
        return [
            'code'     => 'superpdp',
            'name'     => 'SuperPDP',
            'auth'     => 'oauth2',
            'settings' => [
                'client_id'                  => ['type' => 'text', 'required' => true],
                'client_secret'              => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'token_url'                  => ['default' => 'https://api.superpdp.tech/oauth2/token', 'type' => 'url', 'required' => true],
                'api_base_url'               => ['default' => 'https://api.superpdp.tech', 'type' => 'url', 'required' => true],
                'invoice_endpoint'           => ['default' => '/v1.beta/invoices', 'type' => 'path', 'required' => true],
                'invoice_status_endpoint'    => ['default' => '/v1.beta/invoices/{id}', 'type' => 'path', 'required' => true],
                'incoming_invoices_endpoint' => ['default' => '/v1.beta/invoices', 'type' => 'path', 'required' => true],
                'incoming_document_endpoint' => ['default' => '/v1.beta/invoices/{id}/document', 'type' => 'path', 'required' => true],
                'invoice_events_endpoint'    => ['default' => '/v1.beta/invoice_events', 'type' => 'path', 'required' => true],
                'disable_pre_check'          => ['default' => false, 'type' => 'checkbox'],
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = $settings;

        if (
            empty($settings['client_id'])
            || empty($settings['client_secret'])
            || empty($settings['token_url'])
        ) {
            throw new \RuntimeException('Missing SuperPDP OAuth2 settings.');
        }

        $decoded = $this->oauthFetchToken(
            $settings['token_url'],
            $settings['client_id'],
            $settings['client_secret']
        );

        if (empty($decoded['access_token'])) {
            throw new \RuntimeException('SuperPDP OAuth failed: no access_token in response.');
        }

        $this->accessToken = $decoded['access_token'];

        return true;
    }

    public function fetchToken(array $settings): string
    {
        $decoded = $this->oauthFetchToken(
            $settings['token_url'] ?? '',
            $settings['client_id'] ?? '',
            $settings['client_secret'] ?? ''
        );

        return $decoded['access_token'] ?? '';
    }

    /**
     * POST /v1.beta/invoices with the raw PDF as the request body.
     *
     * Optional query parameters:
     *   external_id        caller-provided invoice reference
     *   disable_pre_check  skip provider pre-validation
     */
    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new \RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (empty($this->settings['api_base_url'])) {
            throw new \RuntimeException('Missing SuperPDP API base URL.');
        }

        if (empty($this->accessToken)) {
            throw new \RuntimeException('Missing SuperPDP access token.');
        }

        if (empty($this->settings['invoice_endpoint'])) {
            throw new \RuntimeException('Missing invoice endpoint configuration.');
        }

        $document = file_get_contents($documentPath);
        if ($document === false) {
            throw new \RuntimeException('Unable to read invoice document: ' . $documentPath);
        }

        $query      = [];
        $externalId = $metadata['external_id'] ?? $metadata['invoice_id'] ?? null;

        if (is_scalar($externalId) && (string) $externalId !== '') {
            $query['external_id'] = (string) $externalId;
        }

        if ( ! empty($this->settings['disable_pre_check'])) {
            $query['disable_pre_check'] = true;
        }

        $response = $this->http->request(RequestMethod::POST, $this->buildUrl($this->settings['invoice_endpoint']), [
            'bearer'  => $this->accessToken,
            'body'    => $document,
            'headers' => [
                'Content-Type: application/pdf',
            ],
            'query' => $query,
        ]);

        $response = ProviderResponseNormalizer::entity($response, ['invoice', 'data']);

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'SuperPDP accepted the invoice but returned no external ID.';
        }

        return $response;
    }

    /**
     * GET /v1.beta/invoices/{id}.
     *
     * Response (JSON):
     *   id      string  invoice external ID
     *   status  string  processing|sent|error
     */
    public function getInvoiceStatus(string $externalId): array
    {
        if (empty($this->settings['invoice_status_endpoint'])) {
            throw new \RuntimeException('Missing invoice status endpoint configuration.');
        }

        $endpoint = str_replace('{id}', urlencode($externalId), $this->settings['invoice_status_endpoint']);
        $url      = $this->buildUrl($endpoint);

        $response = ProviderResponseNormalizer::entity(
            $this->request(RequestMethod::GET, $url, [], false, ['external_id' => $externalId]),
            ['invoice', 'data']
        );

        return array_merge($response, ['external_id' => $externalId]);
    }

    /**
     * GET /v1.beta/invoices?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of invoice objects
     */
    public function receiveInvoices(array $filters = []): array
    {
        if (empty($this->settings['incoming_invoices_endpoint'])) {
            throw new \RuntimeException('Missing incoming invoices endpoint configuration.');
        }

        $url = $this->buildUrl($this->settings['incoming_invoices_endpoint'], $filters);

        return ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $url),
            ['invoices', 'items', 'data'],
            'invoices'
        );
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $inline = $this->decodeInlineDocument($invoice);
        if ($inline !== null) {
            return $inline;
        }

        if (empty($this->settings['incoming_document_endpoint'])) {
            throw new \RuntimeException('Missing incoming document endpoint configuration.');
        }

        $externalId = $invoice['document_id'] ?? $invoice['external_id'] ?? $invoice['id'] ?? null;
        if ( ! is_string($externalId) || $externalId === '') {
            throw new \RuntimeException('SuperPDP incoming invoice has no document ID.');
        }

        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->settings['incoming_document_endpoint']);
        $download = $this->http->request(RequestMethod::GET, $this->buildUrl($endpoint), [
            'bearer'             => $this->accessToken,
            'binary'             => true,
            'max_response_bytes' => 15 * 1024 * 1024,
        ]);

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? $invoice['file_name'] ?? 'superpdp-invoice.pdf',
            'mime_type' => $download['content_type'] ?? $invoice['mime_type'] ?? null,
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['document_id' => $externalId],
        ];
    }

    /**
     * GET /v1.beta/invoice_events?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of invoice event objects
     */
    public function getInvoiceEvents(array $filters = []): array
    {
        if (empty($this->settings['invoice_events_endpoint'])) {
            throw new \RuntimeException('Missing invoice events endpoint configuration.');
        }

        $url = $this->buildUrl($this->settings['invoice_events_endpoint'], $filters);

        return ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $url),
            ['events', 'items', 'data'],
            'events'
        );
    }

    private function decodeInlineDocument(array $invoice): ?array
    {
        $encoded = $invoice['content_base64'] ?? $invoice['document_content'] ?? $invoice['content'] ?? null;
        if ( ! is_string($encoded) || $encoded === '') {
            return null;
        }

        $content = base64_decode($encoded, true);
        if ($content === false) {
            throw new \RuntimeException('SuperPDP incoming document is not valid base64.');
        }

        return [
            'success'   => true,
            'content'   => $content,
            'filename'  => $invoice['filename'] ?? $invoice['file_name'] ?? 'superpdp-invoice.xml',
            'mime_type' => $invoice['mime_type'] ?? 'application/xml',
            'message'   => 'Inline SuperPDP document decoded.',
            'http_code' => 200,
            'response'  => [],
        ];
    }
}
