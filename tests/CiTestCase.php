<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
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

    protected function get(string $uri, array $query = []): Integration\Support\HttpResponse
    {
        return $this->request('GET', $uri, $query);
    }
}
