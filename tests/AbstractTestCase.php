<?php

namespace Tests;

use function base_path;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractTestCase extends PHPUnitTestCase
{
    protected mixed $application;

    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('bootstrap/app.php');

        $this->ci = &get_instance();

        $_GET    = [];
        $_POST   = [];
        $_SERVER = [];
    }

    /**
     * @return array<int, string>
     */
    protected function discoverPublicControllerActions(string $controllerClass): array
    {
        $reflection = new ReflectionClass($controllerClass);
        $actions    = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $controllerClass) {
                continue;
            }

            if ($method->isConstructor() || str_starts_with($method->getName(), '_')) {
                continue;
            }

            $actions[] = $method->getName();
        }

        sort($actions);

        return $actions;
    }

    /**
     * @return array{verb: string, uri: string}
     */
    protected function guessRouteForControllerAction(string $controllerClass, string $action): array
    {
        $reflection = new ReflectionClass($controllerClass);
        $module     = mb_strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', explode('\\', $reflection->getNamespaceName())[1] ?? 'core'));
        $controller = mb_strtolower(str_replace('Controller', '', $reflection->getShortName()));

        $verb = 'GET';

        if (str_contains($reflection->getShortName(), 'AjaxController')) {
            $verb = 'POST';
        } elseif ($action === 'form') {
            $verb = 'GET';
        } elseif (preg_match('/^(save|store|create|update|delete|remove|insert)/i', $action) === 1) {
            $verb = 'POST';
        }

        return [
            'verb' => $verb,
            'uri'  => $module . '/' . $controller . '/' . $action,
        ];
    }

    /**
     * @return array<int, array{action: string, verb: string, uri: string}>
     */
    protected function discoverRouteDefinitionsForController(string $controllerClass): array
    {
        $reflection     = new ReflectionClass($controllerClass);
        $controllerFile = $reflection->getFileName();

        if ($controllerFile === false) {
            return [];
        }

        // Walk up from controller file to find the module root (directory containing routes/)
        $dir        = dirname($controllerFile);
        $moduleRoot = null;

        while ($dir !== dirname($dir)) {
            if (is_dir($dir . '/routes')) {
                $moduleRoot = $dir;
                break;
            }
            $dir = dirname($dir);
        }

        if ($moduleRoot === null) {
            return [];
        }

        $routeFileName = mb_strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Controller', '', $reflection->getShortName())));
        $routeFile     = $moduleRoot . '/routes/' . $routeFileName . '.php';

        if ( ! is_file($routeFile)) {
            return [];
        }

        $contents = (string) file_get_contents($routeFile);
        $routes   = [];

        if (preg_match_all("/Route::(get|post)\\(\\s*'([^']+)',\\s*\\[[^\\]]+::class,\\s*'([^']+)'\\]\\)/i", $contents, $matches, PREG_SET_ORDER) === false) {
            return [];
        }

        foreach ($matches as $match) {
            $routes[] = [
                'action' => $match[3],
                'verb'   => mb_strtoupper($match[1]),
                'uri'    => $match[2],
            ];
        }

        return $routes;
    }

    protected function routeDefinitionExists(array $routes, string $verb, string $uri, string $action): bool
    {
        foreach ($routes as $route) {
            if ($route['verb'] === $verb && $route['uri'] === $uri && $route['action'] === $action) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    protected function extractRequiredFieldsFromServiceFile(string $serviceFile): array
    {
        $contents = (string) file_get_contents($serviceFile);
        $required = [];

        if (preg_match_all("/'field'\\s*=>\\s*'([^']+)'[\\s\\S]*?'rules'\\s*=>\\s*'[^']*required[^']*'/m", $contents, $matches) !== false) {
            $required = $matches[1];
        }

        return array_values(array_unique($required));
    }

    protected function validateRequiredFields(array $payload, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if ( ! isset($payload[$field]) || $payload[$field] === '') {
                return false;
            }
        }

        return true;
    }
}

if ( ! function_exists('trans')) {
    function trans(string $key): string
    {
        return $key;
    }
}

if ( ! function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return $default;
    }
}
