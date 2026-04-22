<?php

namespace Tests;

use core\CiKernel;

abstract class CiTestCase extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CiKernel::boot();

        $_GET    = [];
        $_POST   = [];
        $_SERVER = [];
    }

    protected function ci()
    {
        return CiKernel::instance();
    }

    protected function get(string $uri, array $query = []): mixed
    {
        return $this->request('GET', $uri, $query);
    }

    protected function post(string $uri, array $data = []): mixed
    {
        return $this->request('POST', $uri, $data);
    }

    protected function request(string $method, string $uri, array $payload = []): mixed
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        $_GET  = $method === 'GET' ? $payload : [];
        $_POST = $method === 'POST' ? $payload : [];

        ob_start();

        $ci = CiKernel::instance();
        $ci->router->_parse_request($uri);
        $ci->run();

        return ob_get_clean();
    }
}
