<?php

namespace Tests\Fakes\Integration;

use ApiClientInterface;
use RequestMethod;

/**
 * Queue-driven ApiClientInterface for the send_invoice Feature tests.
 *
 * IntegrationTransport::httpClient() builds one of these from the JSON in the
 * INTEGRATION_MOCK_RESPONSES env var and hands it to every provider client for
 * the duration of the request. OAuth token requests (identified by
 * form_params.grant_type) are answered from the "token" config; every other
 * request is answered with the next entry from "responses", in call order.
 *
 * Config: {"responses": [<envelope>, ...], "token": {"access_token": "..."},
 * "token_error": "..."} — or a bare JSON list, treated as {"responses": [...]}.
 * Each response entry is merged onto a 200-OK envelope, so a test only spells
 * out the keys the provider client actually reads (usually success, http_code
 * and a nested response.* shape).
 */
final class QueueApiClient implements ApiClientInterface
{
    /** @var array<int, array<string, mixed>> */
    private array $responses;

    private int $index = 0;

    /** @var array<string, mixed> */
    private array $token;

    private ?string $tokenError;

    /**
     * @param array<int|string, mixed> $config
     */
    public function __construct(array $config)
    {
        if (array_is_list($config)) {
            $config = ['responses' => $config];
        }

        $this->responses  = array_values($config['responses'] ?? []);
        $this->token      = $config['token'] ?? ['access_token' => 'integration-test-token', 'token_type' => 'Bearer', 'expires_in' => 3600];
        $this->tokenError = $config['token_error'] ?? null;
    }

    public function request(RequestMethod $method, string $url, array $options = []): array
    {
        if ( ! empty($options['form_params']['grant_type'])) {
            if ($this->tokenError !== null) {
                return $this->envelope(false, ['message' => $this->tokenError, 'http_code' => 401]);
            }

            return $this->envelope(true, ['response' => $this->token]);
        }

        $next = $this->responses[$this->index++] ?? ['success' => true, 'status' => 'sent'];

        return $this->envelope(
            $next['success'] ?? true,
            $next + ['request' => ['url' => $url, 'method' => $method->value]]
        );
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function envelope(bool $success, array $overrides): array
    {
        return array_replace([
            'success'     => $success,
            'external_id' => null,
            'status'      => $success ? 'ok' : 'error',
            'message'     => 'ok',
            'http_code'   => $success ? 200 : 500,
            'request'     => [],
            'response'    => [],
        ], $overrides);
    }
}
