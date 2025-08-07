<?php

// Enable debug mode if set
define('IP_DEBUG', env_bool('ENABLE_DEBUG'));

/**
 * Small helper function to allow defaults for the getenv function.
 *
 * @param string $env_key
 * @param mixed  $default
 *
 * @return mixed
 */
function env($env_key, $default = null)
{
    if (isset($_ENV[$env_key])) {
        return $_ENV[$env_key];
    }

    return $default;
}

/**
 * Small helper function to get bool values for the env setting.
 *
 * @param string $env_key
 * @param string $default
 */
function env_bool($env_key, $default = 'false'): bool
{
    return env($env_key, $default) === 'true';
}

/**
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('ENVIRONMENT', $_SERVER['CI_ENV'] ?? 'development');

/**
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}
