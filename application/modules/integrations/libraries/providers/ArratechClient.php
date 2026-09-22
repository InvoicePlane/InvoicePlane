<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Arratech Swedish Peppol provider.
 *
 * Arratech's transaction endpoint accepts a Peppol Standard Business Document
 * (SBD) and returns a transaction ID that can be polled until COMPLETED. The
 * InvoicePlane UBL document is wrapped in an SBDH envelope before submission.
 *
 * @see https://docs.arratech.com/tutorials/sendtransaction
 */
class ArratechClient implements IntegrationClientInterface
{
    use ProviderPing;

    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? new CurlApiClient();
    }

    public static function clientCode(): string
    {
        return 'arratech';
    }

    public static function clientName(): string
    {
        return 'Arratech Sweden (Peppol)';
    }

    public static function authType(): string
    {
        return 'api_key';
    }

    public static function defaultSettings(): array
    {
        return [
            'api_key'                    => '',
            'org_id'                     => '',
            'access_point_id'            => '',
            'api_base_url'               => 'https://api.arratech.com',
            'outgoing_endpoint'          => '/orgs/{org}/transactions',
            'outgoing_status_endpoint'   => '/orgs/{org}/transactions/{id}',
            'incoming_endpoint'          => '/orgs/{org}/transactions/from_network',
            'incoming_document_endpoint' => '/orgs/{org}/transactions/{id}/business_document',
            'events_endpoint'            => '/orgs/{org}/transactions/to_network',
            'country_code'               => 'SE',
            'wrap_sbd'                   => true,
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
            'org_id' => [
                'type'     => 'text',
                'label'    => 'org_id',
                'required' => true,
            ],
            'access_point_id' => [
                'type'     => 'text',
                'label'    => 'access_point_id',
                'required' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
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
            'events_endpoint' => [
                'type'     => 'path',
                'label'    => 'events_endpoint',
                'required' => true,
            ],
            'country_code' => [
                'type'     => 'text',
                'label'    => 'country_code',
                'required' => true,
            ],
            'wrap_sbd' => [
                'type'  => 'checkbox',
                'label' => 'wrap_sbd',
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = array_replace(self::defaultSettings(), $settings);

        foreach (['api_key', 'org_id', 'access_point_id', 'api_base_url'] as $field) {
            if (empty($this->settings[$field])) {
                throw new RuntimeException('Missing Arratech setting: ' . $field);
            }
        }

        return true;
    }

    public function fetchToken(array $settings): string
    {
        // Arratech API keys are sent directly in X-Api-Key.
        return (string) ($settings['api_key'] ?? '');
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! is_file($documentPath) || ! is_readable($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        if (mb_strtolower(pathinfo($documentPath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new RuntimeException('Arratech Peppol requires a UBL XML document.');
        }

        $xml = file_get_contents($documentPath);
        if ($xml === false || trim($xml) === '') {
            throw new RuntimeException('Unable to read the Arratech invoice XML document.');
        }

        if ( ! empty($this->settings['wrap_sbd'])) {
            $xml = $this->wrapStandardBusinessDocument($xml, $metadata);
        }

        $endpoint = str_replace('{org}', rawurlencode($this->setting('org_id')), $this->setting('outgoing_endpoint'));
        $response = ProviderResponseNormalizer::entity(
            $this->request(RequestMethod::POST, $endpoint, [
                'body'    => $xml,
                'headers' => ['Content-Type: application/xml'],
                'query'   => ['ap' => $this->setting('access_point_id')],
            ]),
            ['transaction', 'data']
        );
        $this->normalizeTransaction($response);

        if ( ! empty($response['success']) && empty($response['external_id'])) {
            $response['success'] = false;
            $response['status']  = 'error';
            $response['message'] = 'Arratech accepted the document but returned no transaction ID.';
        }

        $response['request']['invoice_id'] = $metadata['invoice_id'] ?? null;

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $endpoint = str_replace(
            ['{org}', '{id}'],
            [rawurlencode($this->setting('org_id')), rawurlencode($externalId)],
            $this->setting('outgoing_status_endpoint')
        );
        $response = ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $endpoint), ['transaction', 'data']);
        $this->normalizeTransaction($response);

        return array_merge($response, ['external_id' => $externalId]);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $endpoint = str_replace('{org}', rawurlencode($this->setting('org_id')), $this->setting('incoming_endpoint'));
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $endpoint, ['query' => $filters]),
            ['transactions', 'items', 'data'],
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
        $externalId = $invoice['id'] ?? $invoice['transaction_id'] ?? $invoice['external_id'] ?? null;
        if ( ! is_scalar($externalId) || (string) $externalId === '') {
            throw new RuntimeException('Arratech incoming transaction has no transaction ID.');
        }

        $endpoint = str_replace(
            ['{org}', '{id}'],
            [rawurlencode($this->setting('org_id')), rawurlencode((string) $externalId)],
            $this->setting('incoming_document_endpoint')
        );
        $download = $this->request(RequestMethod::GET, $endpoint, [
            'binary'             => true,
            'max_response_bytes' => 15 * 1024 * 1024,
            'headers'            => ['Accept: application/xml'],
        ]);

        return [
            'success'   => $download['success'],
            'content'   => $download['body'] ?? null,
            'filename'  => $invoice['filename'] ?? ('arratech-' . $externalId . '.xml'),
            'mime_type' => $download['content_type'] ?? 'application/xml',
            'message'   => $download['message'],
            'http_code' => $download['http_code'],
            'response'  => ['transaction_id' => (string) $externalId],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $endpoint = str_replace('{org}', rawurlencode($this->setting('org_id')), $this->setting('events_endpoint'));
        $response = ProviderResponseNormalizer::collection(
            $this->request(RequestMethod::GET, $endpoint, ['query' => $filters]),
            ['transactions', 'items', 'data'],
            'events'
        );

        $events = [];
        foreach ($response['response']['events'] ?? [] as $transaction) {
            if ( ! is_array($transaction) || ! isset($transaction['id'])) {
                continue;
            }

            $events[] = [
                'invoice_id'  => (string) $transaction['id'],
                'external_id' => (string) $transaction['id'],
                'status'      => $transaction['transactionStatus'] ?? $transaction['status'] ?? null,
                'message'     => $transaction['message'] ?? $transaction['error'] ?? null,
                'updated_at'  => $transaction['updatedAt'] ?? $transaction['updated_at'] ?? null,
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
        $options['headers'] = array_merge(['X-Api-Key: ' . $this->setting('api_key')], $options['headers'] ?? []);

        return $this->http->request(
            $method,
            rtrim($this->setting('api_base_url'), '/') . '/' . ltrim($endpoint, '/'),
            $options
        );
    }

    private function normalizeTransaction(array &$response): void
    {
        $entity = $response['response']['entity'] ?? $response['response'] ?? [];
        if ( ! is_array($entity)) {
            return;
        }

        $id = $entity['id'] ?? $entity['transactionId'] ?? $entity['transaction_id'] ?? null;
        if (is_scalar($id)) {
            $response['external_id'] = (string) $id;
        }

        $status = $entity['transactionStatus'] ?? $entity['status'] ?? null;
        if (is_scalar($status)) {
            $response['status'] = (string) $status;
        }
    }

    private function wrapStandardBusinessDocument(string $xml, array $metadata): string
    {
        $source                     = new DOMDocument();
        $source->preserveWhiteSpace = false;
        if ( ! @$source->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw new RuntimeException('Arratech document is not valid XML.');
        }

        if (mb_strtolower((string) $source->documentElement?->localName) === 'standardbusinessdocument') {
            return $xml;
        }

        $xpath = new DOMXPath($source);
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $endpoints   = $xpath->query('//cac:AccountingSupplierParty//cbc:EndpointID | //cac:AccountingCustomerParty//cbc:EndpointID');
        $identifiers = [];
        foreach ($endpoints ?: [] as $node) {
            $identifiers[] = [
                'authority' => $node->attributes?->getNamedItem('schemeID')?->nodeValue ?: 'iso6523-actorid-upis',
                'value'     => trim($node->textContent),
            ];
        }

        if (count($identifiers) < 2) {
            throw new RuntimeException('Arratech requires supplier and customer Peppol EndpointID values.');
        }

        $customization = trim((string) $xpath->evaluate('string(/*[local-name()="Invoice"]/*[local-name()="CustomizationID"])'));
        $profile       = trim((string) $xpath->evaluate('string(/*[local-name()="Invoice"]/*[local-name()="ProfileID"])'));
        $instance      = 'invoice-' . (string) ($metadata['invoice_id'] ?? bin2hex(random_bytes(8)));
        $sbdNamespace  = 'http://www.unece.org/cefact/namespaces/StandardBusinessDocumentHeader';

        $wrapped               = new DOMDocument('1.0', 'UTF-8');
        $wrapped->formatOutput = false;
        $root                  = $wrapped->createElementNS($sbdNamespace, 'StandardBusinessDocument');
        $wrapped->appendChild($root);
        $sbdh = $wrapped->createElement('StandardBusinessDocumentHeader');
        $root->appendChild($sbdh);
        $sbdh->appendChild($wrapped->createElement('HeaderVersion', '1.0'));

        foreach (['Sender' => $identifiers[0], 'Receiver' => $identifiers[1]] as $name => $identifier) {
            $party = $wrapped->createElement($name);
            $party->appendChild($this->sbdIdentifier($wrapped, $identifier));
            $sbdh->appendChild($party);
        }

        $documentIdentification = $wrapped->createElement('DocumentIdentification');
        $documentIdentification->appendChild($wrapped->createElement('Standard', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'));
        $documentIdentification->appendChild($wrapped->createElement('TypeVersion', '2.1'));
        $documentIdentification->appendChild($wrapped->createElement('InstanceIdentifier', $instance));
        $documentIdentification->appendChild($wrapped->createElement('Type', 'Invoice'));
        $documentIdentification->appendChild($wrapped->createElement('CreationDateAndTime', gmdate('Y-m-d\TH:i:s\Z')));
        $sbdh->appendChild($documentIdentification);

        $scopeDocument = $wrapped->createElement('Scope');
        $scopeDocument->appendChild($wrapped->createElement('Type', 'DOCUMENTID'));
        $scopeDocument->appendChild($wrapped->createElement('InstanceIdentifier', $customization !== '' ? 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##' . $customization . '::2.1' : 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1'));
        $scopeDocument->appendChild($wrapped->createElement('Identifier', 'busdox-docid-qns'));
        $businessScope = $wrapped->createElement('BusinessScope');
        $businessScope->appendChild($scopeDocument);
        $scopeProcess = $wrapped->createElement('Scope');
        $scopeProcess->appendChild($wrapped->createElement('Type', 'PROCESSID'));
        $scopeProcess->appendChild($wrapped->createElement('InstanceIdentifier', $profile !== '' ? $profile : 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0'));
        $scopeProcess->appendChild($wrapped->createElement('Identifier', 'cenbii-procid-ubl'));
        $businessScope->appendChild($scopeProcess);
        $scopeCountry = $wrapped->createElement('Scope');
        $scopeCountry->appendChild($wrapped->createElement('Type', 'COUNTRY_C1'));
        $scopeCountry->appendChild($wrapped->createElement('InstanceIdentifier', $this->setting('country_code')));
        $scopeCountry->appendChild($wrapped->createElement('Identifier', 'iso3166-1'));
        $businessScope->appendChild($scopeCountry);
        $sbdh->appendChild($businessScope);

        $root->appendChild($wrapped->importNode($source->documentElement, true));

        return $wrapped->saveXML() ?: '';
    }

    private function sbdIdentifier(DOMDocument $document, array $identifier): DOMElement
    {
        $node = $document->createElement('Identifier', $identifier['value']);
        $node->setAttribute('Authority', $identifier['authority']);

        return $node;
    }

    private function setting(string $name): string
    {
        $value = $this->settings[$name] ?? '';
        if ( ! is_string($value) || trim($value) === '') {
            throw new RuntimeException('Missing Arratech setting: ' . $name);
        }

        return trim($value);
    }
}
