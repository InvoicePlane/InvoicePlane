<?php

namespace Tests\Fakes\Integration;

/**
 * A LetsPeppolApiClient pre-wired with a FakeLetsPeppolHttpClient.
 *
 * Exposes requestLog and tokenLog as shortcuts so existing tests that depend
 * on FakeLetsPeppolApiClient don't need to change their assertion style.
 */
class FakeLetsPeppolApiClient extends \LetsPeppolApiClient
{
    public array $requestLog;
    public array $tokenLog;

    private FakeLetsPeppolHttpClient $fakeHttp;

    public function __construct(
        array   $responses     = [],
        array   $tokenResponse = ['access_token' => 'fake-token'],
        ?string $tokenError    = null
    ) {
        $this->fakeHttp   = new FakeLetsPeppolHttpClient($responses, $tokenResponse, $tokenError);
        $this->requestLog = &$this->fakeHttp->requestLog;
        $this->tokenLog   = &$this->fakeHttp->tokenLog;

        parent::__construct($this->fakeHttp);
    }
}
