<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Dokapi Belgian Peppol provider.
 *
 * Dokapi uses OAuth2 client credentials and exchanges Peppol BIS Billing 3.0
 * documents. The API contract is subscription-dependent, therefore endpoint
 * paths remain configurable while the documented staging defaults are used.
 *
 * @see https://www.dokapi.io/our-apis
 */
class DokapiClient implements IntegrationClientInterface
{
    private ?string $accessToken = null;

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
        return 'dokapi';
    }

    public static function clientName(): string
    {
        return 'Dokapi (Belgium Peppol)';
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
            'access_token'               => '',
            'api_base_url'               => 'https://peppol-api.dokapi-stg.io/v1',
            'token_url'                  => 'https://dev-portal.dokapi.io/api/oauth2/token',
            'outgoing_endpoint'          => '/outgoing-documents',
            'outgoing_status_endpoint'   => '/outgoing-documents/{id}',
            'incoming_endpoint'          => '/incoming-documents',
            'incoming_document_endpoint' => '/incoming-documents/{id}',
            'document_field'             => 'file',
            'metadata_field'             => 'metadata',
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
            'access_token' => [
                'type'      => 'password',
                'label'     => 'access_token_optional',
                'sensitive' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
            'token_url' => [
                'type'     => 'url',
                'label'    => 'token_url',
                'required' => true,
            ],
            'outgoing_endpoint' => [
                'type'     => 'path',
                'label'    => 'outgoing_endpoint',
                'required' => true,
            ],
            'outgoing_status_endpoint' => [
                'type'     => 'path',
                'label'    => 'outgoing_status_endpoint',
                'required' => true,
            ],
            'incoming_endpoint' => [
                'type'     => 'path',
                'label'    => 'incoming_endpoint',
                'required' => true,
            ],
            'incoming_document_endpoint' => [
                'type'     => 'path',
                'label'    => 'incoming_document_endpoint',
                'required' => true,
            ],
            'document_field' => [
                'type'     => 'text',
                'label'    => 'document_field',
                'required' => true,
            ],
            'metadata_field' => [
                'type'     => 'text',
                'label'    => 'metadata_field',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = array_replace(self::defaultSettings(), $settings);

        if ( ! empty($this->settings['access_token'])) {
            $this->accessToken = (string) $this->settings['access_token'];

            return true;
        }

        foreach (['client_id', 'client_secret', 'token_url'] as $field) {
            if (empty($this->settings[$field])) {
                throw new RuntimeException('Missing Dokapi setting: ' . $field);
            }
        }

        $this->accessToken = $this->fetchToken($this->settings);
        if ($this->accessToken === '') {
            throw new RuntimeException('Dokapi OAuth2 authentication returned no access token.');
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        $response = $this->http->request(RequestMethod::POST, (string) ($settings['token_url'] ?? ''), [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => (string) ($settings['client_id'] ?? ''),
                'client_secret' => (string) ($settings['client_secret'] ?? ''),
            ],
        ]);

        if (empty($response['success'])) {
            throw new RuntimeException('Dokapi OAuth2 error: ' . ($response['message'] ?? 'unknown error'));
        }

        $token = $response['response']['access_token'] ?? $response['response']['token'] ?? null;

        return is_string($token) ? $token : '';
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new RuntimeException('Dokapi Peppol requires a UBL 2.1 XML document.');
        }

        $xml = file_get_contents($documentPath);
        if ($xml === false || trim($xml) === '') {
            throw new RuntimeException('Unable to read the Dokapi invoice XML document.');
        }

        $documentPayload = $this->documentMetadata($xml, $metadata);
        $payload         = [
            $this->setting('document_field') => new CURLFile(
                $documentPath,
                'application/xml',
                basename($documentPath)
            ),
            $this->setting('metadata_field') => json_encode($documentPayload, JSON_THROW_ON_ERROR),
        ];

        $response = ProviderResponseNormalizer::entity(
            $this->request(RequestMethod::POST, $this->setting('outgoing_endpoint'), [
                'multipart' => $payload,
            ]),
            ['document', 'outgoing_document', 'data']
        );
        $this->normalizeIdentifier($response);

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'Dokapi accepted the document but returned no ULID.';
        }

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->setting('outgoing_status_endpoint'));
        $response = ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $endpoint), ['document', 'outgoing_document', 'data']);
        $this->normalizeIdentifier($response);

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $this->setting('incoming_endpoint'), ['query' => $filters]),
            ['documents', 'incoming_documents', 'items', 'data'],
            'invoices'
        );

        foreach ($response['response']['invoices'] ?? [] as &$invoice) {
            if ( ! is_array($invoice)) {
                continue;
            }

            $id = $invoice['ulid'] ?? $invoice['id'] ?? $invoice['document_id'] ?? null;
            if (is_scalar($id)) {
                $invoice['external_id'] = (string) $id;
            }
        }
        unset($invoice);

        return $response;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $externalId = $invoice['ulid'] ?? $invoice['document_id'] ?? $invoice['id'] ?? $invoice['external_id'] ?? null;
        if ( ! is_string($externalId) || $externalId === '') {
            throw new RuntimeException('Dokapi incoming document has no ULID.');
        }

        $endpoint = str_replace('{id}', rawurlencode($externalId), $this->setting('incoming_document_endpoint'));
        $document = $this->request(RequestMethod::GET, $endpoint);
        $url      = $this->firstScalar($document['response'] ?? [], ['downloadUrl', 'download_url', 'presignedUrl', 'presigned_url', 'url']);

        if ($url !== null) {
            $resolved = $this->urlGuard->validateAndResolve($url);
            $download = $this->http->request(RequestMethod::GET, $url, [
                'binary'             => true,
                'max_response_bytes' => 15 * 1024 * 1024,
                'resolve'            => [$resolved['host'], $resolved['port'], $resolved['ip']],
            ]);
        } else {
            $download = $this->request(RequestMethod::GET, $endpoint, [
                'binary'             => true,
                'max_response_bytes' => 15 * 1024 * 1024,
                'headers'            => ['Accept: application/xml'],
            ]);
        }

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? $invoice['fileName'] ?? ('dokapi-' . $externalId . '.xml'),
            'mime_type' => $download['content_type'] ?? 'application/xml',
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['document_id' => $externalId],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $this->setting('outgoing_endpoint'), ['query' => $filters]),
            ['documents', 'outgoing_documents', 'items', 'data'],
            'events'
        );

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
            throw new RuntimeException('Missing Dokapi access token.');
        }

        $options['bearer'] = $this->accessToken;

        return $this->http->request($method, rtrim($this->setting('api_base_url'), '/') . '/' . ltrim($endpoint, '/'), $options);
    }

    private function documentMetadata(string $xml, array $metadata): array
    {
        $dom                     = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        if ( ! @$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw new RuntimeException('Dokapi document is not valid XML.');
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');

        $endpointIds = $xpath->query('//cac:AccountingSupplierParty//cbc:EndpointID | //cac:AccountingCustomerParty//cbc:EndpointID');
        $ids         = [];
        foreach ($endpointIds ?: [] as $node) {
            $ids[] = [
                'scheme' => $node->attributes?->getNamedItem('schemeID')?->nodeValue ?: 'iso6523-actorid-upis',
                'value'  => trim($node->textContent),
            ];
        }

        $customization = trim((string) ($xpath->evaluate('string(/*[local-name()="Invoice"]/*[local-name()="CustomizationID"])') ?: ''));
        $profile       = trim((string) ($xpath->evaluate('string(/*[local-name()="Invoice"]/*[local-name()="ProfileID"])') ?: ''));

        $payload = [
            'sender'                 => $ids[0] ?? null,
            'receiver'               => $ids[1] ?? null,
            'c1CountryCode'          => 'BE',
            'documentTypeIdentifier' => [
                'scheme' => 'busdox-docid-qns',
                'value'  => $customization !== ''
                    ? 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##' . $customization . '::2.1'
                    : 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1',
            ],
            'processIdentifier' => [
                'scheme' => 'cenbii-procid-ubl',
                'value'  => $profile !== '' ? $profile : 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
            ],
            'externalReference' => (string) ($metadata['invoice_id'] ?? ''),
        ];

        if ($payload['sender'] === null || $payload['receiver'] === null) {
            throw new RuntimeException('Dokapi requires supplier and customer Peppol EndpointID values in the UBL document.');
        }

        return $payload;
    }

    private function normalizeIdentifier(array &$response): void
    {
        $entity = $response['response']['entity'] ?? $response['response'] ?? [];
        if ( ! is_array($entity)) {
            return;
        }

        $id = $entity['ulid'] ?? $entity['document_id'] ?? $entity['id'] ?? null;
        if (is_scalar($id)) {
            $response['external_id'] = (string) $id;
        }

        $status = $entity['status'] ?? $entity['state'] ?? null;
        if (is_scalar($status)) {
            $response['status'] = (string) $status;
        }
    }

    private function setting(string $name): string
    {
        $value = $this->settings[$name] ?? '';
        if ( ! is_string($value) || trim($value) === '') {
            throw new RuntimeException('Missing Dokapi setting: ' . $name);
        }

        return trim($value);
    }

    private function firstScalar(array $payload, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key])) {
                return (string) $payload[$key];
            }
        }

        foreach ($payload as $value) {
            if (is_array($value)) {
                $found = $this->firstScalar($value, $keys);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }
}
