<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolClient implements IntegrationClientInterface
{
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
}
