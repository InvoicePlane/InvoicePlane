<?php

namespace Tests\Kernel;

use InvalidArgumentException;
use RuntimeException;

class MxDispatcher
{
    public function dispatch(string $uri, string $method = 'GET', array $payload = []): mixed
    {
        $this->setGlobals([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri,
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php',
        ]);

        [$module, $controller, $action] = $this->parseUri($uri);

        $class = ucfirst($controller);

        if (!class_exists($class)) {
            throw new RuntimeException("Controller not found: {$class}");
        }

        $instance = new $class();

        if (!method_exists($instance, $action)) {
            throw new RuntimeException("Method not found: {$action}");
        }

        return $instance->{$action}();
    }

    public function setGlobals(array|string $globals): void
    {
        if (!is_array($globals)) {
            throw new InvalidArgumentException(
                'MxDispatcher::setGlobals expects array, ' . gettype($globals) . ' given'
            );
        }

        foreach ($globals as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    private function parseUri(string $uri): array
    {
        $uri = trim($uri, '/');

        if ($uri === '') {
            return ['clients', 'clients', 'index'];
        }

        $segments = explode('/', $uri);

        return [
            $segments[0] ?? 'clients',
            $segments[1] ?? $segments[0] ?? 'clients',
            $segments[2] ?? 'index',
        ];
    }
}
