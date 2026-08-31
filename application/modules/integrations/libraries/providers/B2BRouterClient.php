<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * B2Brouter Spain e-invoicing provider.
 *
 * B2Brouter accepts an existing Facturae/UBL XML file through its import API,
 * queues it for the configured transport (Peppol, b2brouter, FACe, ...), and
 * exposes the resulting invoice and state through the invoice API.
 *
 * @see https://developer.b2brouter.net/reference/import-invoice
 */
class B2BRouterClient implements IntegrationClientInterface
{
    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

    public static function clientCode(): string
    {
        return 'b2brouter';
    }

    public static function clientName(): string
    {
        return 'B2Brouter Spain';
    }

    public static function authType(): string
    {
        return 'api_key';
    }

    public static function defaultSettings(): array
    {
        return [
            'api_key'                    => '',
            'account_id'                 => '',
            'api_base_url'               => 'https://api-staging.b2brouter.net',
            'api_version'                => '2026-06-26',
            'import_endpoint'            => '/accounts/{account}/invoices/import',
            'invoice_status_endpoint'    => '/invoices/{id}',
            'incoming_invoices_endpoint' => '/accounts/{account}/invoices',
            'incoming_document_endpoint' => '/invoices/{id}/as/original',
            'events_endpoint'            => '/accounts/{account}/invoices',
            'transport_type_code'        => 'peppol',
            'document_type_code'         => 'xml.facturae.3.2.2',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'api_key' => [
                'type'      => 'password',
                'label'     => 'api_key',
                'required'  => true,
                'sensitive' => true,
            ],
            'account_id' => [
                'type'     => 'text',
                'label'    => 'account_id',
                'required' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
            'api_version' => [
                'type'     => 'text',
                'label'    => 'api_version',
                'required' => true,
            ],
            'import_endpoint' => [
                'type'     => 'path',
                'label'    => 'import_endpoint',
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
            'events_endpoint' => [
                'type'     => 'path',
                'label'    => 'events_endpoint',
                'required' => true,
            ],
            'transport_type_code' => [
                'type'     => 'text',
                'label'    => 'transport_type_code',
                'required' => true,
            ],
            'document_type_code' => [
                'type'     => 'text',
                'label'    => 'document_type_code',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = array_replace(self::defaultSettings(), $settings);

        foreach (['api_key', 'account_id', 'api_base_url'] as $field) {
            if (empty($this->settings[$field])) {
                throw new RuntimeException('Missing B2Brouter setting: ' . $field);
            }
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        // B2Brouter uses a long-lived API key, not a token exchange.
        return (string) ($settings['api_key'] ?? '');
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new RuntimeException('B2Brouter Spain requires a Facturae or UBL XML document.');
        }

        $document = file_get_contents($documentPath);
        if ($document === false || trim($document) === '') {
            throw new RuntimeException('Unable to read the B2Brouter invoice XML document.');
        }

        $endpoint = str_replace('{account}', rawurlencode($this->setting('account_id')), $this->setting('import_endpoint'));
        $response = ProviderResponseNormalizer::entity(
            $this->request(RequestMethod::POST, $endpoint, [
                'body'    => $document,
                'headers' => ['Content-Type: application/xml'],
                'query'   => [
                    'send_after_import'               => 'true',
                    'issued'                          => 'true',
                    'transport_type_code_for_contact' => $this->setting('transport_type_code'),
                    'document_type_code_for_contact'  => $this->setting('document_type_code'),
                ],
            ]),
            ['invoice', 'data']
        );
        $this->normalizeInvoice($response);

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'B2Brouter accepted the invoice but returned no invoice ID.';
        }

        $response['request']['invoice_id'] = $metadata['invoice_id'] ?? null;

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->setting('invoice_status_endpoint'));
        $response = ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $endpoint), ['invoice', 'data']);
        $this->normalizeInvoice($response);

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $filters += ['type' => 'ReceivedInvoice', 'limit' => 100];
        $endpoint = str_replace('{account}', rawurlencode($this->setting('account_id')), $this->setting('incoming_invoices_endpoint'));
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $endpoint, ['query' => $filters]),
            ['invoices', 'items', 'data'],
            'invoices'
        );

        foreach ($response['response']['invoices'] ?? [] as &$invoice) {
            if (is_array($invoice) && isset($invoice['id']) && is_scalar($invoice['id'])) {
                $invoice['external_id'] = (string) $invoice['id'];
            }
        }
        unset($invoice);

        return $response;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $externalId = $invoice['id'] ?? $invoice['external_id'] ?? null;
        if ( ! is_scalar($externalId) || (string) $externalId === '') {
            throw new RuntimeException('B2Brouter incoming invoice has no invoice ID.');
        }

        $endpoint = str_replace('{id}', rawurlencode((string) $externalId), $this->setting('incoming_document_endpoint'));
        $download = $this->request(RequestMethod::GET, $endpoint, [
            'binary'             => true,
            'max_response_bytes' => 15 * 1024 * 1024,
            'headers'            => ['Accept: application/xml'],
        ]);

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? ('b2brouter-' . $externalId . '.xml'),
            'mime_type' => $download['content_type'] ?? 'application/xml',
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['invoice_id' => (string) $externalId],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $filters += ['type' => 'IssuedInvoice', 'limit' => 100];
        $endpoint = str_replace('{account}', rawurlencode($this->setting('account_id')), $this->setting('events_endpoint'));
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $endpoint, ['query' => $filters]),
            ['invoices', 'items', 'data'],
            'events'
        );

        $events = [];
        foreach ($response['response']['events'] ?? [] as $invoice) {
            if ( ! is_array($invoice) || ! isset($invoice['id'])) {
                continue;
            }

            $events[] = [
                'invoice_id'  => (string) $invoice['id'],
                'external_id' => (string) $invoice['id'],
                'status'      => $invoice['state'] ?? $invoice['status'] ?? null,
                'message'     => $invoice['error'] ?? $invoice['reason'] ?? null,
                'updated_at'  => $invoice['updated_at'] ?? $invoice['updatedAt'] ?? null,
            ];
        }
        $response['response']['events'] = $events;

        return $response;
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        $metadata['invoice_number'] = $invoice->invoice_number ?? null;

        return $metadata;
    }

    private function request(RequestMethod $method, string $endpoint, array $options = []): array
    {
        $options['headers'] = array_merge([
            'X-B2B-API-Key: ' . $this->setting('api_key'),
            'X-B2B-API-Version: ' . $this->setting('api_version'),
        ], $options['headers'] ?? []);

        return $this->http->request(
            $method,
            rtrim($this->setting('api_base_url'), '/') . '/' . ltrim($endpoint, '/'),
            $options
        );
    }

    private function normalizeInvoice(array &$response): void
    {
        $entity = $response['response']['entity'] ?? $response['response'] ?? [];
        if ( ! is_array($entity)) {
            return;
        }

        $id = $entity['id'] ?? $entity['invoice_id'] ?? null;
        if (is_scalar($id)) {
            $response['external_id'] = (string) $id;
        }

        $state = $entity['state'] ?? $entity['status'] ?? null;
        if (is_scalar($state)) {
            $response['status'] = (string) $state;
        }
    }

    private function setting(string $name): string
    {
        $value = $this->settings[$name] ?? '';
        if ( ! is_string($value) || trim($value) === '') {
            throw new RuntimeException('Missing B2Brouter setting: ' . $name);
        }

        return trim($value);
    }
}
