<?php

namespace Tests\Fakes\Integration;

class FakeLetsPeppolApiClient extends \LetsPeppolApiClient
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
    }

    protected function fetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $this->tokenLog[] = compact('tokenUrl', 'clientId', 'clientSecret');

        if ($this->tokenError !== null) {
            throw new \RuntimeException($this->tokenError);
        }

        return $this->tokenResponse;
    }

    protected function send(
        \RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false
    ): array {
        $this->requestLog[] = compact('method', 'url', 'payload', 'multipart');

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
