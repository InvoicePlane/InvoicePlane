<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolTransmissionEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * GET {transmission_status_endpoint}  →  /v1/transmissions/{id}
     *
     * Response (JSON):
     *   id          string  transmission ID
     *   status      string  queued|processing|delivered|failed
     *   invoice_id  string  related invoice ID
     *   created_at  string  ISO 8601 timestamp
     */
    public function status(string $transmissionId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['transmission_status_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol transmission status endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['transmission_status_endpoint'], $transmissionId);
        $response = $this->client->request(RequestMethod::GET, $url);

        return array_merge($response, ['external_id' => $transmissionId]);
    }

    /**
     * GET {transmissions_endpoint}?{filters}
     *
     * Response (JSON):
     *   data[]  array  list of transmission objects
     *   meta    object pagination metadata
     */
    public function list(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['transmissions_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol transmissions endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['transmissions_endpoint']);

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }
}
