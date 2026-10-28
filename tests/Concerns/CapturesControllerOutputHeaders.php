<?php

namespace Tests\Concerns;

use RuntimeException;

/**
 * Runs one request through tests/Integration/bin/request.php and reports the headers the CONTROLLER
 * queued on CI_Output (via set_header()).
 *
 * AbstractTestCase::request() cannot do this: headers_list() is empty under the CLI SAPI, and the
 * runner appends bootstrap/kernel.php's $GLOBALS['ip_security_response_headers'] to every response.
 * Every header assertion made through request() is therefore satisfied by the kernel alone, even
 * when Base_Controller / Guest_Controller::setSecurityHeaders() is deleted. Reading
 * get_instance()->output->get_header() in a shutdown hook sees only what the controller itself set.
 */
trait CapturesControllerOutputHeaders
{
    /**
     * @param array<string, mixed> $post
     *
     * @return array{status: int, headers: array<string, string|null>}
     */
    protected function requestCapturingControllerHeaders(string $method, string $uri, array $post = []): array
    {
        $payload = [
            'method'  => mb_strtoupper($method),
            'uri'     => '/' . ltrim($uri, '/'),
            'query'   => [],
            'post'    => $post,
            'cookies' => [],
            'session' => $this->sessionData,
            'env'     => $this->environmentData,
            'server'  => $this->serverData,
            'files'   => [],
            'ajax'    => false,
        ];

        $this->resetDatabaseConnection();

        $prelude = <<<'PHP'
            register_shutdown_function(static function (): void {
                $names = ['X-Frame-Options', 'Content-Security-Policy', 'Referrer-Policy', 'X-Content-Type-Options'];
                $out   = [];
                if (function_exists('get_instance') && ($ci = get_instance()) && isset($ci->output)) {
                    foreach ($names as $name) {
                        $out[$name] = $ci->output->get_header($name);
                    }
                }
                fwrite(STDERR, "\n__CTRL_HEADERS__" . json_encode($out === [] ? null : $out) . "__CTRL_END__\n");
            });
            require $argv[1];
            PHP;

        $inheritedEnv = getenv();
        foreach (['DB_HOSTNAME', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_DRIVER'] as $key) {
            unset($inheritedEnv[$key]);
        }
        $environment = array_merge($inheritedEnv, [
            'CI_TEST_REQUEST' => base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $process = proc_open(
            [PHP_BINARY, '-r', $prelude, dirname(__DIR__) . '/Integration/bin/request.php'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__, 2),
            $environment,
        );

        if ( ! is_resource($process)) {
            throw new RuntimeException('Unable to start request process.');
        }

        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);

        if ( ! preg_match('/__CTRL_HEADERS__(.*?)__CTRL_END__/s', $stderr, $headerMatch)
            || ! preg_match('/__CI_TEST_RESULT_START__(.*?)__CI_TEST_RESULT_END__/s', $stdout, $resultMatch)) {
            throw new RuntimeException("Header-capturing runner produced no result for {$method} {$uri}. STDERR: {$stderr} STDOUT: " . mb_substr($stdout, 0, 500));
        }

        $headers = json_decode($headerMatch[1], true, 512, JSON_THROW_ON_ERROR);
        $result  = json_decode((string) base64_decode($resultMatch[1], true), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($headers, "No controller was instantiated for {$method} {$uri}; nothing to inspect.");

        return ['status' => (int) ($result['status'] ?? 0), 'headers' => $headers];
    }

    /** Guest user bound to exactly one client; returns [userId, clientId]. */
    protected function seedLoggedInGuest(): array
    {
        $clientId = $this->seedClient(['client_name' => 'Frame Guest Client']);
        $userId   = $this->databaseInsert('ip_users', [
            'user_name'          => 'frame_guest',
            'user_password'      => password_hash('guestpass', PASSWORD_DEFAULT),
            'user_email'         => 'frame-guest@test.local',
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);

        $this->actingAs([
            'user_id'       => $userId,
            'user_type'     => 2,
            'user_email'    => 'frame-guest@test.local',
            'user_name'     => 'frame_guest',
            'user_company'  => '',
            'user_language' => 'system',
        ]);

        return [$userId, $clientId];
    }
}
