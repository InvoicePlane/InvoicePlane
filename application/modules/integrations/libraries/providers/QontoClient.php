<?php

defined('BASEPATH') || exit('No direct script access allowed');

class QontoClient implements IntegrationClientInterface
{
    private array $settings = [];

    private ApiClientInterface $http;

    private RemoteUrlGuard $urlGuard;

    public function __construct(?ApiClientInterface $http = null, ?RemoteUrlGuard $urlGuard = null)
    {
        $this->http     = $http ?? new CurlApiClient();
        $this->urlGuard = $urlGuard ?? new RemoteUrlGuard();
    }

    public static function clientCode(): string
    {
        return 'qonto';
    }

    public static function clientName(): string
    {
        return 'Qonto PA';
    }

    public static function authType(): string
    {
        return 'bearer';
    }

    public static function defaultSettings(): array
    {
        return [
            'access_token'               => '',
            'staging_token'              => '',
            'api_base_url'               => 'https://thirdparty.qonto.com',
            'import_endpoint'            => '/v2/client_invoices/bulk',
            'client_invoices_endpoint'   => '/v2/client_invoices',
            'send_invoice_endpoint'      => '/v2/client_invoices/{id}/send_by_einvoice',
            'invoice_status_endpoint'    => '/v2/client_invoices/{id}',
            'incoming_invoices_endpoint' => '/v2/supplier_invoices',
            'attachment_endpoint'        => '/v2/attachments/{id}',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'access_token' => [
                'type'      => 'password',
                'label'     => 'access_token',
                'required'  => true,
                'sensitive' => true,
            ],
            'staging_token' => [
                'type'      => 'password',
                'label'     => 'staging_token',
                'sensitive' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
            'import_endpoint' => [
                'type'     => 'path',
                'label'    => 'import_endpoint',
                'required' => true,
            ],
            'client_invoices_endpoint' => [
                'type'     => 'path',
                'label'    => 'client_invoices_endpoint',
                'required' => true,
            ],
            'send_invoice_endpoint' => [
                'type'     => 'path',
                'label'    => 'send_invoice_endpoint',
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
            'attachment_endpoint' => [
                'type'     => 'path',
                'label'    => 'attachment_endpoint',
                'required' => true,
            ],
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
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new \RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'pdf') {
            throw new \RuntimeException('Qonto e-invoice imports require a Factur-X PDF document.');
        }

        $fileSize = filesize($documentPath);
        if ($fileSize === false || $fileSize > 5 * 1024 * 1024) {
            throw new \RuntimeException('Qonto e-invoice imports are limited to 5 MB per document.');
        }

        foreach (['import_endpoint', 'send_invoice_endpoint'] as $field) {
            $this->requireSetting($field);
        }

        $importResponse = $this->importClientInvoice($documentPath);

        if (empty($importResponse['success'])) {
            return $importResponse;
        }

        $clientInvoiceId = $importResponse['external_id'];

        $sendResponse = $this->sendClientInvoiceByEinvoice($clientInvoiceId);

        $sendResponse['external_id']                  = $clientInvoiceId;
        $sendResponse['request']['client_invoice_id'] = $clientInvoiceId;
        $sendResponse['request']['invoice_id']        = $metadata['invoice_id'] ?? null;

        if ( ! empty($sendResponse['success'])) {
            $sendResponse['status']  = 'pending';
            $sendResponse['message'] = 'Qonto accepted the e-invoice for asynchronous processing.';
        }

        return $sendResponse;
    }

    /**
     * GET /v2/client_invoices/{id}.
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
        $url      = $this->buildUrl($endpoint);

        $response = $this->request(RequestMethod::GET, $url);

        $invoice = $response['response']['client_invoice']
            ?? $response['response']['data']['attributes']
            ?? $response['response']
            ?? [];

        $events = is_array($invoice['einvoicing_lifecycle_events'] ?? null)
            ? $invoice['einvoicing_lifecycle_events']
            : [];
        $latestEvent = $this->latestLifecycleEvent($events);
        $statusCode  = $latestEvent['status_code'] ?? null;

        return [
            'success'     => $response['success'],
            'external_id' => $externalId,
            'status'      => $statusCode ?? $invoice['einvoicing_status'] ?? $invoice['status'] ?? $response['status'],
            'status_code' => $statusCode,
            'message'     => $latestEvent !== []
                ? $latestEvent['reason_message'] ?? $latestEvent['reason'] ?? $response['message']
                : $response['message'],
            'http_code' => $response['http_code'],
            'request'   => $response['request'],
            'response'  => $response['response'],
        ];
    }

    /**
     * GET /v2/supplier_invoices?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of supplier invoice objects
     *   meta    object pagination metadata
     */
    public function receiveInvoices(array $filters = []): array
    {
        $this->requireSetting('incoming_invoices_endpoint');

        $filters += ['filter[source][]' => 'e_invoicing', 'per_page' => 100];
        $url = $this->buildUrl($this->settings['incoming_invoices_endpoint'], $filters);

        $response = $this->request(RequestMethod::GET, $url);
        $invoices = $response['response']['supplier_invoices'] ?? [];

        if (is_array($invoices)) {
            $response['response']['invoices'] = $invoices;
        }

        return $response;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $this->requireSetting('attachment_endpoint');

        $attachmentId = $invoice['attachment_id'] ?? $invoice['display_attachment_id'] ?? null;
        if ( ! is_string($attachmentId) || $attachmentId === '') {
            throw new \RuntimeException('Qonto supplier invoice has no downloadable attachment ID.');
        }

        $endpoint = str_replace('{id}', rawurlencode($attachmentId), $this->settings['attachment_endpoint']);
        $metadata = $this->request(RequestMethod::GET, $this->buildUrl($endpoint));
        if (empty($metadata['success'])) {
            return $metadata + ['content' => null, 'filename' => null, 'mime_type' => null];
        }

        $attachment = $metadata['response']['attachment']
            ?? $metadata['response']['data']['attributes']
            ?? $metadata['response']['data']
            ?? [];
        if ( ! is_array($attachment)) {
            throw new \RuntimeException('Qonto attachment metadata is malformed.');
        }

        $url = $attachment['url'] ?? null;
        if ( ! is_string($url) || $url === '') {
            throw new \RuntimeException('Qonto attachment metadata has no download URL.');
        }

        $declaredSize = filter_var($attachment['file_size'] ?? null, FILTER_VALIDATE_INT);
        if ($declaredSize !== false && $declaredSize > 15 * 1024 * 1024) {
            throw new \RuntimeException('Qonto attachment exceeds the 15 MB incoming-document limit.');
        }

        $resolved = $this->urlGuard->validateAndResolve($url);
        $download = $this->http->request(RequestMethod::GET, $url, [
            'binary'             => true,
            'max_response_bytes' => 15 * 1024 * 1024,
            'resolve'            => [$resolved['host'], $resolved['port'], $resolved['ip']],
        ]);

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['file_name'] ?? $attachment['file_name'] ?? 'qonto-invoice.pdf',
            'mime_type' => $attachment['file_content_type'] ?? $download['content_type'] ?? null,
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['attachment_id' => $attachmentId],
        ];
    }

    /**
     * GET /v2/client_invoices?{filters}, then expose embedded lifecycle events.
     *
     * Response (JSON):
     *   client_invoices[].einvoicing_lifecycle_events[]
     */
    public function getInvoiceEvents(array $filters = []): array
    {
        $this->requireSetting('client_invoices_endpoint');

        $filters += ['exclude_imported' => 'false', 'per_page' => 100];
        $url      = $this->buildUrl($this->settings['client_invoices_endpoint'], $filters);
        $response = $this->request(RequestMethod::GET, $url);
        $invoices = $response['response']['client_invoices'] ?? [];
        $events   = [];

        if (is_array($invoices)) {
            foreach ($invoices as $invoice) {
                if ( ! is_array($invoice)) {
                    continue;
                }

                foreach ($invoice['einvoicing_lifecycle_events'] ?? [] as $event) {
                    if ( ! is_array($event)) {
                        continue;
                    }

                    $event['invoice_id']     = $invoice['id'] ?? null;
                    $event['external_id']    = $invoice['id'] ?? null;
                    $event['invoice_number'] = $invoice['number'] ?? null;
                    $events[]                = $event;
                }
            }
        }

        $response['response']['events'] = $events;

        return $response;
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        $metadata['invoice_number'] = $invoice->invoice_number ?? null;

        return $metadata;
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $options = ['bearer' => $this->settings['access_token']];

        if ( ! empty($this->settings['staging_token'])) {
            $options['headers'] = ['X-Qonto-Staging-Token: ' . $this->settings['staging_token']];
        }

        if ($multipart) {
            $options['multipart'] = $payload;
        } elseif ($method === RequestMethod::POST && ! empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->http->request($method, $url, $options);
    }

    /**
     * POST /v2/client_invoices/bulk with one already-issued Factur-X PDF.
     */
    private function importClientInvoice(string $documentPath): array
    {
        $url = $this->buildUrl($this->settings['import_endpoint']);

        $payload = [
            'client_invoices' => new \CURLFile(
                $documentPath,
                'application/pdf',
                basename($documentPath)
            ),
        ];

        $response = $this->request(RequestMethod::POST, $url, $payload, true, [
            'document_path' => $documentPath,
        ]);

        $invoiceId = $response['response']['client_invoices'][0]['invoice_id']
            ?? null;

        $response['external_id'] = $invoiceId;

        if ($response['success'] && empty($invoiceId)) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'Qonto import succeeded but no client invoice ID was returned.';
        }

        return $response;
    }

    /**
     * POST /v2/client_invoices/{id}/send_by_einvoice.
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

    private function buildUrl(string $endpoint, array $query = []): string
    {
        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if ( ! empty($query)) {
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

    private function latestLifecycleEvent(array $events): array
    {
        $latest          = [];
        $latestTimestamp = '';

        foreach ($events as $event) {
            if ( ! is_array($event)) {
                continue;
            }

            $timestamp = is_string($event['timestamp'] ?? null) ? $event['timestamp'] : '';
            if ($latest === [] || $timestamp === '' || $timestamp >= $latestTimestamp) {
                $latest          = $event;
                $latestTimestamp = $timestamp;
            }
        }

        return $latest;
    }
}
