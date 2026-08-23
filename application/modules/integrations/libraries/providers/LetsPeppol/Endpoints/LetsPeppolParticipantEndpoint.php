<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolParticipantEndpoint
{
    public function __construct(private LetsPeppolApiClient $client) {}

    /**
     * GET {participant_lookup_endpoint}  →  /v1/participants/{id}.
     *
     * Response (JSON):
     *   id          string  participant identifier (e.g. "0088:1234567890")
     *   name        string  registered company name
     *   country     string  ISO 3166-1 alpha-2 country code
     *   reachable   bool    whether participant can receive PEPPOL documents
     */
    public function lookup(string $participantId): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['participant_lookup_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol participant lookup endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['participant_lookup_endpoint'], $participantId);

        return ProviderResponseNormalizer::entity(
            $this->client->request(RequestMethod::GET, $url),
            ['participant', 'data']
        );
    }

    /**
     * GET {participants_endpoint}?{filters}.
     *
     * Response (JSON):
     *   data[]  array  list of participant objects
     *   meta    object pagination metadata
     */
    public function list(array $filters = []): array
    {
        $settings = $this->client->getSettings();

        if (empty($settings['participants_endpoint'])) {
            throw new \RuntimeException('Missing LetsPeppol participants endpoint configuration.');
        }

        $url = $this->client->buildUrl($settings['participants_endpoint']);

        return ProviderResponseNormalizer::collection(
            $this->client->request(RequestMethod::GET, $url, query: $filters),
            ['participants', 'items', 'data'],
            'participants'
        );
    }
}
