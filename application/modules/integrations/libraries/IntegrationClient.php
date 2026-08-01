<?php

defined('BASEPATH') || exit('No direct script access allowed');

class IntegrationClient
{
    private bool $authenticated = false;

    private IntegrationClientInterface $provider;

    private array $settings;

    public function __construct(IntegrationClientInterface $provider, array $settings)
    {
        $this->provider = $provider;
        $this->settings = array_replace($provider::defaultSettings(), $settings);
    }

    public function authenticate(): bool
    {
        if ( ! $this->authenticated) {
            $this->authenticated = $this->provider->authenticate($this->settings);
        }

        return $this->authenticated;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        $this->ensureAuthenticated();

        return $this->provider->sendInvoice($documentPath, $metadata);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $this->ensureAuthenticated();

        return $this->provider->getInvoiceStatus($externalId);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $this->ensureAuthenticated();

        return $this->provider->receiveInvoices($filters);
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        $this->ensureAuthenticated();

        return $this->provider->downloadInvoiceDocument($invoice);
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $this->ensureAuthenticated();

        return $this->provider->getInvoiceEvents($filters);
    }

    public function lookupParticipant(string $participantId): array
    {
        if ( ! method_exists($this->provider, 'participants')) {
            return ['success' => false, 'response' => ['reachable' => false], 'message' => 'Provider does not support participant lookup'];
        }
        $this->ensureAuthenticated();

        return $this->provider->participants()->lookup($participantId);
    }

    private function ensureAuthenticated(): void
    {
        if ( ! $this->authenticate()) {
            throw new RuntimeException('E-invoicing provider authentication failed.');
        }
    }
}
