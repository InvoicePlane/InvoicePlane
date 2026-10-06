<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolClient extends AbstractRestProvider
{
    private LetsPeppolApiClient $apiClient;

    private LetsPeppolInvoiceEndpoint $invoices;

    private LetsPeppolParticipantEndpoint $participants;

    private LetsPeppolCreditNoteEndpoint $creditNotes;

    private LetsPeppolTransmissionEndpoint $transmissions;

    private LetsPeppolDocumentEndpoint $documents;

    public function __construct(?LetsPeppolApiClient $apiClient = null)
    {
        // HTTP goes through LetsPeppolApiClient, so the base transport is not built.
        $this->apiClient     = $apiClient ?? new LetsPeppolApiClient();
        $this->invoices      = new LetsPeppolInvoiceEndpoint($this->apiClient);
        $this->participants  = new LetsPeppolParticipantEndpoint($this->apiClient);
        $this->creditNotes   = new LetsPeppolCreditNoteEndpoint($this->apiClient);
        $this->transmissions = new LetsPeppolTransmissionEndpoint($this->apiClient);
        $this->documents     = new LetsPeppolDocumentEndpoint($this->apiClient);
    }

    public function authenticate(array $settings): bool
    {
        $this->requireSettings($settings, ['client_id', 'client_secret', 'token_url', 'api_base_url']);

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

    protected static function definition(): array
    {
        return [
            'code'     => 'letspeppol',
            'name'     => 'LetsPeppol',
            'auth'     => 'oauth2',
            'settings' => [
                'client_id'                    => ['type' => 'text', 'required' => true],
                'client_secret'                => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'token_url'                    => ['default' => 'https://api.letspeppol.eu/oauth2/token', 'type' => 'url', 'required' => true],
                'api_base_url'                 => ['default' => 'https://api.letspeppol.eu', 'type' => 'url', 'required' => true],
                'invoice_endpoint'             => ['default' => '/v1/invoices', 'type' => 'path', 'required' => true],
                'invoice_status_endpoint'      => ['default' => '/v1/invoices/{id}', 'type' => 'path', 'required' => true],
                'incoming_invoices_endpoint'   => ['default' => '/v1/incoming-invoices', 'type' => 'path', 'required' => true],
                'invoice_events_endpoint'      => ['default' => '/v1/invoice-events', 'type' => 'path', 'required' => true],
                'credit_note_endpoint'         => ['default' => '/v1/credit-notes', 'type' => 'path', 'required' => true],
                'credit_note_status_endpoint'  => ['default' => '/v1/credit-notes/{id}', 'type' => 'path', 'required' => true],
                'participants_endpoint'        => ['default' => '/v1/participants', 'type' => 'path', 'required' => true],
                'participant_lookup_endpoint'  => ['default' => '/v1/participants/{id}', 'type' => 'path', 'required' => true],
                'transmissions_endpoint'       => ['default' => '/v1/transmissions', 'type' => 'path', 'required' => true],
                'transmission_status_endpoint' => ['default' => '/v1/transmissions/{id}', 'type' => 'path', 'required' => true],
                'documents_endpoint'           => ['default' => '/v1/documents', 'type' => 'path', 'required' => true],
                'document_endpoint'            => ['default' => '/v1/documents/{id}', 'type' => 'path', 'required' => true],
            ],
        ];
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
