<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * A-CUBE Italy e-invoicing provider (FatturaPA / SDI).
 *
 * The Italy API accepts the original FatturaPA XML payload.
 *
 * @see https://docs.acubeapi.com/documentation/italy/gov-it/invoices/sending-invoice
 */
class ACubeClient implements IntegrationClientInterface
{
    use ProviderPing;

    private ?string $accessToken = null;

    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

    public static function clientCode(): string
    {
        return 'acube';
    }

    public static function clientName(): string
    {
        return 'A-CUBE Italy (SDI)';
    }

    public static function authType(): string
    {
        return 'bearer';
    }

    public static function defaultSettings(): array
    {
        return [
            'email'                     => '',
            'password'                  => '',
            'environment'               => 'production',
            'token_url'                 => 'https://common.api.acubeapi.com/login',
            'api_base_url'              => '',
            'invoices_endpoint'         => '/invoices',
            'invoice_status_endpoint'   => '/invoices/{id}',
            'invoice_document_endpoint' => '/invoices/{id}',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'email' => [
                'type'     => 'text',
                'label'    => 'email',
                'required' => true,
            ],
            'password' => [
                'type'      => 'password',
                'label'     => 'password',
                'required'  => true,
                'sensitive' => true,
            ],
            'environment' => [
                'type'     => 'select',
                'label'    => 'environment',
                'required' => true,
                'options'  => [
                    'sandbox'    => 'Sandbox',
                    'production' => 'Production',
                ],
            ],
            'token_url' => [
                'type'     => 'url',
                'label'    => 'token_url',
                'required' => true,
            ],
            'api_base_url' => [
                'type'        => 'url',
                'label'       => 'api_base_url',
                'placeholder' => 'Leave empty to use the selected environment',
            ],
            'invoices_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoices_endpoint',
                'required' => true,
            ],
            'invoice_status_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_status_endpoint',
                'required' => true,
            ],
            'invoice_document_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_document_endpoint',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = array_replace(self::defaultSettings(), $settings);

        foreach (['email', 'password', 'token_url'] as $field) {
            if (empty($this->settings[$field])) {
                throw new RuntimeException('Missing A-CUBE setting: ' . $field);
            }
        }

        $this->accessToken = $this->fetchToken($this->settings);
        if ($this->accessToken === '') {
            throw new RuntimeException('A-CUBE authentication failed: no JWT token in response.');
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        $environment = $settings['environment'] ?? 'production';
        if ( ! in_array($environment, ['sandbox', 'production'], true)) {
            throw new RuntimeException('Invalid A-CUBE environment.');
        }

        $response = $this->http->request(RequestMethod::POST, (string) ($settings['token_url'] ?? ''), [
            'json' => [
                'email'       => (string) ($settings['email'] ?? ''),
                'password'    => (string) ($settings['password'] ?? ''),
                'environment' => $environment,
            ],
        ]);

        if (empty($response['success'])) {
            throw new RuntimeException('A-CUBE authentication failed: ' . ($response['message'] ?? 'unknown error'));
        }

        $token = $response['response']['token'] ?? null;

        return is_string($token) ? $token : '';
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new RuntimeException('A-CUBE Italy requires a FatturaPA XML document.');
        }

        $document = file_get_contents($documentPath);
        if ($document === false || trim($document) === '') {
            throw new RuntimeException('Unable to read the A-CUBE invoice XML document.');
        }

        $response = ProviderResponseNormalizer::entity($this->request(RequestMethod::POST, $this->setting('invoices_endpoint'), [
            'body'    => $document,
            'headers' => ['Content-Type: application/xml'],
        ]), ['invoice', 'data']);

        $this->normalizeUuid($response);

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'A-CUBE accepted the invoice but returned no UUID.';
        }

        $response['request']['invoice_id'] = $metadata['invoice_id'] ?? null;

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->setting('invoice_status_endpoint'));
        $response = ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $endpoint), ['invoice', 'data']);
        $this->normalizeUuid($response);
        $entity = $response['response']['entity'] ?? [];
        if (is_array($entity) && isset($entity['marking']) && is_scalar($entity['marking'])) {
            $response['status'] = (string) $entity['marking'];
        }

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $this->setting('invoices_endpoint'), ['query' => $filters]),
            ['invoices', 'items', 'data'],
            'invoices'
        );

        $invoices = $response['response']['invoices'] ?? [];
        if (is_array($invoices)) {
            // A-CUBE's collection includes both directions. Only synchronize
            // documents explicitly marked as inbound to prevent re-importing our own sales invoices.
            $response['response']['invoices'] = array_values(array_filter($invoices, static function ($invoice): bool {
                if ( ! is_array($invoice)) {
                    return false;
                }

                return mb_strtoupper((string) ($invoice['direction'] ?? '')) === 'INBOUND';
            }));
            foreach ($response['response']['invoices'] as &$invoice) {
                if (empty($invoice['external_id']) && isset($invoice['uuid']) && is_scalar($invoice['uuid'])) {
                    $invoice['external_id'] = (string) $invoice['uuid'];
                }
            }
            unset($invoice);
        }

        return $response;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $externalId = $invoice['uuid'] ?? $invoice['invoiceUuid'] ?? $invoice['id'] ?? $invoice['external_id'] ?? null;
        if ( ! is_string($externalId) || $externalId === '') {
            throw new RuntimeException('A-CUBE incoming invoice has no UUID.');
        }

        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->setting('invoice_document_endpoint'));
        $download = $this->request(RequestMethod::GET, $endpoint, [
            'binary'             => true,
            'max_response_bytes' => 15 * 1024 * 1024,
            'headers'            => ['Accept: application/xml'],
        ]);

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? $invoice['fileName'] ?? ('acube-' . $externalId . '.xml'),
            'mime_type' => $download['content_type'] ?? $invoice['mime_type'] ?? 'application/xml',
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['invoice_uuid' => $externalId],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        // A-CUBE publishes lifecycle changes through signed webhooks. Polling
        // the collection remains useful as a fallback and records its current status.
        $response = $this->request(RequestMethod::GET, $this->setting('invoices_endpoint'), ['query' => $filters]);
        $response = ProviderResponseNormalizer::collection($response, ['invoices', 'items', 'data'], 'invoices');
        $events   = [];

        foreach ($response['response']['invoices'] ?? [] as $invoice) {
            if ( ! is_array($invoice)) {
                continue;
            }

            $status = $invoice['marking'] ?? $invoice['status'] ?? $invoice['currentStatus'] ?? null;
            $id     = $invoice['uuid'] ?? $invoice['invoiceUuid'] ?? $invoice['id'] ?? null;
            if ($status === null || ! is_scalar($id)) {
                continue;
            }

            $events[] = [
                'external_id' => (string) $id,
                'invoice_id'  => (string) $id,
                'status'      => $status,
                'message'     => $invoice['notice'] ?? $invoice['statusMessage'] ?? null,
                'updated_at'  => $invoice['updatedAt'] ?? $invoice['updated_at'] ?? null,
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
        if (empty($this->accessToken)) {
            throw new RuntimeException('Missing A-CUBE JWT token.');
        }

        $options['bearer'] = $this->accessToken;

        return $this->http->request($method, $this->buildUrl($endpoint), $options);
    }

    private function setting(string $key): string
    {
        $value = $this->settings[$key] ?? '';
        if ( ! is_string($value) || $value === '') {
            throw new RuntimeException('Missing A-CUBE setting: ' . $key);
        }

        return $value;
    }

    private function buildUrl(string $endpoint): string
    {
        $baseUrl = $this->settings['api_base_url'] ?? '';
        if ( ! is_string($baseUrl) || $baseUrl === '') {
            $baseUrl = ($this->settings['environment'] ?? 'production') === 'sandbox'
                ? 'https://it-sandbox.api.acubeapi.com'
                : 'https://it.api.acubeapi.com';
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    private function normalizeUuid(array &$response): void
    {
        if ( ! empty($response['external_id'])) {
            return;
        }

        $entity = $response['response']['entity'] ?? $response['response'] ?? [];
        if (is_array($entity) && isset($entity['uuid']) && is_scalar($entity['uuid'])) {
            $response['external_id'] = (string) $entity['uuid'];
        }
    }
}
