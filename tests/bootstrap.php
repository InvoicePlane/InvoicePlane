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

// session_credential_fingerprint() calls config_item('encryption_key'); if that
// returns null in the parent process the HMAC mismatches the subprocess's value
// and every actingAsAdmin() feature test gets a 307 redirect instead of 200.
$GLOBALS['unitCiConfig'] = array_merge($GLOBALS['unitCiConfig'] ?? [], [
    'encryption_key'   => $_ENV['ENCRYPTION_KEY'] ?? null,
    'csrf_token_name'  => $_ENV['CSRF_TOKEN_NAME'] ?? '_ip_csrf',
    'csrf_cookie_name' => $_ENV['CSRF_COOKIE_NAME'] ?? 'ip_csrf_cookie',
]);

// CI3 gateway libraries have no namespace — PSR-4 autoloading won't find them.
require_once $basePath . '/application/libraries/gateways/PaypalLib.php';

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';
