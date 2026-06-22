<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolParticipantEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    public function lookup(string $participantId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['participant_lookup_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol participant lookup endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['participant_lookup_endpoint'], $participantId);

        return $this->client->request(RequestMethod::GET, $url);
    }

    public function list(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['participants_endpoint'])) {
            throw new RuntimeException('Missing LetsPeppol participants endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['participants_endpoint']);

        return $this->client->request(RequestMethod::GET, $url, query: $filters);
    }
}
