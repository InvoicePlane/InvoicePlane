<?php

/*
 * Global-scope stubs that let Mdl_Templates be exercised in an isolated unit
 * test without booting the full CodeIgniter stack.
 *
 * Mdl_Templates extends the framework's CI_Model and, on the custom-template
 * path, calls log_message() and (once the ipconfig wiring is fixed) the env()
 * helper. None of those are autoloaded, so the class cannot even be required
 * without them. The definitions below mirror the real behaviour just enough
 * for a unit test:
 *
 *  - env() reads $_ENV, exactly like the real helper in index.php, which is
 *    where Dotenv::createImmutable() places every ipconfig.php value.
 */

if ( ! class_exists('CI_Model')) {
    class CI_Model
    {
    }
}

if ( ! function_exists('log_message')) {
    function log_message($level, $message)
    {
        // No-op: logging is irrelevant to the template-listing behaviour under test.
    }
}

if ( ! function_exists('env')) {
    /**
     * Mirror of the env() helper defined in index.php.
     *
     * Dotenv::createImmutable() loads ipconfig.php into $_ENV, so this is the
     * canonical way application code reads an ipconfig value.
     */
    function env($env_key, $default = null)
    {
        return $_ENV[$env_key] ?? $default;
    }
}
