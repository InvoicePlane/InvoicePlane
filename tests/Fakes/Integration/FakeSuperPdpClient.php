<?php

namespace Tests\Fakes\Integration;

use ApiClientInterface;
use RequestMethod;
use RuntimeException;
use SuperPdpClient;

/**
 * A SuperPdpClient wired to a canned response queue so no real network traffic
 * is made.
 *
 * sendInvoice() / downloadInvoiceDocument() reach the HTTP adapter directly
 * (via $this->http); every other call goes through the protected request()
 * wrapper. Both paths funnel into $requestLog here, and OAuth token fetches are
 * recorded in $tokenLog without touching the queue.
 */
class FakeSuperPdpClient extends SuperPdpClient
{
    public array $requestLog = [];

    public array $tokenLog = [];

    private array $responses;

    private int $callIndex = 0;

    private array $tokenResponse;

    private ?string $tokenError;

    public function __construct(
        array $responses = [],
        array $tokenResponse = ['access_token' => 'fake-token'],
        ?string $tokenError = null
    ) {
        $this->responses     = $responses;
        $this->tokenResponse = $tokenResponse;
        $this->tokenError    = $tokenError;

        parent::__construct(new class ($this) implements ApiClientInterface {
            public function __construct(private FakeSuperPdpClient $owner) {}

            public function request(RequestMethod $method, string $url, array $options = []): array
            {
                return $this->owner->recordHttpCall($method, $url, $options);
            }
        });
    }

    public function recordHttpCall(RequestMethod $method, string $url, array $options): array
    {
        $this->requestLog[] = [
            'method'    => $method,
            'url'       => $url,
            'options'   => $options,
            'multipart' => isset($options['multipart']),
            'payload'   => $options['json'] ?? $options['multipart'] ?? $options['body'] ?? [],
        ];

        return $this->nextResponse($method, $url);
    }

    protected function oauthFetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $this->tokenLog[] = compact('tokenUrl', 'clientId', 'clientSecret');

        if ($this->tokenError !== null) {
            throw new RuntimeException($this->tokenError);
        }

        return $this->tokenResponse;
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $this->requestLog[] = compact('method', 'url', 'payload', 'multipart', 'requestDebug');

        return $this->nextResponse($method, $url);
    }

    private function nextResponse(RequestMethod $method, string $url): array
    {
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
}
