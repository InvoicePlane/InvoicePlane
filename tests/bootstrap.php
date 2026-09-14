<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('CI_TESTING', true);

$basePath = dirname(__DIR__);

// Guard against the "masked DB run". When DB_* are exported into the OS env
// (a CI job-level `env:`, a local `.envrc`/`direnv`, `export DB_*=...`) but not
// into $_ENV — PHP CLI's default variables_order (GPCS) has no 'E' — phpdotenv's
// createImmutable in bootstrap/kernel.php sees them as already set and never
// copies the ipconfig.php values in. The app's env() helper reads only $_ENV,
// finds nothing, and every DB-backed test connects as ''@'localhost',
// markTestSkipped()s, and the run turns green having proved nothing (~600
// skips). Mirror any OS-env DB_* into $_ENV *before* kernel.php loads so env()
// and phpdotenv's skip agree on one value; a wrong value then fails loudly
// instead of skipping. This is belt-and-braces with the CI workflow's
// `env -u DB_*` and Makefile's PHPUNIT_ENV_CLEAN, and a no-op when neither
// applies.
foreach (['DB_HOSTNAME', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_DRIVER'] as $dbEnvKey) {
    $dbEnvValue = getenv($dbEnvKey);
    if ($dbEnvValue !== false && ! array_key_exists($dbEnvKey, $_ENV)) {
        $_ENV[$dbEnvKey] = $dbEnvValue;
    }
}
unset($dbEnvKey, $dbEnvValue);

require_once $basePath . '/bootstrap/kernel.php';
require_once $basePath . '/tests/Integration/bootstrap.php';

// Isolated unit tests load application libraries without the CodeIgniter
// request lifecycle. Keep application logging calls harmless in that context.
if ( ! function_exists('log_message')) {
    function log_message(string $level, string $message): void {}
}

require_once $basePath . '/tests/Support/UnitCodeIgniter.php';

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';
