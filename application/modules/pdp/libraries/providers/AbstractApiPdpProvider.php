<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/PdpProviderInterface.php';
require_once APPPATH . 'modules/pdp/libraries/PdpStatusMapper.php';

abstract class AbstractApiPdpProvider implements PdpProviderInterface
{
    protected $settings = array();
    protected $accessToken = null;

    public function authenticate(array $settings): bool
    {
        $this->settings = $settings;
        $authType = $settings['auth_type'] ?? 'bearer';

        if ($authType === 'none') {
            return true;
        }

        if ($authType === 'oauth2_client_credentials') {
            return $this->authenticateClientCredentials();
        }

        if ($authType === 'api_key') {
            return !empty($settings['api_key']);
        }

        $this->accessToken = $settings['access_token'] ?? null;
        return !empty($this->accessToken) || !empty($settings['api_url']);
    }

    protected function authenticateClientCredentials(): bool
    {
        if (empty($this->settings['token_url']) || empty($this->settings['client_id']) || empty($this->settings['client_secret'])) {
            return false;
        }

        $response = $this->httpRequest('POST', $this->settings['token_url'], array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->settings['client_id'],
            'client_secret' => $this->settings['client_secret'],
            'scope' => $this->settings['scope'] ?? '',
        ), false, array('Content-Type: application/x-www-form-urlencoded'));

        if (!empty($response['raw']['access_token'])) {
            $this->accessToken = $response['raw']['access_token'];
            return true;
        }

        return false;
    }

    protected function endpoint(string $key, string $fallback): string
    {
        $baseUrl = rtrim($this->settings['api_url'] ?? '', '/');
        $path = $this->settings[$key] ?? $fallback;
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        return $baseUrl . '/' . ltrim($path, '/');
    }

    protected function httpRequest(string $method, string $url, array $fields = array(), bool $multipart = false, array $extraHeaders = array()): array
    {
        $ch = curl_init();
        $headers = array('Accept: application/json');

        $authType = $this->settings['auth_type'] ?? 'bearer';
        if ($authType === 'api_key' && !empty($this->settings['api_key'])) {
            $headerName = $this->settings['api_key_header'] ?? 'X-API-Key';
            $headers[] = $headerName . ': ' . $this->settings['api_key'];
        } elseif (!empty($this->accessToken)) {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        } elseif (!empty($this->settings['access_token'])) {
            $headers[] = 'Authorization: Bearer ' . $this->settings['access_token'];
        }

        foreach ($extraHeaders as $header) {
            $headers[] = $header;
        }

        if (!$multipart && $method !== 'GET' && !$this->hasContentType($headers)) {
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => (int)($this->settings['timeout'] ?? 60),
        ));

        if ($method !== 'GET') {
            if ($multipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            } elseif ($this->hasFormContentType($headers)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            }
        }

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return array('success' => false, 'status' => PdpStatusMapper::ERROR, 'message' => $error, 'raw' => null);
        }

        $json = json_decode($body, true);
        $rawStatus = is_array($json) ? ($json['status'] ?? $json['state'] ?? null) : null;

        return array(
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => PdpStatusMapper::normalize($rawStatus, $httpCode),
            'external_id' => is_array($json) ? ($json['id'] ?? $json['external_id'] ?? $json['uuid'] ?? null) : null,
            'message' => is_array($json) ? ($json['message'] ?? $json['detail'] ?? ('HTTP ' . $httpCode)) : ('HTTP ' . $httpCode),
            'http_code' => $httpCode,
            'raw' => $json ?: $body,
        );
    }

    private function hasContentType(array $headers): bool
    {
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Type:') === 0) return true;
        }
        return false;
    }

    private function hasFormContentType(array $headers): bool
    {
        foreach ($headers as $header) {
            if (stripos($header, 'application/x-www-form-urlencoded') !== false) return true;
        }
        return false;
    }
}
