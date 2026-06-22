<?php

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

    protected function request(string $method, string $uri, array $query = [], array $post = [], bool $ajax = false): HttpResponse
    {
        $payload = [
            'method'  => mb_strtoupper($method),
            'uri'     => $this->normalizeUri($uri),
            'query'   => $query,
            'post'    => $post,
            'session' => $this->sessionData,
            'ajax'    => $ajax,
        ];

        // Close any open PDO connection before spawning the subprocess.
        // SQLite WAL mode: uncommitted or un-checkpointed writes are invisible
        // to other connections. Closing the connection triggers a checkpoint so
        // the subprocess always sees every insert made during Arrange.
        $this->resetDatabaseConnection();

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

        // If no CI result envelope was emitted at all, the process died before request.php
        // could capture any output (e.g. a PHP parse error). Only then do we fail fast.
        if ( ! isset($matches[1])) {
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

    protected function ajax(string $method, string $uri, array $data = []): HttpResponse
    {
        return $this->request(mb_strtoupper($method), $uri, [], $data, true);
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

    /**
     * Fail with the actual error text if the response body contains a CI3 error page.
     *
     * DB errors and general application errors throw exceptions in the test subprocess
     * (via MY_Exceptions::show_error) and never reach the response body. This helper
     * is the belt-and-suspenders catch for PHP-level errors (notices, warnings, fatal
     * errors) which CI3 renders via error_php.php and which bypass show_error().
     *
     * Call this before asserting on a redirect or specific status code when you want
     * a clear failure message instead of "expected redirect, got 200".
     */
    protected function assertNoApplicationError(HttpResponse $response): void
    {
        $body = $response->body();

        $signatures = [
            'A PHP Error was encountered',
            '<title>Database Error</title>',
            '<title>Error</title>',
        ];

        foreach ($signatures as $signature) {
            if (! str_contains($body, $signature)) {
                continue;
            }

            // Extract the human-readable detail from the error page.
            if (preg_match('/<p>(?:Message:|Severity:)?\s*(.*?)<\/p>/si', $body, $m)) {
                $detail = trim(strip_tags($m[1]));
            } elseif (preg_match('/<div[^>]+id=["\']body["\'][^>]*>(.*?)<\/div>/si', $body, $m)) {
                $detail = trim(strip_tags($m[1]));
            } else {
                $detail = mb_substr(strip_tags($body), 0, 400);
            }

            self::fail('Application error in response [HTTP ' . $response->statusCode() . ']: ' . $detail);
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
        $trimmed = mb_ltrim($uri, '/');

        return '/' . $trimmed;
    }
}
