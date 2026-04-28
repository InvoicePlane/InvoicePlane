<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use RuntimeException;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Integration\Support\HttpResponse;

abstract class AbstractTestCase extends PhpUnitTestCase
{
    use InteractsWithDatabase;

    protected array $sessionData = [];

    protected function actingAsAdmin(int $userId = 1): void
    {
        $this->sessionData = [
            'user_id'       => $userId,
            'user_type'     => 1,
            'user_email'    => 'admin@test.local',
            'user_name'     => 'Test Admin',
            'user_company'  => 'Test Co',
            'user_language' => 'system',
        ];
    }

    protected function actingAsGuest(): void
    {
        $this->sessionData = [];
    }

    protected function actingAs(object|array $user): void
    {
        $data = is_array($user) ? $user : get_object_vars($user);

        $this->sessionData = [
            'user_id'       => (int) ($data['user_id'] ?? 1),
            'user_type'     => (int) ($data['user_type'] ?? 1),
            'user_email'    => (string) ($data['user_email'] ?? 'admin@test.local'),
            'user_name'     => (string) ($data['user_name'] ?? 'Test User'),
            'user_company'  => (string) ($data['user_company'] ?? 'Test Company'),
            'user_language' => (string) ($data['user_language'] ?? 'system'),
        ];
    }

    protected function request(string $method, string $uri, array $query = [], array $post = []): HttpResponse
    {
        $payload = [
            'method'  => mb_strtoupper($method),
            'uri'     => $this->normalizeUri($uri),
            'query'   => $query,
            'post'    => $post,
            'session' => $this->sessionData,
        ];

        $command = sprintf('php %s', escapeshellarg(dirname(__DIR__) . '/tests/Integration/bin/request.php'));
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            dirname(__DIR__),
            ['CI_TEST_REQUEST' => base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR))],
        );

        if ( ! is_resource($process)) {
            throw new RuntimeException('Unable to start request process.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        preg_match('/__CI_TEST_RESULT_START__(.*?)__CI_TEST_RESULT_END__/s', $stdout ?: '', $matches);

        if ($exitCode !== 0 || ! isset($matches[1])) {
            throw new RuntimeException(
                "CI request runner failed for [{$payload['method']}] {$payload['uri']}" . PHP_EOL
                . 'Exit code: ' . $exitCode . PHP_EOL
                . 'STDOUT: ' . ($stdout ?: '[empty]') . PHP_EOL
                . 'STDERR: ' . ($stderr ?: '[empty]')
            );
        }

        $result = json_decode(base64_decode($matches[1], true), true, 512, JSON_THROW_ON_ERROR);

        if (($result['exception'] ?? null) !== null) {
            throw new RuntimeException(
                "Unhandled exception during CI request [{$payload['method']}] {$payload['uri']}: "
                . $result['exception']
            );
        }

        return new HttpResponse(
            $result['output'] ?? '',
            (int) ($result['status'] ?? 200),
            $result['headers'] ?? [],
            (string) $stderr,
        );
    }

    protected function get(string $uri, array $query = []): HttpResponse
    {
        return $this->request('GET', $uri, $query, []);
    }

    protected function post(string $uri, array $data = [], array $query = []): HttpResponse
    {
        return $this->request('POST', $uri, $query, $data);
    }

    protected function delete(string $uri, array $data = [], array $query = []): HttpResponse
    {
        return $this->request('DELETE', $uri, $query, $data);
    }

    protected function assertResponseOk(HttpResponse $response): void
    {
        self::assertSame(200, $response->statusCode());
    }

    protected function assertResponseSuccessful(HttpResponse $response): void
    {
        self::assertGreaterThanOrEqual(200, $response->statusCode());
        self::assertLessThan(300, $response->statusCode());
    }


    protected function assertOk(HttpResponse $response): void
    {
        $this->assertResponseOk($response);
    }

    protected function assertRedirectTo(HttpResponse $response, string $expectedUrl): void
    {
        $this->assertResponseRedirectTo($response, $expectedUrl);
    }

    protected function assertJsonKey(HttpResponse $response, string $key): void
    {
        self::assertArrayHasKey($key, $response->json());
    }

    protected function assertResponseRedirectTo(HttpResponse $response, string $expectedUrl): void
    {
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Expected redirect status code, got [%d].', $response->statusCode())
        );
        self::assertSame($expectedUrl, $response->redirectUrl());
    }

    protected function assertResponseBodyContains(HttpResponse $response, string $needle): void
    {
        self::assertStringContainsString($needle, $response->body());
    }

    protected function assertResponseBodyNotContains(HttpResponse $response, string $needle): void
    {
        self::assertStringNotContainsString($needle, $response->body());
    }

    protected function assertResponseStatusCode(HttpResponse $response, int $expected): void
    {
        self::assertSame($expected, $response->statusCode());
    }

    protected function assertResponseHasNoPhpErrors(HttpResponse $response): void
    {
        foreach (['Fatal error', 'Parse error', 'Uncaught', 'Warning:', 'Notice:', 'Deprecated:'] as $marker) {
            self::assertStringNotContainsString($marker, $response->body());
        }
    }

    protected function assertResponseMatchesSnapshot(HttpResponse $response, string $snapshotName): void
    {
        $dir = dirname(__DIR__) . '/tests/__snapshots__';

        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $path = $dir . '/' . $snapshotName . '.snap';

        if ( ! file_exists($path)) {
            file_put_contents($path, $response->body());
            self::markTestSkipped(sprintf('Snapshot [%s] created. Re-run test.', $snapshotName));
        }

        self::assertSame((string) file_get_contents($path), $response->body());
    }

    private function normalizeUri(string $uri): string
    {
        $trimmed = ltrim($uri, '/');

        return '/' . $trimmed;
    }
}
