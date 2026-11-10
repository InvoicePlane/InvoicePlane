<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * bootstrap/test-server.php is the router behind the E2E workflow's "php -S ... -t . bootstrap/test-server.php".
 *
 * Static files live in public/ since the web root moved there, but the workflow's document root is the project
 * root. The router therefore has to send an existing static file itself: returning false would make PHP look it
 * up under the document root (./assets, ./favicon.ico), find nothing and answer 404, so every page loaded
 * without its stylesheet, jQuery or Dropzone.
 */
#[Group('security')]
final class TestServerStaticFilesTest extends TestCase
{
    /** @var resource|null */
    private $server;

    private int $port = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $root   = dirname(__DIR__, 3);
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertNotFalse($socket, 'A free port is needed for the test server.');
        $this->port = (int) substr((string) strrchr((string) stream_socket_get_name($socket, false), ':'), 1);
        fclose($socket);

        $pipes        = [];
        $this->server = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . $this->port, '-t', $root, $root . '/bootstrap/test-server.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root
        );
        self::assertIsResource($this->server, 'The test server must start.');

        for ($i = 0; $i < 50; $i++) {
            $connection = @fsockopen('127.0.0.1', $this->port, $errno, $errstr, 0.2);
            if ($connection !== false) {
                fclose($connection);

                return;
            }
            usleep(100000);
        }

        self::fail('The test server did not start listening.');
    }

    protected function tearDown(): void
    {
        if (is_resource($this->server)) {
            proc_terminate($this->server);
            proc_close($this->server);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_serves_a_static_file_from_public_with_its_content_type(): void
    {
        /* Act */
        [$status, $type, $body] = $this->fetch('/favicon.ico');

        /* Assert */
        self::assertSame(200, $status, 'public/favicon.ico must be served although the document root is the project root.');
        self::assertStringContainsString('image/', $type);
        self::assertSame((string) file_get_contents(dirname(__DIR__, 3) . '/public/favicon.ico'), $body);
    }

    #[Test]
    public function it_serves_a_text_file_from_public(): void
    {
        /* Act */
        [$status, $type] = $this->fetch('/robots.txt');

        /* Assert */
        self::assertSame(200, $status);
        self::assertStringContainsString('text/plain', $type);
    }

    #[Test]
    public function it_never_serves_a_php_file_or_anything_outside_the_web_roots(): void
    {
        /* Act */
        $source = $this->fetch('/bootstrap/test-server.php');
        $config = $this->fetch('/ipconfig.php.example');
        $up     = $this->fetch('/%2e%2e/%2e%2e/etc/passwd');

        /* Assert: PHP source never leaves the server, and the project root only exposes what production maps (uploads/) */
        self::assertStringNotContainsString('send_static_file', $source[2], 'A PHP file must be routed, not sent as text.');
        self::assertStringNotContainsString('IP_URL', $config[2], 'Files in the project root are not static assets.');
        self::assertStringNotContainsString('root:', $up[2], 'A path above the root must never be served.');
    }

    /**
     * @return array{0: int, 1: string, 2: string} status, content type, body
     */
    private function fetch(string $path): array
    {
        $context = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
        $body    = (string) @file_get_contents('http://127.0.0.1:' . $this->port . $path, false, $context);
        $status  = 0;
        $type    = '';

        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $m)) {
                $status = (int) $m[1];
            } elseif (stripos($header, 'Content-Type:') === 0) {
                $type = trim(substr($header, 13));
            }
        }

        return [$status, $type, $body];
    }
}
