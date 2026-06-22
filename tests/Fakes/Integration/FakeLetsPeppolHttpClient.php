<?php

namespace Tests\Fakes\Integration;

use RequestMethod;
use RuntimeException;

require_once dirname(__DIR__, 3) . '/application/modules/integrations/libraries/providers/LetsPeppol/LetsPeppolHttpClientInterface.php';

/**
 * Test double for LetsPeppolHttpClientInterface.
 *
 * Records every call so tests can assert on what was sent without hitting the
 * network. Responses are served from a pre-seeded queue; when the queue is
 * exhausted a default 200-OK envelope is returned.
 */
class FakeLetsPeppolHttpClient implements \LetsPeppolHttpClientInterface
{
    public array $requestLog = [];
    public array $tokenLog   = [];

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

    public function fetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $this->tokenLog[] = compact('tokenUrl', 'clientId', 'clientSecret');

        if ($this->tokenError !== null) {
            throw new RuntimeException($this->tokenError);
        }

        return $this->tokenResponse;
    }

    public function send(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        ?string $bearerToken = null
    ): array {
        $this->requestLog[] = compact('method', 'url', 'payload', 'multipart', 'bearerToken');

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
