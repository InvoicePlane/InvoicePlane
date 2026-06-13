<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SuperPdpProvider implements MerchantProviderInterface
{
    private ?string $accessToken = null;
    private array $settings = [];

    public static function providerCode(): string
    {
        return 'superpdp';
    }

    public static function providerName(): string
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

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $settings['token_url'],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $settings['client_id'],
                'client_secret' => $settings['client_secret'],
            ]),
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('SuperPDP OAuth error: ' . $curlError);
        }

        $decoded = json_decode($rawResponse, true);

        if ($httpCode < 200 || $httpCode >= 300 || empty($decoded['access_token'])) {
            throw new RuntimeException('SuperPDP OAuth failed: ' . $rawResponse);
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
           throw new RuntimeException(
              'Missing invoice endpoint configuration.'
           );
        }
        $endpoint = $this->settings['invoice_endpoint'];

        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if (!empty($this->settings['disable_pre_check'])) {
           $url .= '?disable_pre_check=1';
        }

        $ch = curl_init();

	// Paramètres spécifiques SuperPDP

        $postFields = [
            'file' => new CURLFile($documentPath, 'application/pdf', basename($documentPath)),
	    'metadata' => json_encode($metadata),
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => $postFields,
        ]);

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
                'request' => [
                    'url' => $url,
                    'document_path' => $documentPath,
                    'metadata' => $metadata,
                ],
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $decoded['id'] ?? $decoded['external_id'] ?? null,
            'status' => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message' => $decoded['message'] ?? 'SuperPDP API response received',
            'http_code' => $httpCode,
            'request' => [
                'url' => $url,
                'document_path' => $documentPath,
                'metadata' => $metadata,
            ],
            'response' => $decoded ?: $rawResponse,
        ];
    }

    public function getInvoiceStatus(string $externalId): array
    {
	if (empty($this->settings['invoice_status_endpoint'])) {
            throw new RuntimeException(
               'Missing invoice status endpoint configuration.'
            );
        }
        $endpoint = $this->settings['invoice_status_endpoint'];
        $endpoint = str_replace('{id}', urlencode($externalId), $endpoint);

        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/json',
            ],
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $decoded = json_decode($rawResponse, true);

        if ($curlError) {
            return [
                'success' => false,
                'external_id' => $externalId,
                'status' => 'error',
                'message' => $curlError,
                'http_code' => $httpCode,
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $externalId,
            'status' => $decoded['status'] ?? 'unknown',
            'message' => $decoded['message'] ?? 'SuperPDP status response received',
            'http_code' => $httpCode,
            'response' => $decoded ?: $rawResponse,
        ];
    }

    public function receiveInvoices(array $filters = []): array
    {
	if (empty($this->settings['incoming_invoices_endpoint'])) {
            throw new RuntimeException(
               'Missing incoming invoices endpoint configuration.'
            );
        }

        $endpoint = $this->settings['incoming_invoices_endpoint'];

        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/json',
            ],
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($rawResponse, true);

        if ($curlError) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => $curlError,
                'http_code' => $httpCode,
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $httpCode >= 200 && $httpCode < 300 ? 'received' : 'error',
            'message' => 'Incoming invoices response received',
            'http_code' => $httpCode,
            'response' => $decoded ?: $rawResponse,
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
	if (empty($this->settings['invoice_events_endpoint'])) {
            throw new RuntimeException(
               'Missing invoice events endpoint configuration.'
            );
        }

        $endpoint = $this->settings['invoice_events_endpoint'];
        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/json',
            ],
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($rawResponse, true);

        if ($curlError) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => $curlError,
                'http_code' => $httpCode,
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $httpCode >= 200 && $httpCode < 300 ? 'events_received' : 'error',
            'message' => 'Invoice events response received',
            'http_code' => $httpCode,
            'response' => $decoded ?: $rawResponse,
        ];
    }
}
