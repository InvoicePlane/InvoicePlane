<?php

namespace core;

class CiKernel
{
    private static ?self $instance = null;

    private static $ci = null;

    public static function http(): self
    {
        return self::boot('http');
    }

    public static function cli(): self
    {
        return self::boot('cli');
    }

    public static function testing(): self
    {
        return self::boot('testing');
    }

    public static function boot(string $context): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = new self();

        self::$instance->defineConstants($context);
        self::$instance->loadEnvOnce();
        self::$instance->loadCore();
        self::$instance->initCi();

        return self::$instance;
    }

    public function handleHttp(): void
    {
        self::$ci->run();
    }

    private function defineConstants(string $context): void
    {
        $base = dirname(__DIR__);

        $this->define('CI_CONTEXT', $context);

        $this->define('FCPATH', $base . '/');
        $this->define('APPPATH', $base . '/application/');
        $this->define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
        $this->define('VIEWPATH', APPPATH . 'views/');
    }

    private function loadEnvOnce(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $loaded = true;

        $path = dirname(__DIR__);

        if ( ! file_exists($path . '/ipconfig.php')) {
            return;
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($path, 'ipconfig.php');
        $dotenv->safeLoad();
    }

    private function loadCore(): void
    {
        if ( ! defined('CI_TESTING')) {
            require_once BASEPATH . 'core/CodeIgniter.php';
            exit;
        }

        if (defined('CI_TESTING')) {
            // prevent routing execution during phpunit bootstrap
            return;
        }
    }

    private function initCi(): void
    {
        self::$ci = get_instance();
    }

    private function define(string $key, mixed $value): void
    {
        if ( ! defined($key)) {
            define($key, $value);
        }
    }
}
