<?php

namespace Tests\Kernel;

class MxDispatcher
{
    public function dispatch(string $uri, string $method = 'GET', array $payload = []): mixed
    {
        $this->loadMx(); // move here

        [$module, $controller, $action] = $this->parseUri($uri);

        $class = $this->resolveController($module, $controller);

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: {$class}");
        }

        $instance = new $class();

        if (!method_exists($instance, $action)) {
            throw new \RuntimeException("Method not found: {$action}");
        }

        $this->setGlobals($method, $uri, $payload);

        return $instance->{$action}();
    }

    private function loadMx(): void
    {
        require_once APPPATH . 'third_party/MX/Loader.php';
        require_once APPPATH . 'third_party/MX/Router.php';
        require_once APPPATH . 'third_party/MX/Controller.php';
        require_once APPPATH . 'third_party/MX/Modules.php';

        \Modules::$locations = [
            APPPATH . 'modules/' => APPPATH . 'modules/',
        ];
    }

    private function parseUri(string $uri): array
    {
        $parts = array_values(array_filter(explode('/', trim($uri, '/'))));

        return [
            $parts[0] ?? 'home',
            $parts[1] ?? 'index',
            $parts[2] ?? 'index',
        ];
    }

    private function resolveController(string $module, string $controller): string
    {
        $module     = ucfirst($module);
        $controller = ucfirst($controller);

        return "{$module}_controllers_{$controller}";
    }

    private function setGlobals(string $method, string $uri, array $payload): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        $_GET  = $method === 'GET' ? $payload : [];
        $_POST = $method === 'POST' ? $payload : [];
    }
}
