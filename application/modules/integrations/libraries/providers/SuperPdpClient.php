<?php

defined('BASEPATH') or exit('No direct script access allowed');

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

    public static function defaultSettings(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'token_url' => 'https://api.superpdp.tech/oauth2/token',
            'api_base_url' => 'https://api.superpdp.tech',
            'invoice_endpoint' => '/v1.beta/invoices',
            'invoice_status_endpoint' => '/v1.beta/invoices/{id}',
            'incoming_invoices_endpoint' => '/v1.beta/invoices',
            'invoice_events_endpoint' => '/v1.beta/invoice_events',
            'disable_pre_check' => false,
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = $settings;

        if (
            empty($settings['client_id']) ||
            empty($settings['client_secret']) ||
            empty($settings['token_url'])
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
     * POST /v1.beta/invoices  (multipart)
     *
     * Request:
     *   file      file    PDF invoice document
     *   metadata  string  JSON-encoded metadata object
     *
     * Response (JSON):
     *   id              string  external invoice ID
     *   status          string  processing|sent|error
     *   external_id     string  alias for id
     */
    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if (!file_exists($documentPath)) {
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

        $url = $this->buildUrl($this->settings['invoice_endpoint']);

        if (!empty($this->settings['disable_pre_check'])) {
            $url .= '?disable_pre_check=1';
        }

        $payload = [
            'file' => new \CURLFile($documentPath, 'application/pdf', basename($documentPath)),
            'metadata' => json_encode($metadata),
        ];

        return $this->request(RequestMethod::POST, $url, $payload, true, [
            'document_path' => $documentPath,
            'metadata' => $metadata,
        ]);
    }

    /**
     * GET /v1.beta/invoices/{id}
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
        $url = $this->buildUrl($endpoint);

        $response = $this->request(RequestMethod::GET, $url, [], false, ['external_id' => $externalId]);

        return array_merge($response, ['external_id' => $externalId]);
    }

    /**
     * GET /v1.beta/invoices?{filters}
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

        return $this->request(RequestMethod::GET, $url);
    }

    /**
     * GET /v1.beta/invoice_events?{filters}
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

        return $this->request(RequestMethod::GET, $url);
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    /**
     * POST {token_url}  (form-encoded)
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

        if (!$result['success']) {
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
        } elseif ($method === RequestMethod::POST && !empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->http->request($method, $url, $options);
    }

    private function buildUrl(string $endpoint, array $query = []): string
    {
        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
