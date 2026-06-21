<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LetsPeppolApiClient
{
    private ?string $accessToken = null;
    private array $settings = [];

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
        $decoded = $this->fetchToken(
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

        return $this->send($method, $url, $payload, $multipart);
    }

    protected function fetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL             => $tokenUrl,
            CURLOPT_POST            => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_HTTPHEADER      => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'openid',
            ]),
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('LetsPeppol OAuth error: ' . $curlError);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException('LetsPeppol OAuth failed (HTTP ' . $httpCode . '): ' . $rawResponse);
        }

        return json_decode($rawResponse, true) ?? [];
    }

    protected function send(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false
    ): array {
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Accept: application/json',
        ];

        $ch = curl_init();

        $options = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
        ];

        if ($method === RequestMethod::POST) {
            $options[CURLOPT_POST] = true;

            if ($multipart) {
                $options[CURLOPT_POSTFIELDS] = $payload;
            } else {
                $options[CURLOPT_POSTFIELDS] = json_encode($payload);
                $headers[] = 'Content-Type: application/json';
                $options[CURLOPT_HTTPHEADER] = $headers;
            }
        }

        curl_setopt_array($ch, $options);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($rawResponse, true);

        if ($curlError) {
            return [
                'success'     => false,
                'external_id' => null,
                'status'      => 'error',
                'message'     => $curlError,
                'http_code'   => 0,
                'request'     => ['url' => $url, 'method' => $method->value],
                'response'    => [],
            ];
        }

        return [
            'success'     => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $decoded['id'] ?? null,
            'status'      => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message'     => $decoded['message'] ?? 'LetsPeppol API response received',
            'http_code'   => $httpCode,
            'request'     => ['url' => $url, 'method' => $method->value],
            'response'    => $decoded ?: [],
        ];
    }
}
