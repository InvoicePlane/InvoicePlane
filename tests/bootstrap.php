<?php

/*
 * InvoicePlane test bootstrap.
 *
 * InvoicePlane is CodeIgniter 3, which has no test-friendly container, so the unit
 * suite does not boot the framework. It covers plain function libraries that are
 * written to be free of CodeIgniter state. Those files guard on BASEPATH to block
 * direct web access, so the constant is defined here to let them be required.
 */

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/../vendor/pocketarc/codeigniter/system/');
}
