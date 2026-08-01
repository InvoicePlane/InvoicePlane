<?php

defined('BASEPATH') || exit('No direct script access allowed');

class SuperPdpClient implements IntegrationClientInterface
{
    private ?string $accessToken = null;

    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

    public static function clientCode(): string
    {
        return 'superpdp';
    }

    public static function clientName(): string
    {
        return 'SuperPDP';
    }

    public static function authType(): string
    {
        return 'oauth2';
    }

    public static function defaultSettings(): array
    {
        return [
            'client_id'                  => '',
            'client_secret'              => '',
            'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
            'api_base_url'               => 'https://api.superpdp.tech',
            'invoice_endpoint'           => '/v1.beta/invoices',
            'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
            'incoming_invoices_endpoint' => '/v1.beta/invoices',
            'incoming_document_endpoint' => '/v1.beta/invoices/{id}/document',
            'invoice_events_endpoint'    => '/v1.beta/invoice_events',
            'disable_pre_check'          => false,
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'client_id' => [
                'type'     => 'text',
                'label'    => 'client_id',
                'required' => true,
            ],
            'client_secret' => [
                'type'      => 'password',
                'label'     => 'client_secret',
                'required'  => true,
                'sensitive' => true,
            ],
            'token_url' => [
                'type'     => 'url',
                'label'    => 'token_url',
                'required' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
            'invoice_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_endpoint',
                'required' => true,
            ],
            'invoice_status_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_status_endpoint',
                'required' => true,
            ],
            'incoming_invoices_endpoint' => [
                'type'     => 'path',
                'label'    => 'incoming_invoices_endpoint',
                'required' => true,
            ],
            'incoming_document_endpoint' => [
                'type'     => 'path',
                'label'    => 'incoming_document_endpoint',
                'required' => true,
            ],
            'invoice_events_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_events_endpoint',
                'required' => true,
            ],
            'disable_pre_check' => [
                'type'  => 'checkbox',
                'label' => 'disable_pre_check',
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

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    /**
     * POST {token_url}  (form-encoded).
     *
     * Request:
     *   grant_type     client_credentials
     *   client_id      string
     *   client_secret  string
     *
     * Response (JSON):
     *   access_token  string
     *   token_type    string  "Bearer"
     *   expires_in    int
     */
    protected function oauthFetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $result = $this->http->request(RequestMethod::POST, $tokenUrl, [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ],
        ]);

        if ( ! $result['success']) {
            throw new \RuntimeException('SuperPDP OAuth error: ' . $result['message']);
        }

        return $result['response'];
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $options = ['bearer' => $this->accessToken];

        if ($multipart) {
            $options['multipart'] = $payload;
        } elseif ($method === RequestMethod::POST && ! empty($payload)) {
            $options['json'] = $payload;
        }

        $response = $this->http->request($method, $url, $options);

        if ($requestDebug !== []) {
            $response['request'] = array_merge($response['request'] ?? [], $requestDebug);
        }

        return $response;
    }

    private function buildUrl(string $endpoint, array $query = []): string
    {
        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if ( ! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
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
