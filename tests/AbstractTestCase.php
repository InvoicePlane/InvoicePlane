<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Kernel\MxDispatcher;

abstract class AbstractTestCase extends TestCase
{
    protected MxDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new MxDispatcher();
    }

    protected function get(string $uri, array $query = []): mixed
    {
        return $this->dispatcher->dispatch($uri, 'GET', $query);
    }

    protected function post(string $uri, array $data = []): mixed
    {
        return $this->dispatcher->dispatch($uri, 'POST', $data);
    }
}
