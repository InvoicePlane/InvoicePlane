<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/PdpProviderInterface.php';
require_once APPPATH . 'modules/pdp/libraries/PdpStatusMapper.php';

/**
 * Provider SuperPDP fonctionnel.
 *
 * Valide avec l'API SuperPDP :
 * - OAuth2 client_credentials : POST https://api.superpdp.tech/oauth2/token
 * - Controle session : GET /v1.beta/oauth2_sessions/me
 * - Envoi facture : POST /v1.beta/invoices?external_id=...&disable_pre_check=true|false
 * - Payload : PDF Factur-X brut avec Content-Type: application/pdf
 * - Statut : GET /v1.beta/invoices/{id}
 * - Evenements : GET /v1.beta/invoice_events
 */
class SuperPdpProvider implements PdpProviderInterface
{
    public static function providerCode(): string
    {
        return 'superpdp';
    }

    public static function providerName(): string
    {
        return 'SuperPDP';
    }

    private $settings = array();
    private $accessToken = null;

    public function authenticate(array $settings): bool
    {
        $this->settings = $this->withDefaults($settings);

        // Utile pour tests rapides avec un token deja genere.
        if (!empty($this->settings['access_token']) && empty($this->settings['client_secret'])) {
            $this->accessToken = trim($this->settings['access_token']);
            return true;
        }

        if (empty($this->settings['client_id']) || empty($this->settings['client_secret'])) {
            log_message('error', 'SuperPDP OAuth: client_id ou client_secret manquant.');
            return false;
        }

        $ch = curl_init($this->settings['token_url']);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->settings['client_id'] . ':' . $this->settings['client_secret'],
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ),
            CURLOPT_POSTFIELDS => http_build_query(array('grant_type' => 'client_credentials')),
            CURLOPT_TIMEOUT => (int) ($this->settings['timeout'] ?? 60),
        ));

        $body = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            log_message('error', 'SuperPDP OAuth cURL error: ' . $error);
            return false;
        }

        $json = json_decode($body, true);
        if ($httpCode >= 200 && $httpCode < 300 && is_array($json) && !empty($json['access_token'])) {
            $this->accessToken = $json['access_token'];
            return true;
        }

        log_message('error', 'SuperPDP OAuth failed HTTP ' . $httpCode . ': ' . $body);
        return false;
    }

    public function sendInvoice(string $filePath, array $invoiceData): array
    {
        if (empty($this->accessToken)) {
            return $this->error('Authentification SuperPDP impossible : access_token manquant.');
        }

        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->error('PDF Factur-X introuvable ou illisible : ' . $filePath);
        }

        $externalId = $this->buildExternalId($invoiceData);
        $query = array('external_id' => $externalId);

        if ($this->asBool($this->settings['disable_pre_check'] ?? false)) {
            $query['disable_pre_check'] = 'true';
        }

	$url = $this->endpoint('send_endpoint', '/v1.beta/invoices') . '?' . http_build_query($query);
	// die($url);
        $body = file_get_contents($filePath);
        if ($body === false) {
            return $this->error('Impossible de lire le PDF Factur-X : ' . $filePath);
        }

        $response = $this->rawRequest('POST', $url, $body, array(
            'Content-Type: application/pdf',
            'Content-Length: ' . strlen($body),
        ));

        $response['invoiceplane_external_id'] = $externalId;
        $response['provider_external_id'] = $response['external_id'] ?? null;
        $response['direction'] = is_array($response['raw'] ?? null) ? ($response['raw']['direction'] ?? null) : null;

        if ($response['success']) {
            $response['message'] = $response['message'] ?: 'Facture televersee chez SuperPDP.';
        }

        return $response;
    }

    public function getInvoiceStatus(string $externalId): array
    {
        if (empty($this->accessToken)) {
            return $this->error('Authentification SuperPDP impossible : access_token manquant.');
        }

        $url = str_replace('{id}', rawurlencode($externalId), $this->endpoint('status_endpoint', '/v1.beta/invoices/{id}'));
        return $this->rawRequest('GET', $url);
    }

    public function receiveInvoices(array $filters = array()): array
    {
        if (empty($this->accessToken)) {
            return $this->error('Authentification SuperPDP impossible : access_token manquant.');
        }

        $query = array('direction' => 'incoming', 'order' => 'desc', 'limit' => 100);
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }

        $url = $this->endpoint('receive_endpoint', '/v1.beta/invoices') . '?' . http_build_query($query);
        return $this->rawRequest('GET', $url);
    }

    public function invoiceEvents(array $filters = array()): array
    {
        if (empty($this->accessToken)) {
            return $this->error('Authentification SuperPDP impossible : access_token manquant.');
        }
        $url = $this->endpoint('events_endpoint', '/v1.beta/invoice_events');
        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }
        return $this->rawRequest('GET', $url);
    }

    public function me(): array
    {
        if (empty($this->accessToken)) {
            return $this->error('Authentification SuperPDP impossible : access_token manquant.');
        }
        return $this->rawRequest('GET', $this->endpoint('me_endpoint', '/v1.beta/oauth2_sessions/me'));
    }

    private function rawRequest(string $method, string $url, $body = null, array $extraHeaders = array()): array
    {
        $headers = array_merge(array(
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken,
        ), $extraHeaders);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => (int) ($this->settings['timeout'] ?? 90),
        ));

        if ($method !== 'GET' && $body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            return $this->error($error ?: 'Erreur cURL SuperPDP inconnue.');
        }

        $json = json_decode($responseBody, true);
        $raw = is_array($json) ? $json : $responseBody;
        $event = $this->lastEvent(is_array($json) ? $json : array());

        $statusCode = $event['status_code'] ?? (is_array($json) ? ($json['status_code'] ?? $json['status'] ?? $json['state'] ?? null) : null);
        $statusText = $event['status_text'] ?? (is_array($json) ? ($json['status_text'] ?? null) : null);
        $message = is_array($json)
            ? ($json['message'] ?? $json['detail'] ?? $json['error_description'] ?? $statusText ?? ('HTTP ' . $httpCode))
            : ('HTTP ' . $httpCode);

        return array(
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $this->normalizeSuperPdpStatus($statusCode, $httpCode),
            'status_code' => $statusCode,
            'status_text' => $statusText,
            'external_id' => is_array($json) ? (string) ($json['id'] ?? $json['invoice_id'] ?? $json['uuid'] ?? '') : null,
            'message' => $message,
            'http_code' => $httpCode,
            'raw' => $raw,
        );
    }

    private function lastEvent(array $json): ?array
    {
        if (empty($json['events']) || !is_array($json['events'])) {
            return null;
        }
        $events = $json['events'];
        return is_array(end($events)) ? end($events) : null;
    }

    private function normalizeSuperPdpStatus($statusCode, int $httpCode): string
    {
        if ($httpCode < 200 || $httpCode >= 300) {
            return PdpStatusMapper::ERROR;
        }

        $s = strtolower((string) $statusCode);
        if ($s === '') {
            return PdpStatusMapper::SENT;
        }
        if (strpos($s, 'uploaded') !== false) {
            return PdpStatusMapper::DEPOSITED;
        }
        if (strpos($s, 'rejected') !== false || strpos($s, 'failed') !== false || strpos($s, 'error') !== false) {
            return PdpStatusMapper::REJECTED;
        }
        if (strpos($s, 'accepted') !== false || strpos($s, 'validated') !== false) {
            return PdpStatusMapper::ACCEPTED;
        }
        if (strpos($s, 'sent') !== false || strpos($s, 'submitted') !== false) {
            return PdpStatusMapper::SENT;
        }
        if (strpos($s, 'paid') !== false) {
            return PdpStatusMapper::PAID;
        }

        return PdpStatusMapper::normalize($statusCode, $httpCode);
    }

    private function endpoint(string $key, string $fallback): string
    {
        $baseUrl = rtrim($this->settings['api_url'], '/');
        $path = $this->settings[$key] ?? $fallback;
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        return $baseUrl . '/' . ltrim($path, '/');
    }

    private function withDefaults(array $settings): array
    {
        $defaults = array(
            'provider' => 'superpdp',
            'auth_type' => 'oauth2_client_credentials',
            'api_url' => 'https://api.superpdp.tech',
            'token_url' => 'https://api.superpdp.tech/oauth2/token',
            'send_endpoint' => '/v1.beta/invoices',
            'status_endpoint' => '/v1.beta/invoices/{id}',
            'receive_endpoint' => '/v1.beta/invoices',
            'events_endpoint' => '/v1.beta/invoice_events',
            'me_endpoint' => '/v1.beta/oauth2_sessions/me',
            'disable_pre_check' => '0',
            'timeout' => 90,
        );

        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key]) || $settings[$key] === '') {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    private function buildExternalId(array $invoiceData): string
    {
        $candidate = $invoiceData['external_id']
            ?? $invoiceData['invoice_number']
            ?? ('IP-' . ($invoiceData['invoice_id'] ?? date('YmdHis')));

        $candidate = preg_replace('/[^A-Za-z0-9_.-]/', '-', (string) $candidate);
        return substr($candidate, 0, 36);
    }

    private function asBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        return in_array(strtolower((string) $value), array('1', 'true', 'yes', 'on'), true);
    }

    private function error(string $message): array
    {
        return array(
            'success' => false,
            'status' => PdpStatusMapper::ERROR,
            'status_code' => null,
            'status_text' => null,
            'external_id' => null,
            'message' => $message,
            'http_code' => null,
            'raw' => null,
        );
    }
}
