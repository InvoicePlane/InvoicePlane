<?php

namespace Tests\Kernel;

use PHPUnit\Framework\TestCase;

abstract class MxTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MxKernel::boot();

        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    }

    protected function ci(): mixed
    {
        return get_instance();
    }

    protected function get(string $uri): string
    {
        return $this->dispatch('GET', $uri);
    }

    protected function post(string $uri, array $data = []): string
    {
        return $this->dispatch('POST', $uri, $data);
    }

    private function dispatch(string $method, string $uri, array $data = []): string
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;

        $_GET = $method === 'GET' ? $data : [];
        $_POST = $method === 'POST' ? $data : [];

        ob_start();

        require dirname(__DIR__, 2) . '/index.php';

        return ob_get_clean();
    }
}
