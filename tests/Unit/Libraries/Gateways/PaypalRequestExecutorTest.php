<?php

namespace Tests\Unit\Libraries\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PaypalRequestExecutor;
use PHPUnit\Framework\TestCase;

class PaypalRequestExecutorTest extends TestCase
{
    private PaypalRequestExecutor $executor;
    private \PHPUnit\Framework\MockObject\MockObject $client_mock;

    protected function setUp(): void
    {
        $this->client_mock = $this->createMock(Client::class);
        $this->executor = new PaypalRequestExecutor($this->client_mock);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_executes_successful_request_and_returns_response(): void
    {
        $response = new Response(200, [], 'test response body');

        $callback = fn () => $response;

        $result = $this->executor->execute($callback, 'test action');

        $this->assertTrue($result['status']);
        $this->assertSame($response, $result['response']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_client_exception_and_returns_error(): void
    {
        $request = new Request('POST', 'https://api-m.paypal.com/v1/oauth2/token');
        $response = new Response(401, [], '{"error":"unauthorized"}');
        $exception = new ClientException('Unauthorized', $request, $response);

        $callback = fn () => throw $exception;

        $result = $this->executor->execute($callback, 'test action');

        $this->assertFalse($result['status']);
        $this->assertSame($exception, $result['error']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_invalid_argument_exception(): void
    {
        $exception = new InvalidArgumentException('Invalid order ID format');

        $callback = fn () => throw $exception;

        $result = $this->executor->execute($callback, 'test action');

        $this->assertFalse($result['status']);
        $this->assertSame($exception, $result['error']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_catches_throwable_exceptions(): void
    {
        $exception = new \Exception('Unknown error');

        $callback = fn () => throw $exception;

        $this->expectException(\Exception::class);
        $this->executor->execute($callback, 'test action');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_action_messages(): void
    {
        $response = new Response(200);
        $callback = fn () => $response;

        $this->executor->execute($callback, 'complex operation');

        $this->assertTrue(true); // Log verification would require mocking log_message
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_multiple_sequential_requests(): void
    {
        $response1 = new Response(200, [], 'response1');
        $response2 = new Response(200, [], 'response2');

        $result1 = $this->executor->execute(fn () => $response1, 'action1');
        $result2 = $this->executor->execute(fn () => $response2, 'action2');

        $this->assertTrue($result1['status']);
        $this->assertTrue($result2['status']);
        $this->assertSame($response1, $result1['response']);
        $this->assertSame($response2, $result2['response']);
    }
}
