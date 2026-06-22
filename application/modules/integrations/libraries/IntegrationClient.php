<?php

defined('BASEPATH') or exit('No direct script access allowed');

class IntegrationClient
{
    private IntegrationClientInterface $provider;
    private array $settings;

    public function __construct(IntegrationClientInterface $provider, array $settings)
    {
        $this->provider = $provider;
        $this->settings = $settings;
    }

    public function authenticate(): bool
    {
        return $this->provider->authenticate($this->settings);
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        $this->authenticate();
        return $this->provider->sendInvoice($documentPath, $metadata);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $this->authenticate();
        return $this->provider->getInvoiceStatus($externalId);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $this->authenticate();
        return $this->provider->receiveInvoices($filters);
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        $this->authenticate();
        return $this->provider->getInvoiceEvents($filters);
    }
}
