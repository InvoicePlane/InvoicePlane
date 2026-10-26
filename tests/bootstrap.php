<?php

define('CI_TESTING', true);
define('CI_INTEGRATION_TESTING', true);

$basePath = dirname(__DIR__);
define('ROOT_PATH', $basePath);

require_once $basePath . '/bootstrap/kernel.php';

// Isolated unit tests load application libraries without the CodeIgniter
// request lifecycle. Keep application logging calls harmless in that context.
if ( ! function_exists('log_message')) {
    function log_message(string $level, string $message): void {}
}

require_once $basePath . '/tests/Support/UnitCodeIgniter.php';

// CI3 gateway libraries have no namespace — PSR-4 autoloading won't find them.
require_once $basePath . '/application/libraries/gateways/PaypalLib.php';

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';
