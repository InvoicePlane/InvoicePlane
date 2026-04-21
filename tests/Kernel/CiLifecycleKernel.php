<?php

namespace Tests\Kernel;

abstract class CiLifecycleKernel extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_GET  = [];
        $_POST = [];
    }

    protected function get(string $uri, array $query = []): string
    {
        return $this->request('GET', $uri, $query, []);
    }

    protected function post(string $uri, array $data = []): string
    {
        return $this->request('POST', $uri, [], $data);
    }

    protected function request(string $method, string $uri, array $query, array $post): string
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        $_GET  = $query;
        $_POST = $post;

        ob_start();

        require dirname(__DIR__, 2) . '/index.php';

        return ob_get_clean();
    }
}
