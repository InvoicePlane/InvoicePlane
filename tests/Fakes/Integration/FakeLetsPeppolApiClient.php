<?php

namespace Tests\Fakes\Integration;

/**
 * A LetsPeppolApiClient pre-wired with an ApiClientFake.
 *
 * Exposes requestLog and tokenLog as shortcuts so tests can assert on what
 * was sent without touching the network.
 */
class FakeLetsPeppolApiClient extends \LetsPeppolApiClient
{
    public array $requestLog;
    public array $tokenLog;

    private ApiClientFake $fakeHttp;

    public function __construct(
        array   $responses     = [],
        array   $tokenResponse = ['access_token' => 'fake-token'],
        ?string $tokenError    = null
    ) {
        $this->fakeHttp   = new ApiClientFake($responses, $tokenResponse, $tokenError);
        $this->requestLog = &$this->fakeHttp->requestLog;
        $this->tokenLog   = &$this->fakeHttp->tokenLog;

        parent::__construct($this->fakeHttp);
    }
}
