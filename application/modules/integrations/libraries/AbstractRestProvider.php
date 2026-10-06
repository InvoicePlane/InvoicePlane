<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Shared plumbing for REST e-invoicing providers.
 *
 * A provider declares itself once in definition(); the registry metadata (code, name, auth type,
 * default settings, settings form) is derived from it. What remains in the subclass is what really
 * differs per provider: the send payload, status mapping and incoming-document mapping.
 *
 * definition() shape:
 *   code     string  registry code (^[a-z][a-z0-9_-]*$)
 *   name     string  display name
 *   label    string  short name used in exception messages (defaults to name)
 *   auth     string  none | oauth2 | bearer | api_key
 *   token    string  bearer only: the setting that holds the access token (default access_token)
 *   settings array<string, array{default?: mixed, type: string, required?: bool, sensitive?: bool}>
 *            in form order; endpoint settings are the keys ending in "_endpoint"
 *
 * authenticate() and fetchToken() work out of the box for bearer (token setting + api_base_url) and
 * oauth2 (client_id, client_secret, token_url + api_base_url); override them for anything else.
 * A new provider then implements sendInvoice, getInvoiceStatus, receiveInvoices,
 * downloadInvoiceDocument and getInvoiceEvents.
 */
abstract class AbstractRestProvider implements IntegrationClientInterface
{
    use ProviderPing;

    protected array $settings = [];

    protected ?string $accessToken = null;

    protected ApiClientInterface $http;

    /** Providers whose callers pass request context opt in to having it echoed under response['request']. */
    protected bool $mergeRequestDebug = false;

    public function __construct(?ApiClientInterface $http = null)
    {
        $this->http = $http ?? IntegrationTransport::httpClient() ?? new CurlApiClient();
    }

    abstract protected static function definition(): array;

    final public static function clientCode(): string
    {
        return (string) static::definition()['code'];
    }

    final public static function clientName(): string
    {
        return (string) static::definition()['name'];
    }

    final public static function authType(): string
    {
        return (string) static::definition()['auth'];
    }

    final public static function defaultSettings(): array
    {
        $defaults = [];
        foreach (static::definition()['settings'] as $key => $field) {
            $defaults[$key] = $field['default'] ?? '';
        }

        return $defaults;
    }

    final public static function settingsSchema(): array
    {
        $schema = [];
        foreach (static::definition()['settings'] as $key => $field) {
            $entry = ['type' => $field['type'], 'label' => $key];
            if ( ! empty($field['required'])) {
                $entry['required'] = true;
            }
            if ( ! empty($field['sensitive'])) {
                $entry['sensitive'] = true;
            }
            $schema[$key] = $entry;
        }

        return $schema;
    }

    public function authenticate(array $settings): bool
    {
        $this->settings = $settings;

        if (static::authType() === 'oauth2') {
            $this->requireSettings($settings, ['client_id', 'client_secret', 'token_url', 'api_base_url']);
            $token = $this->oauthFetchToken($settings['token_url'], $settings['client_id'], $settings['client_secret']);
            if (empty($token['access_token'])) {
                throw new RuntimeException(static::label() . ' OAuth failed: no access_token in response.');
            }
            $this->accessToken = $token['access_token'];

            return true;
        }

        $this->requireSettings($settings, [static::tokenSetting(), 'api_base_url']);
        $this->accessToken = (string) $settings[static::tokenSetting()];

        return true;
    }

    public function fetchToken(array $settings): string
    {
        if (static::authType() === 'oauth2') {
            $token = $this->oauthFetchToken($settings['token_url'] ?? '', $settings['client_id'] ?? '', $settings['client_secret'] ?? '');

            return (string) ($token['access_token'] ?? '');
        }

        return (string) ($settings[static::tokenSetting()] ?? '');
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    protected static function tokenSetting(): string
    {
        return (string) (static::definition()['token'] ?? 'access_token');
    }

    protected static function label(): string
    {
        return (string) (static::definition()['label'] ?? static::definition()['name']);
    }

    protected function buildUrl(string $endpoint, array $query = []): string
    {
        $url = rtrim($this->settings['api_base_url'], '/') . '/' . ltrim($endpoint, '/');

        if ( ! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    protected function requireSetting(string $key): void
    {
        $this->requireSettings($this->settings, [$key]);
    }

    protected function requireSettings(array $settings, array $keys): void
    {
        foreach ($keys as $key) {
            if (empty($settings[$key])) {
                throw new RuntimeException('Missing ' . static::label() . ' setting: ' . $key);
            }
        }
    }

    protected function bearerToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @return list<string> raw "Name: value" header lines added to every request
     */
    protected function extraHeaders(): array
    {
        return [];
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $options = ['bearer' => $this->bearerToken()];

        if ($this->extraHeaders() !== []) {
            $options['headers'] = $this->extraHeaders();
        }

        if ($multipart) {
            $options['multipart'] = $payload;
        } elseif ($method === RequestMethod::POST && ! empty($payload)) {
            $options['json'] = $payload;
        }

        $response = $this->http->request($method, $url, $options);

        if ($this->mergeRequestDebug && $requestDebug !== []) {
            $response['request'] = array_merge($response['request'] ?? [], $requestDebug);
        }

        return $response;
    }

    /**
     * OAuth2 client-credentials grant (form-encoded POST to the token URL).
     *
     * @return array<string, mixed> decoded token response
     */
    protected function oauthFetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $result = $this->http->request(RequestMethod::POST, $tokenUrl, [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ],
        ]);

        if ( ! $result['success']) {
            throw new RuntimeException(static::label() . ' OAuth error: ' . $result['message']);
        }

        return $result['response'];
    }
}
