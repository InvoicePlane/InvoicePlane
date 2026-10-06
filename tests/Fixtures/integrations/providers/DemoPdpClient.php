<?php

defined('BASEPATH') || exit('No direct script access allowed');

class DemoPdpClient extends AbstractRestProvider
{
    protected static function definition(): array
    {
        return [
            'code'     => 'demopdp',
            'name'     => 'Demo PDP',
            'auth'     => 'bearer',
            'settings' => [
                'access_token'            => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'api_base_url'            => ['default' => 'https://api.demo-pdp.test', 'type' => 'url', 'required' => true],
                'invoice_endpoint'        => ['default' => '/v1/invoices', 'type' => 'path', 'required' => true],
                'invoice_status_endpoint' => ['default' => '/v1/invoices/{id}', 'type' => 'path', 'required' => true],
            ],
        ];
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        $response = $this->request(RequestMethod::POST, $this->buildUrl($this->settings['invoice_endpoint']), ['filename' => basename($documentPath)]);

        return ProviderResponseNormalizer::entity($response, ['invoice', 'data']);
    }

    public function getInvoiceStatus(string $externalId): array
    {
        $url = $this->buildUrl(str_replace('{id}', rawurlencode($externalId), $this->settings['invoice_status_endpoint']));

        return ProviderResponseNormalizer::entity($this->request(RequestMethod::GET, $url), ['invoice', 'data']);
    }

    public function receiveInvoices(array $filters = []): array
    {
        $response                        = $this->request(RequestMethod::GET, $this->buildUrl($this->settings['invoice_endpoint'], $filters));
        $response['response']['invoices'] = IntegrationResponseNormalizer::extractItems($response, ['invoices', 'data']);

        return $response;
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        return ['success' => false, 'message' => 'Demo PDP has no document download.'];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        return ['success' => true, 'response' => ['events' => []]];
    }
}
