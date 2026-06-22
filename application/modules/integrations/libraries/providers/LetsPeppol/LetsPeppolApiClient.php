<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/LetsPeppolHttpClientInterface.php';
require_once __DIR__ . '/LetsPeppolCurlHttpClient.php';

class LetsPeppolApiClient
{
    private ?string $accessToken = null;
    private array $settings = [];
    private LetsPeppolHttpClientInterface $http;

    public function __construct(?LetsPeppolHttpClientInterface $http = null)
    {
        $this->http = $http ?? new LetsPeppolCurlHttpClient();
    }

    public function configure(array $settings): void
    {
        $this->settings = $settings;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function authenticate(): void
    {
        $decoded = $this->http->fetchToken(
            $this->settings['token_url'],
            $this->settings['client_id'],
            $this->settings['client_secret']
        );

        if (empty($decoded['access_token'])) {
            throw new RuntimeException('LetsPeppol OAuth failed: no access_token in response.');
        }

        $this->accessToken = $decoded['access_token'];
    }

    public function buildUrl(string $endpoint, ?string $id = null): string
    {
        $path = $id !== null ? str_replace('{id}', urlencode($id), $endpoint) : $endpoint;

        return rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($path, '/');
    }

    public function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $query = []
    ): array {
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $this->http->send($method, $url, $payload, $multipart, $this->accessToken);
    }
}
