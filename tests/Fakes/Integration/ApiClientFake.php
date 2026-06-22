<?php

namespace Tests\Fakes\Integration;

use RequestMethod;
use RuntimeException;

/**
 * Test double for ApiClientInterface.
 *
 * Records every call so tests can assert on what was sent without hitting the
 * network. Responses are served from a pre-seeded queue; when the queue is
 * exhausted a default 200-OK envelope is returned.
 *
 * OAuth token requests (identified by form_params.grant_type) are routed to
 * the tokenResponse queue and recorded in tokenLog separately so that tests
 * can assert on authenticate() calls without consuming the responses queue.
 */
class ApiClientFake implements \ApiClientInterface
{
    /** Entries: [method, url, payload, multipart, bearerToken] — matches legacy FakeLetsPeppolHttpClient shape. */
    public array $requestLog = [];

    /** Entries: [tokenUrl, clientId, clientSecret] */
    public array $tokenLog = [];

    private array   $responses;
    private int     $callIndex;
    private array   $tokenResponse;
    private ?string $tokenError;

    public function __construct(
        array   $responses     = [],
        array   $tokenResponse = ['access_token' => 'fake-token'],
        ?string $tokenError    = null
    ) {
        $this->responses     = $responses;
        $this->callIndex     = 0;
        $this->tokenResponse = $tokenResponse;
        $this->tokenError    = $tokenError;
    }

    public function request(RequestMethod $method, string $url, array $options = []): array
    {
        if (!empty($options['form_params']['grant_type'])) {
            return $this->handleTokenRequest($url, $options['form_params']);
        }

        $this->requestLog[] = [
            'method'      => $method,
            'url'         => $url,
            'payload'     => $options['json'] ?? $options['multipart'] ?? [],
            'multipart'   => isset($options['multipart']),
            'bearerToken' => $options['bearer'] ?? null,
            'options'     => $options,
        ];

        return $this->responses[$this->callIndex++] ?? [
            'success'     => true,
            'external_id' => null,
            'status'      => 'sent',
            'message'     => 'ok',
            'http_code'   => 200,
            'request'     => ['url' => $url, 'method' => $method->value],
            'response'    => [],
        ];
    }

    private function handleTokenRequest(string $url, array $formParams): array
    {
        $this->tokenLog[] = [
            'tokenUrl'     => $url,
            'clientId'     => $formParams['client_id'] ?? null,
            'clientSecret' => $formParams['client_secret'] ?? null,
        ];

        if ($this->tokenError !== null) {
            throw new RuntimeException($this->tokenError);
        }

        return [
            'success'     => true,
            'external_id' => null,
            'status'      => 'ok',
            'message'     => 'ok',
            'http_code'   => 200,
            'request'     => ['url' => $url, 'method' => 'POST'],
            'response'    => $this->tokenResponse,
        ];
    }
}
