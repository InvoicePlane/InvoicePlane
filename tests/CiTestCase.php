<?php

namespace Tests;

use core\CiKernel;

abstract class CiTestCase extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CiKernel::boot('testing');

        $_GET    = [];
        $_POST   = [];
        $_SERVER = [];
    }

    protected function ci()
    {
        return CiKernel::instance();
    }

    protected function get(string $uri, array $query = []): Integration\Support\HttpResponse
    {
        return $this->request('GET', $uri, $query);
    }
}
