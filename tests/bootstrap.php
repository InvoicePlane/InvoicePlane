<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('CI_TESTING', true);

$basePath = dirname(__DIR__);

require_once $basePath . '/bootstrap/kernel.php';
require_once $basePath . '/tests/Integration/bootstrap.php';

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';
