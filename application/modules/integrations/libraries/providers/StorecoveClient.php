<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Storecove Netherlands (Peppol) provider.
 *
 * Storecove accepts a base64 encoded UBL document in its JSON Parsed mode.
 * Incoming documents and delivery statuses are exposed through its webhook
 * queue, while the original received document can be downloaded by GUID.
 *
 * @see https://www.storecove.com/docs/
 */
class StorecoveClient implements IntegrationClientInterface
{
    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

    public static function clientCode(): string
    {
        return 'storecove';
    }

    public static function clientName(): string
    {
        return 'Storecove Netherlands (Peppol)';
    }

    public static function authType(): string
    {
        return 'bearer';
    }

    public static function defaultSettings(): array
    {
        return [
            'api_key'                       => '',
            'legal_entity_id'               => '',
            'api_base_url'                  => 'https://api.storecove.com/api/v2',
            'document_submissions_endpoint' => '/document_submissions',
            'evidence_endpoint'             => '/document_submissions/{id}/evidence/{type}',
            'webhook_endpoint'              => '/webhook_instances/',
            'received_document_endpoint'    => '/received_documents/{id}/original',
            'recipient_scheme'              => '',
            'recipient_id'                  => '',
            'country_code'                  => 'NL',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'api_key'                       => ['type' => 'password', 'label' => 'api_key', 'required' => true, 'sensitive' => true],
            'legal_entity_id'               => ['type' => 'text', 'label' => 'legal_entity_id', 'required' => true],
            'api_base_url'                  => ['type' => 'url', 'label' => 'api_base_url', 'required' => true],
            'document_submissions_endpoint' => ['type' => 'path', 'label' => 'document_submissions_endpoint', 'required' => true],
            'evidence_endpoint'             => ['type' => 'path', 'label' => 'evidence_endpoint', 'required' => true],
            'webhook_endpoint'              => ['type' => 'path', 'label' => 'webhook_endpoint', 'required' => true],
            'received_document_endpoint'    => ['type' => 'path', 'label' => 'received_document_endpoint', 'required' => true],
            'recipient_scheme'              => ['type' => 'text', 'label' => 'recipient_scheme'],
            'recipient_id'                  => ['type' => 'text', 'label' => 'recipient_id'],
            'country_code'                  => ['type' => 'text', 'label' => 'country_code', 'required' => true],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = array_replace(self::defaultSettings(), $settings);
        foreach (['api_key', 'legal_entity_id', 'api_base_url'] as $field) {
            if ((string) $this->settings[$field] === '') {
                throw new RuntimeException('Missing Storecove setting: ' . $field);
            }
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        return (string) ($settings['api_key'] ?? '');
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }
        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new RuntimeException('Storecove Peppol requires a UBL XML document.');
        }
        $xml = file_get_contents($documentPath);
        if ($xml === false || trim($xml) === '') {
            throw new RuntimeException('Unable to read the Storecove invoice XML document.');
        }

        $routing = $this->routing($metadata);
        $payload = [
            'legalEntityId' => (int) $this->setting('legal_entity_id'),
            'document'      => [
                'documentType'    => 'invoice',
                'rawDocumentData' => [
                    'document'      => base64_encode($xml),
                    'parseStrategy' => 'ubl',
                ],
            ],
        ];
        if ($routing !== []) {
            $payload['routing'] = $routing;
        }
        if ( ! empty($metadata['idempotency_guid'])) {
            $payload['idempotencyGuid'] = (string) $metadata['idempotency_guid'];
        }

        $response = ProviderResponseNormalizer::entity(
            $this->request(RequestMethod::POST, $this->setting('document_submissions_endpoint'), ['json' => $payload]),
            ['document_submission', 'submission', 'data']
        );
        $entity = $response['response']['entity'] ?? [];
        $guid   = is_array($entity) ? ($entity['guid'] ?? $entity['document_submission_guid'] ?? null) : null;
        if (is_scalar($guid)) {
            $response['external_id'] = (string) $guid;
        }
        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'Storecove accepted the document but returned no submission GUID.';
        }
        $response['request']['invoice_id'] = $metadata['invoice_id'] ?? null;

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $endpoint                = str_replace(['{id}', '{type}'], [rawurlencode($externalId), 'sending'], $this->setting('evidence_endpoint'));
        $response                = ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $endpoint), ['evidence', 'data']);
        $response['external_id'] = $externalId;

        return $response;
    }

    public function receiveInvoices(array $filters = []): array
    {
        $transport = $this->request(RequestMethod::GET, $this->setting('webhook_endpoint'));
        if (($transport['http_code'] ?? 0) === 204 || empty($transport['response'])) {
            $transport['response']['invoices'] = [];

            return $transport;
        }
        $event = $transport['response'];
        if (isset($event['payload']) && is_array($event['payload'])) {
            $event = array_replace($event, $event['payload']);
        }
        $type = $event['event_type'] ?? $event['eventType'] ?? '';
        if ($type !== 'received_document' && $type !== 'received_invoice') {
            $transport['response']['invoices'] = [];

            return $transport;
        }
        $guid    = $event['document_guid'] ?? $event['documentGuid'] ?? $event['guid'] ?? null;
        $invoice = $event;
        if (is_scalar($guid)) {
            $invoice['external_id'] = (string) $guid;
        }
        $transport['response']['invoices'] = [$invoice];

        return $transport;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $id = $invoice['document_guid'] ?? $invoice['documentGuid'] ?? $invoice['external_id'] ?? $invoice['guid'] ?? null;
        if ( ! is_scalar($id) || (string) $id === '') {
            throw new RuntimeException('Storecove incoming document has no GUID.');
        }
        $endpoint = str_replace('{id}', rawurlencode((string) $id), $this->setting('received_document_endpoint'));
        $download = $this->request(RequestMethod::GET, $endpoint, ['binary' => true, 'max_response_bytes' => 15 * 1024 * 1024, 'headers' => ['Accept: application/xml']]);

        return [
            'success'   => $download['success'], 'content' => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? ('storecove-' . $id . '.xml'),
            'mime_type' => $download['content_type'] ?? 'application/xml', 'message' => $download['message'] ?? null,
            'http_code' => $download['http_code'] ?? null, 'response' => ['document_guid' => (string) $id],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $transport = $this->request(RequestMethod::GET, $this->setting('webhook_endpoint'));
        $event     = $transport['response'] ?? [];
        if ( ! is_array($event) || ($event['event_type'] ?? '') !== 'document_submission') {
            $transport['response']['events'] = [];

            return $transport;
        }
        $id                              = $event['guid'] ?? $event['idempotencyGuid'] ?? null;
        $transport['response']['events'] = [[
            'invoice_id' => $event['idempotencyGuid'] ?? $id, 'external_id' => $id,
            'status'     => $event['event'] ?? $event['status'] ?? null, 'message' => $event['details'] ?? null,
        ]];

        return $transport;
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        $metadata['invoice_number'] = $invoice->invoice_number ?? null;

        return $metadata;
    }

    private function routing(array $metadata): array
    {
        $scheme = $metadata['recipient_scheme'] ?? $this->setting('recipient_scheme');
        $id     = $metadata['recipient_id'] ?? $metadata['peppol_participant_id'] ?? $metadata['client_peppol_id'] ?? $this->setting('recipient_id');
        if ((string) $scheme === '' || (string) $id === '') {
            return [];
        }

        return ['eIdentifiers' => [['scheme' => (string) $scheme, 'id' => (string) $id]]];
    }

    private function request(RequestMethod $method, string $endpoint, array $options = []): array
    {
        $options['bearer'] = $this->setting('api_key');

        return $this->http->request($method, rtrim($this->setting('api_base_url'), '/') . '/' . ltrim($endpoint, '/'), $options);
    }

    private function setting(string $key): string
    {
        return (string) ($this->settings[$key] ?? '');
    }
}
