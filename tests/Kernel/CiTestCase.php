<?php

namespace Tests\Kernel;

use CI_Controller;
use Exception;

abstract class CiTestCase extends \PHPUnit\Framework\TestCase
{
    protected CI_Controller $ci;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ci = &get_instance();

        if ( ! $this->ci) {
            throw new Exception('CI instance not available');
        }

        $this->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->rollbackTransaction();

        parent::tearDown();
    }

    protected function get(string $uri, array $params = []): mixed
    {
        return $this->request('GET', $uri, $params);
    }

    protected function post(string $uri, array $params = []): mixed
    {
        return $this->request('POST', $uri, $params);
    }

    protected function request(string $method, string $uri, array $params = []): mixed
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        $_GET  = $method === 'GET' ? $params : [];
        $_POST = $method === 'POST' ? $params : [];

        return $this->dispatch($uri);
    }

    protected function dispatch(string $uri): mixed
    {
        $segments = array_values(array_filter(explode('/', mb_trim($uri, '/'))));

        $controllerName = $segments[0] ?? 'home';
        $method         = $segments[1] ?? 'index';
        $params         = array_slice($segments, 2);

        $controllerClass = $this->resolveController($controllerName);

        if ( ! class_exists($controllerClass)) {
            throw new Exception("Controller not found: {$controllerClass}");
        }

        $controller = new $controllerClass();

        if ( ! method_exists($controller, $method)) {
            throw new Exception("Method not found: {$method}");
        }

        return $controller->{$method}(...$params);
    }

    protected function resolveController(string $name): string
    {
        $name = ucfirst($name);

        return 'CI_Controller_' . $name;
    }

    protected function beginTransaction(): void
    {
        if (isset($this->ci->db)) {
            $this->ci->db->trans_begin();
        }
    }

    protected function rollbackTransaction(): void
    {
        if (isset($this->ci->db) && $this->ci->db->trans_status() !== false) {
            $this->ci->db->trans_rollback();
        }
    }
}
