<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'modules/pdp/libraries/PdpProviderRegistry.php';

class PdpClient
{
    private $provider;

    public function __construct(array $settings = array())
    {
        $providerName = strtolower($settings['provider'] ?? 'superpdp');
        $registry = new PdpProviderRegistry();
        $this->provider = $registry->make($providerName);
        $this->provider->authenticate($settings);
    }

    public function sendInvoice(string $filePath, array $invoiceData): array
    {
        return $this->provider->sendInvoice($filePath, $invoiceData);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        return $this->provider->getInvoiceStatus($externalId);
    }

    public function receiveInvoices(array $filters = array()): array
    {
        return $this->provider->receiveInvoices($filters);
    }
}
