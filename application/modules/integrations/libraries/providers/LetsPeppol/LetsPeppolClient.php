<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolClient implements IntegrationClientInterface
{
    use ProviderPing;

    private LetsPeppolApiClient $apiClient;

    private LetsPeppolInvoiceEndpoint $invoices;

    private LetsPeppolParticipantEndpoint $participants;

    private LetsPeppolCreditNoteEndpoint $creditNotes;

    private LetsPeppolTransmissionEndpoint $transmissions;

    private LetsPeppolDocumentEndpoint $documents;

    public function __construct(?LetsPeppolApiClient $apiClient = null)
    {
        $this->apiClient     = $apiClient ?? new LetsPeppolApiClient();
        $this->invoices      = new LetsPeppolInvoiceEndpoint($this->apiClient);
        $this->participants  = new LetsPeppolParticipantEndpoint($this->apiClient);
        $this->creditNotes   = new LetsPeppolCreditNoteEndpoint($this->apiClient);
        $this->transmissions = new LetsPeppolTransmissionEndpoint($this->apiClient);
        $this->documents     = new LetsPeppolDocumentEndpoint($this->apiClient);
    }

    public static function clientCode(): string
    {
        return 'letspeppol';
    }

    public static function clientName(): string
    {
        return 'LetsPeppol';
    }

    public static function authType(): string
    {
        return 'oauth2';
    }

    public static function defaultSettings(): array
    {
        return [
            'client_id'                    => '',
            'client_secret'                => '',
            'token_url'                    => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url'                 => 'https://api.letspeppol.eu',
            'invoice_endpoint'             => '/v1/invoices',
            'invoice_status_endpoint'      => '/v1/invoices/{id}',
            'incoming_invoices_endpoint'   => '/v1/incoming-invoices',
            'invoice_events_endpoint'      => '/v1/invoice-events',
            'credit_note_endpoint'         => '/v1/credit-notes',
            'credit_note_status_endpoint'  => '/v1/credit-notes/{id}',
            'participants_endpoint'        => '/v1/participants',
            'participant_lookup_endpoint'  => '/v1/participants/{id}',
            'transmissions_endpoint'       => '/v1/transmissions',
            'transmission_status_endpoint' => '/v1/transmissions/{id}',
            'documents_endpoint'           => '/v1/documents',
            'document_endpoint'            => '/v1/documents/{id}',
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
            'token_url' => [
                'type'     => 'url',
                'label'    => 'token_url',
                'required' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
            'invoice_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_endpoint',
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
            'invoice_events_endpoint' => [
                'type'     => 'path',
                'label'    => 'invoice_events_endpoint',
                'required' => true,
            ],
            'credit_note_endpoint' => [
                'type'     => 'path',
                'label'    => 'credit_note_endpoint',
                'required' => true,
            ],
            'credit_note_status_endpoint' => [
                'type'     => 'path',
                'label'    => 'credit_note_status_endpoint',
                'required' => true,
            ],
            'participants_endpoint' => [
                'type'     => 'path',
                'label'    => 'participants_endpoint',
                'required' => true,
            ],
            'participant_lookup_endpoint' => [
                'type'     => 'path',
                'label'    => 'participant_lookup_endpoint',
                'required' => true,
            ],
            'transmissions_endpoint' => [
                'type'     => 'path',
                'label'    => 'transmissions_endpoint',
                'required' => true,
            ],
            'transmission_status_endpoint' => [
                'type'     => 'path',
                'label'    => 'transmission_status_endpoint',
                'required' => true,
            ],
            'documents_endpoint' => [
                'type'     => 'path',
                'label'    => 'documents_endpoint',
                'required' => true,
            ],
            'document_endpoint' => [
                'type'     => 'path',
                'label'    => 'document_endpoint',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        foreach (['client_id', 'client_secret', 'token_url', 'api_base_url'] as $field) {
            if (empty($settings[$field])) {
                throw new RuntimeException('Missing LetsPeppol setting: ' . $field);
            }
        }

        $this->apiClient->configure($settings);
        $this->apiClient->authenticate();

        return true;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        if ( ! file_exists($documentPath)) {
            throw new RuntimeException('Invoice document not found: ' . $documentPath);
        }

        return $this->invoices->send($documentPath, $metadata);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        return $this->invoices->status($externalId);
    }

    public function receiveInvoices(array $filters = []): array
    {
        return $this->invoices->incoming($filters);
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $document = $invoice;
        $encoded  = $this->encodedDocument($document);

        if ($encoded === null) {
            $documentId = $invoice['document_id'] ?? $invoice['id'] ?? null;
            if ( ! is_string($documentId) || $documentId === '') {
                throw new RuntimeException('LetsPeppol incoming invoice has no document ID.');
            }

            $response = $this->documents->get($documentId);
            if (empty($response['success'])) {
                return $response + ['content' => null, 'filename' => null, 'mime_type' => null];
            }

            $document = $response['response']['entity']
                ?? $response['response']['document']
                ?? $response['response']['data']
                ?? [];
            $encoded = $this->encodedDocument($document);
        }

        if ($encoded === null) {
            throw new RuntimeException('LetsPeppol document response has no base64 content.');
        }

        $content = base64_decode($encoded, true);
        if ($content === false) {
            throw new RuntimeException('LetsPeppol incoming document is not valid base64.');
        }

        return [
            'success'   => true,
            'content'   => $content,
            'filename'  => $document['filename'] ?? $document['file_name'] ?? 'letspeppol-invoice.xml',
            'mime_type' => $document['mime_type'] ?? $document['content_type'] ?? 'application/xml',
            'message'   => 'LetsPeppol document decoded.',
            'http_code' => 200,
            'response'  => [],
        ];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        return $this->invoices->events($filters);
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    public function fetchToken(array $settings): string
    {
        $this->apiClient->configure($settings);
        $this->apiClient->authenticate();

        return $this->apiClient->getAccessToken() ?? '';
    }

    public function participants(): LetsPeppolParticipantEndpoint
    {
        return $this->participants;
    }

    public function creditNotes(): LetsPeppolCreditNoteEndpoint
    {
        return $this->creditNotes;
    }

    public function transmissions(): LetsPeppolTransmissionEndpoint
    {
        return $this->transmissions;
    }

    public function documents(): LetsPeppolDocumentEndpoint
    {
        return $this->documents;
    }

    private function encodedDocument(array $document): ?string
    {
        foreach (['content_base64', 'document_content', 'content'] as $key) {
            if (isset($document[$key]) && is_string($document[$key]) && $document[$key] !== '') {
                return $document[$key];
            }
        }

        return null;
    }
}
