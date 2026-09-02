<?php

defined('BASEPATH') || exit('No direct script access allowed');

class LetsPeppolApiClient
{
    private ?string $accessToken = null;

    private array $settings = [];

    private ApiClientInterface $http;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? IntegrationTransport::httpClient() ?? new GuzzleApiClient();
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

    /**
     * Obtain a bearer token from the OAuth2 client-credentials endpoint.
     *
     * Request (form-encoded POST to token_url):
     *   grant_type    client_credentials
     *   client_id     {settings.client_id}
     *   client_secret {settings.client_secret}
     *   scope         openid
     *
     * Response (JSON):
     *   access_token  string  Bearer token for subsequent API calls
     *   token_type    string  "Bearer"
     *   expires_in    int     Seconds until expiry
     */
    public function authenticate(): void
    {
        $result = $this->http->request(RequestMethod::POST, $this->settings['token_url'], [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'scope'         => 'openid',
            ],
        ]);

        if (empty($result['success'])) {
            throw new \RuntimeException('LetsPeppol OAuth request failed: ' . ($result['message'] ?? 'unknown error'));
        }

        if (empty($result['response']['access_token'])) {
            throw new \RuntimeException('LetsPeppol OAuth failed: no access_token in response.');
        }

        $this->accessToken = $result['response']['access_token'];
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
        if ( ! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $options = ['bearer' => $this->accessToken];

        if ($multipart) {
            $options['multipart'] = $payload;
        } elseif ( ! empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->http->request($method, $url, $options);
    }
}
