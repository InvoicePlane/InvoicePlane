<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */

/*
 * ---------------------------------------------------------------
 * Composer Loader
 * ---------------------------------------------------------------
 */
require(__DIR__ . '/../vendor/autoload.php');

/*
 * ---------------------------------------------------------------
 * IP Config Loader
 * ---------------------------------------------------------------
 * Loads the Dotenv, defines additional functions for it and
 * the IP Config file from the root directory
 */

if (!file_exists(__DIR__ . '/../ipconfig')) {
    exit("<div style='font-family:sans-serif;font-size:20px;color:#c33;text-align:center;'>The <b>ipconfig</b> file is missing! Please make a copy of the <b>ipconfig.example</b> file and rename it to <b>ipconfig</b> in your root directory.</div>");
}

$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../', 'ipconfig');
$dotenv->load();

/**
 * Small helper function to allow defaults for the getenv function
 *
 * @param string $env_key
 * @param mixed  $default
 *
 * @return mixed
 */
function env($env_key, $default = null)
{
    $value = getenv($env_key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (strlen($value) > 1
        && substr($value, 0, strlen('"')) === '"'
        && substr($value, -strlen('"')) === '"'
    ) {
        return substr($value, 1, -1);
    }

    return $value;
}
