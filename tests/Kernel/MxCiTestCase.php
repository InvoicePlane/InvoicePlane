<?php

namespace Tests\Kernel;

use Exception;

abstract class MxCiTestCase extends CiTestCase
{
    protected function dispatch(string $uri): mixed
    {
        $segments = array_values(array_filter(explode('/', mb_trim($uri, '/'))));

        $module     = $segments[0] ?? 'home';
        $controller = $segments[1] ?? 'index';
        $method     = $segments[2] ?? 'index';

        $class = $this->resolveMxController($module, $controller);

        if ( ! class_exists($class)) {
            throw new Exception("MX Controller not found: {$class}");
        }

        $instance = new $class();

        if ( ! method_exists($instance, $method)) {
            throw new Exception("Method not found: {$method}");
        }

        return $instance->{$method}();
    }

    protected function resolveMxController(string $module, string $controller): string
    {
        $module     = ucfirst($module);
        $controller = ucfirst($controller);

        return "{$module}_controllers_{$controller}";
    }
}
