<?php

namespace Tests;

use core\CiKernel;

abstract class CiTestCase extends AbstractTestCase
{
    /** @var \CI_Controller|null */
    protected $CI;

    protected function setUp(): void
    {
        parent::setUp();

        CiKernel::boot('testing');

        $this->CI = & get_instance();

        $_GET    = [];
        $_POST   = [];
        $_SERVER = [];
    }

    /**
     * Skip the current test if no database connection is available.
     * Tests that perform DB inserts/queries should call this first.
     */
    protected function skipWithoutDatabase(): void
    {
        if ($this->CI === null) {
            $this->markTestSkipped('CI3 instance not available.');
        }

        try {
            $this->CI->db->query('SELECT 1');
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database unavailable: ' . $e->getMessage());
        }
    }

    protected function ci(): self
    {
        return $this;
    }

    protected function get(string $uri, array $query = []): Integration\Support\HttpResponse
    {
        return $this->request('GET', $uri, $query);
    }
}
