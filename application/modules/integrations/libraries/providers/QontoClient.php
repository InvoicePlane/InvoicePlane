<?php

defined('BASEPATH') or exit('No direct script access allowed');

class QontoClient implements IntegrationClientInterface
{
    private array $settings = [];

    public static function clientCode(): string
    {
        return 'qonto';
    }

    public static function clientName(): string
    {
        return 'Qonto PA';
    }

    public static function defaultSettings(): array
    {
        return [
            'access_token' => '',
            'staging_token' => '',
            'api_base_url' => 'https://thirdparty.qonto.com',
            'upload_endpoint' => '/v2/client_invoices/uploads',
            'invoice_endpoint' => '/v2/client_invoices',
            'send_invoice_endpoint' => '/v2/client_invoices/{id}/send_by_einvoice',
            'invoice_status_endpoint' => '/v2/client_invoices/{id}',
            'incoming_invoices_endpoint' => '/v2/supplier_invoices',
            'invoice_events_endpoint' => '/v2/client_invoices/{id}',
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = $settings;

        foreach (['access_token', 'api_base_url'] as $field) {
            if (empty($settings[$field])) {
                throw new RuntimeException('Missing Qonto setting: ' . $field);
            }
        }

        return true;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if (!file_exists($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        foreach (['upload_endpoint', 'invoice_endpoint', 'send_invoice_endpoint'] as $field) {
            $this->requireSetting($field);
        }

        $uploadResponse = $this->uploadInvoiceFile($documentPath);

        if (empty($uploadResponse['success'])) {
            return $uploadResponse;
        }

        $uploadId = $uploadResponse['external_id'];

        $invoicePayload = $metadata['qonto_invoice_payload'] ?? [];

        if (empty($invoicePayload)) {
            return [
                'success' => false,
                'external_id' => null,
                'status' => 'error',
                'message' => 'Missing qonto_invoice_payload metadata. Qonto requires invoice data to create the client invoice.',
                'http_code' => null,
                'request' => [
                    'document_path' => $documentPath,
                    'metadata' => $metadata,
                ],
                'response' => $uploadResponse,
            ];
        }

        $invoicePayload['upload_id'] = $uploadId;

        $createResponse = $this->createClientInvoice($invoicePayload);

        if (empty($createResponse['success'])) {
            return $createResponse;
        }

        $clientInvoiceId = $createResponse['external_id'];

        $sendResponse = $this->sendClientInvoiceByEinvoice($clientInvoiceId);

        $sendResponse['external_id'] = $clientInvoiceId;
        $sendResponse['request']['upload_id'] = $uploadId;
        $sendResponse['request']['client_invoice_id'] = $clientInvoiceId;

        return $sendResponse;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $this->requireSetting('invoice_status_endpoint');

        $endpoint = str_replace('{id}', urlencode($externalId), $this->settings['invoice_status_endpoint']);
        $url = $this->buildUrl($endpoint);

        $response = $this->request(RequestMethod::GET, $url);

        $invoice = $response['response']['client_invoice']
            ?? $response['response']['data']['attributes']
            ?? $response['response']
            ?? [];

        return [
            'success' => $response['success'],
            'external_id' => $externalId,
            'status' => $invoice['status'] ?? $response['status'],
            'message' => $response['message'],
            'http_code' => $response['http_code'],
            'request' => $response['request'],
            'response' => $response['response'],
        ];
    }

    public function receiveInvoices(array $filters = []): array
    {
        $this->requireSetting('incoming_invoices_endpoint');

        $url = $this->buildUrl($this->settings['incoming_invoices_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $this->requireSetting('invoice_events_endpoint');

        $url = $this->buildUrl($this->settings['invoice_events_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    private function uploadInvoiceFile(string $documentPath): array
    {
        $url = $this->buildUrl($this->settings['upload_endpoint']);

        $payload = [
            'client_invoices_upload' => new CURLFile(
                $documentPath,
                'application/pdf',
                basename($documentPath)
            ),
        ];

        $response = $this->request(RequestMethod::POST, $url, $payload, true, [
            'document_path' => $documentPath,
        ]);

        $uploadId = $response['response']['data']['id']
            ?? $response['response']['data']['attributes']['id']
            ?? null;

        $response['external_id'] = $uploadId;

        if ($response['success'] && empty($uploadId)) {
            $response['success'] = false;
            $response['status'] = 'error';
            $response['message'] = 'Qonto upload succeeded but no upload ID was returned.';
        }

        return $response;
    }

    private function createClientInvoice(array $payload): array
    {
        $url = $this->buildUrl($this->settings['invoice_endpoint']);

        $response = $this->request(RequestMethod::POST, $url, $payload, false, [
            'payload' => $payload,
        ]);

        $clientInvoiceId = $response['response']['client_invoice']['id']
            ?? $response['response']['data']['id']
            ?? $response['response']['id']
            ?? null;

        $response['external_id'] = $clientInvoiceId;

        if ($response['success'] && empty($clientInvoiceId)) {
            $response['success'] = false;
            $response['status'] = 'error';
            $response['message'] = 'Qonto invoice creation succeeded but no client invoice ID was returned.';
        }

        return $response;
    }

    private function sendClientInvoiceByEinvoice(string $clientInvoiceId): array
    {
        $endpoint = str_replace(
            '{id}',
            urlencode($clientInvoiceId),
            $this->settings['send_invoice_endpoint']
        );

        $url = $this->buildUrl($endpoint);

        return $this->request(RequestMethod::POST, $url, [], false, [
            'client_invoice_id' => $clientInvoiceId,
        ]);
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $headers = [
            'Authorization: Bearer ' . $this->settings['access_token'],
            'Accept: application/json',
        ];

        if (!empty($this->settings['staging_token'])) {
            $headers[] = 'X-Qonto-Staging-Token: ' . $this->settings['staging_token'];
        }

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === RequestMethod::POST) {
            $options[CURLOPT_POST] = true;

            if ($multipart) {
                $options[CURLOPT_POSTFIELDS] = $payload;
            } else {
                $headers[] = 'Content-Type: application/json';
                $options[CURLOPT_HTTPHEADER] = $headers;
                $options[CURLOPT_POSTFIELDS] = json_encode($payload);
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
                'request' => [
                    'url' => $url,
                    'method' => $method->value,
                ] + $requestDebug,
                'response' => $rawResponse,
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $decoded['id'] ?? $decoded['data']['id'] ?? null,
            'status' => $decoded['status']
                ?? $decoded['client_invoice']['status']
                ?? $decoded['data']['attributes']['status']
                ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message' => $decoded['message'] ?? 'Qonto API response received',
            'http_code' => $httpCode,
            'request' => [
                'url' => $url,
                'method' => $method,
            ] + $requestDebug,
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

    private function requireSetting(string $key): void
    {
        if (empty($this->settings[$key])) {
            throw new RuntimeException('Missing Qonto setting: ' . $key);
        }
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        $metadata['qonto_invoice_payload'] = [
            'client_invoice' => [
                'number' => $invoice->invoice_number,
                'issue_date' => $invoice->invoice_date_created,
                'due_date' => $invoice->invoice_date_due,
                'currency' => $invoice->client_currency ?: 'EUR',
                'client' => [
                    'name' => $invoice->client_name,
                    'email' => $invoice->client_email,
                ],
                'line_items' => array_map(static function ($item) {
                    return [
                        'title' => $item->item_name,
                        'description' => $item->item_description,
                        'quantity' => (float) $item->item_quantity,
                        'unit_price' => (float) $item->item_price,
                    ];
                }, $items),
            ],
        ];

        return $metadata;
    }
}

