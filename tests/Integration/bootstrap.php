<?php

use Dotenv\Dotenv;

define('CI_INTEGRATION_TESTING', true);

$basePath = dirname(__DIR__, 2);

require_once $basePath . '/vendor/autoload.php';

if (file_exists($basePath . '/ipconfig.php')) {
    Dotenv::createImmutable($basePath, 'ipconfig.php')->safeLoad();
}
