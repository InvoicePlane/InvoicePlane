<?php

namespace Tests\Kernel;

class MxKernel
{
    public static function boot(): void
    {
        self::defineConstants();
        self::loadAutoload();
        self::loadCiCore();
        self::loadMx();
        self::loadHelpers();
    }

    private static function defineConstants(): void
    {
        $base = dirname(__DIR__, 2);

        defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

        defined('FCPATH')   || define('FCPATH', $base . '/');
        defined('APPPATH')  || define('APPPATH', $base . '/application/');
        defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
        defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

        defined('IP_DEBUG') || define(
            'IP_DEBUG',
            filter_var($_ENV['ENABLE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
        );
    }

    private static function loadAutoload(): void
    {
        // already handled by bootstrap.php
    }

    private static function loadCiCore(): void
    {
        require_once BASEPATH . 'core/Common.php';
        require_once BASEPATH . 'core/Exceptions.php';
        require_once BASEPATH . 'core/Benchmark.php';
        require_once BASEPATH . 'core/Hooks.php';
        require_once BASEPATH . 'core/Config.php';
        require_once BASEPATH . 'core/Utf8.php';
        require_once BASEPATH . 'core/URI.php';
        require_once BASEPATH . 'core/Router.php';
        require_once BASEPATH . 'core/Output.php';
        require_once BASEPATH . 'core/Input.php';
        require_once BASEPATH . 'core/Lang.php';
        require_once BASEPATH . 'core/Controller.php';
        require_once BASEPATH . 'core/Loader.php';
    }

    private static function loadMx(): void
    {
        require_once APPPATH . 'third_party/MX/Modules.php';
        require_once APPPATH . 'third_party/MX/Loader.php';
        require_once APPPATH . 'third_party/MX/Controller.php';
        require_once APPPATH . 'third_party/MX/Router.php';

        \Modules::$locations = [
            APPPATH . 'modules/' => APPPATH . 'modules/',
        ];
    }

    private static function loadHelpers(): void
    {
        $file = dirname(__DIR__, 2) . '/bootstrap/helpers.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
}
