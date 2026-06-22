<?php

defined('BASEPATH') or exit('No direct script access allowed');

class QontoClient implements IntegrationClientInterface
{
    private array $settings = [];
    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

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
                throw new \RuntimeException('Missing Qonto setting: ' . $field);
            }
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        return $settings['access_token'] ?? '';
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if (!file_exists($documentPath)) {
            throw new \RuntimeException('Invoice document not found: ' . $documentPath);
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

    /**
     * GET /v2/client_invoices/{id}
     *
     * Response (JSON):
     *   client_invoice.id      string
     *   client_invoice.status  string  pending|sent|paid|rejected
     *   client_invoice.*       mixed   full invoice attributes
     */
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

    /**
     * GET /v2/supplier_invoices?{filters}
     *
     * Response (JSON):
     *   data[]  array  list of supplier invoice objects
     *   meta    object pagination metadata
     */
    public function receiveInvoices(array $filters = []): array
    {
        $this->requireSetting('incoming_invoices_endpoint');

        $url = $this->buildUrl($this->settings['incoming_invoices_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    /**
     * GET /v2/client_invoices/{id}?{filters}
     *
     * Response (JSON):
     *   client_invoice  object  includes status history events
     */
    public function getInvoiceEvents(array $filters = []): array
    {
        $this->requireSetting('invoice_events_endpoint');

        $url = $this->buildUrl($this->settings['invoice_events_endpoint'], $filters);

        return $this->request(RequestMethod::GET, $url);
    }

    /**
     * POST /v2/client_invoices/uploads  (multipart)
     *
     * Request:
     *   client_invoices_upload  file  PDF invoice file
     *
     * Response (JSON):
     *   data.id         string  upload UUID to reference in invoice creation
     *   data.attributes object  upload metadata
     */
    private function uploadInvoiceFile(string $documentPath): array
    {
        $url = $this->buildUrl($this->settings['upload_endpoint']);

        $payload = [
            'client_invoices_upload' => new \CURLFile(
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

    /**
     * POST /v2/client_invoices
     *
     * Request (JSON):
     *   client_invoice.number     string
     *   client_invoice.issue_date string  YYYY-MM-DD
     *   client_invoice.due_date   string  YYYY-MM-DD
     *   client_invoice.currency   string  ISO 4217 (e.g. EUR)
     *   client_invoice.upload_id  string  from upload step
     *   client_invoice.client     object  {name, email}
     *   client_invoice.line_items array   [{title, quantity, unit_price}]
     *
     * Response (JSON):
     *   client_invoice.id  string  created invoice UUID
     */
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

    /**
     * POST /v2/client_invoices/{id}/send_by_einvoice
     *
     * Request: empty body
     *
     * Response (JSON):
     *   client_invoice.id      string
     *   client_invoice.status  string  "sent"
     */
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
        $options = ['bearer' => $this->settings['access_token']];

        if (!empty($this->settings['staging_token'])) {
            $options['headers'] = ['X-Qonto-Staging-Token: ' . $this->settings['staging_token']];
        }

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

    private function requireSetting(string $key): void
    {
        if (empty($this->settings[$key])) {
            throw new \RuntimeException('Missing Qonto setting: ' . $key);
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
