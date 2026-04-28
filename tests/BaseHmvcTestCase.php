<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests;

use Tests\Concerns\InteractsWithDatabase;
use Tests\Concerns\InteractsWithSession;

abstract class BaseHmvcTestCase extends AbstractTestCase
{
    use InteractsWithDatabase;
    use InteractsWithSession;

    protected HmvcRequestRunner $runner;

    protected Snapshot $snapshot;

    private static ?HmvcKernel $sharedKernel = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$sharedKernel === null) {
            self::$sharedKernel = new HmvcKernel();
            self::$sharedKernel->boot();
        }

        $this->runner   = new HmvcRequestRunner(self::$sharedKernel);
        $this->snapshot = new Snapshot();

        $this->resetGlobalState();
    }

    protected function tearDown(): void
    {
        $this->resetGlobalState();
        parent::tearDown();
    }

    protected function get(string $uri, array $query = []): HmvcResponse
    {
        return $this->runner->get($uri, $query);
    }

    protected function post(string $uri, array $body = []): HmvcResponse
    {
        return $this->runner->post($uri, $body);
    }

    protected function assertResponseBodyContains(HmvcResponse $response, string $needle): void
    {
        self::assertTrue(
            $response->contains($needle),
            sprintf(
                "Failed asserting response body contains [%s].\nActual body (first 500 chars):\n%s",
                $needle,
                mb_substr($response->body(), 0, 500)
            )
        );
    }

    protected function assertResponseBodyNotContains(HmvcResponse $response, string $needle): void
    {
        self::assertFalse(
            $response->contains($needle),
            sprintf(
                "Failed asserting response body does NOT contain [%s].\nBody (first 500 chars):\n%s",
                $needle,
                mb_substr($response->body(), 0, 500)
            )
        );
    }

    protected function assertResponseStatusCode(HmvcResponse $response, int $expected): void
    {
        self::assertSame(
            $expected,
            $response->statusCode(),
            sprintf(
                'Expected HTTP status [%d] but received [%d]. Body (first 300 chars): %s',
                $expected,
                $response->statusCode(),
                mb_substr($response->body(), 0, 300)
            )
        );
    }

    protected function assertResponseRedirectsTo(HmvcResponse $response, string $uriFragment): void
    {
        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Expected a 3xx redirect but got status [%d]. Body: %s',
                $response->statusCode(),
                mb_substr($response->body(), 0, 300)
            )
        );

        self::assertStringContainsString(
            $uriFragment,
            (string) $response->redirectUrl(),
            sprintf(
                'Expected redirect Location to contain [%s] but was [%s].',
                $uriFragment,
                (string) $response->redirectUrl()
            )
        );
    }

    protected function assertJsonKeyEquals(HmvcResponse $response, string $key, mixed $expected): void
    {
        $payload = $response->json();

        self::assertArrayHasKey(
            $key,
            $payload,
            sprintf('JSON payload missing key [%s]. Full payload: %s', $key, json_encode($payload))
        );

        self::assertSame(
            $expected,
            $payload[$key],
            sprintf(
                'JSON key [%s]: expected [%s] but got [%s].',
                $key,
                json_encode($expected),
                json_encode($payload[$key])
            )
        );
    }

    protected function assertResponseMatchesSnapshot(HmvcResponse $response, string $snapshotName): void
    {
        $this->snapshot->assert($snapshotName, $response->body(), $this);
    }

    protected function assertResponseHasNoPhpErrors(HmvcResponse $response): void
    {
        foreach (['Fatal error', 'Parse error', 'Uncaught', 'Warning:', 'Notice:', 'Deprecated:'] as $marker) {
            self::assertStringNotContainsString(
                $marker,
                $response->body(),
                sprintf('Response body contains PHP error indicator [%s].', $marker)
            );
        }
    }

    private function resetGlobalState(): void
    {
        $_GET   = [];
        $_POST  = [];
        $_FILES = [];

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['QUERY_STRING']   = '';
        $_SERVER['PATH_INFO']      = '/';
    }
}
