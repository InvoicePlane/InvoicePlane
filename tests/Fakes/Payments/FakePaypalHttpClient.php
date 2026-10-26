<?php

namespace Tests\Fakes\Payments;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * Guzzle transport fake for PaypalLib.
 *
 * PayPal uses Guzzle's concrete Client rather than an HTTP client interface,
 * so this fake is a handler that queues fixture responses and records the
 * prepared request at the transport boundary.
 */
final class FakePaypalHttpClient
{
    private function __construct(private MockHandler $mock) {}

    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $captureFile = getenv('PAYPAL_MOCK_REQUEST_CAPTURE');
        if (is_string($captureFile) && $captureFile !== '') {
            file_put_contents($captureFile, (string) json_encode([
                'method'  => $request->getMethod(),
                'url'     => (string) $request->getUri(),
                'headers' => $request->getHeaders(),
                'body'    => (string) $request->getBody(),
            ], JSON_THROW_ON_ERROR));
        }

        return ($this->mock)($request, $options);
    }

    /** @param array<int, array{status?: int, body?: string}> $queue */
    public static function handlerStack(array $queue): HandlerStack
    {
        $responses = array_map(
            static fn (array $entry): Response => new Response(
                $entry['status'] ?? 200,
                [],
                $entry['body'] ?? ''
            ),
            $queue
        );

        return \GuzzleHttp\HandlerStack::create(new self(new MockHandler($responses)));
    }
}
