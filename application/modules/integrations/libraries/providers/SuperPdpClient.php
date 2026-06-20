<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SuperPdpClient implements IntegrationClientInterface
{
    private ?string $accessToken = null;
    private array $settings = [];

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
            throw new RuntimeException('Missing SuperPDP OAuth2 settings.');
        }

        $decoded = $this->fetchToken(
            $settings['token_url'],
            $settings['client_id'],
            $settings['client_secret']
        );

        if (empty($decoded['access_token'])) {
            throw new RuntimeException('SuperPDP OAuth failed: no access_token in response.');
        }

        $this->accessToken = $decoded['access_token'];

        return true;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if (!file_exists($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (empty($this->settings['api_base_url'])) {
            throw new RuntimeException('Missing SuperPDP API base URL.');
        }

        if (empty($this->accessToken)) {
            throw new RuntimeException('Missing SuperPDP access token.');
        }

        if (empty($this->settings['invoice_endpoint'])) {
            throw new RuntimeException('Missing invoice endpoint configuration.');
        }

        $url = $this->buildUrl($this->settings['invoice_endpoint']);

        if (!empty($this->settings['disable_pre_check'])) {
            $url .= '?disable_pre_check=1';
        }

        $payload = [
            'file' => new CURLFile($documentPath, 'application/pdf', basename($documentPath)),
            'metadata' => json_encode($metadata),
        ];

        return $this->request(RequestMethod::POST, $url, $payload, true, [
            'document_path' => $documentPath,
            'metadata' => $metadata,
        ]);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        if (empty($this->settings['invoice_status_endpoint'])) {
            throw new RuntimeException('Missing invoice status endpoint configuration.');
        }

        $endpoint = str_replace('{id}', urlencode($externalId), $this->settings['invoice_status_endpoint']);
        $url = $this->buildUrl($endpoint);

        $response = $this->request(RequestMethod::GET, $url, [], false, ['external_id' => $externalId]);

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function receiveInvoices(array $filters = []): array
    {
        if (empty($this->settings['incoming_invoices_endpoint'])) {
            throw new RuntimeException('Missing incoming invoices endpoint configuration.');
        }

        $url = $this->buildUrl($this->settings['incoming_invoices_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        if (empty($this->settings['invoice_events_endpoint'])) {
            throw new RuntimeException('Missing invoice events endpoint configuration.');
        }

        $url = $this->buildUrl($this->settings['invoice_events_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    protected function fetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $tokenUrl,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]),
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('SuperPDP OAuth error: ' . $curlError);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException('SuperPDP OAuth failed: ' . $rawResponse);
        }

        return json_decode($rawResponse, true) ?? [];
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Accept: application/json',
        ];

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === RequestMethod::POST) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $multipart ? $payload : json_encode($payload);

            if (!$multipart) {
                $headers[] = 'Content-Type: application/json';
                $options[CURLOPT_HTTPHEADER] = $headers;
            }
        }

        curl_setopt_array($ch, $options);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($rawResponse, true);

        if ($curlError) {
            return [
                'success' => false,
                'external_id' => null,
                'status' => 'error',
                'message' => $curlError,
                'http_code' => $httpCode,
                'request' => array_merge(['url' => $url, 'method' => $method->value], $requestDebug),
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $decoded['id'] ?? $decoded['external_id'] ?? null,
            'status' => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message' => $decoded['message'] ?? 'SuperPDP API response received',
            'http_code' => $httpCode,
            'request' => array_merge(['url' => $url, 'method' => $method->value], $requestDebug),
            'response' => $decoded ?: $rawResponse,
        ];
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
