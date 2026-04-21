<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Integration\Support\HttpResponse;

abstract class CiIntegrationTestCase extends TestCase
{
    protected function get(string $uri, array $query = []): HttpResponse
    {
        return $this->request('GET', $uri, $query, []);
    }

    protected function post(string $uri, array $data = [], array $query = []): HttpResponse
    {
        return $this->request('POST', $uri, $query, $data);
    }

    protected function request(string $method, string $uri, array $query = [], array $post = []): HttpResponse
    {
        $payload = [
            'method' => strtoupper($method),
            'uri'    => '/' . mb_ltrim($uri, '/'),
            'query'  => $query,
            'post'   => $post,
        ];

        $command = sprintf('php %s', escapeshellarg(dirname(__DIR__) . '/Integration/bin/request.php'));

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptorSpec,
            $pipes,
            dirname(__DIR__, 2),
            [
                'CI_TEST_REQUEST' => base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR)),
            ],
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
}
