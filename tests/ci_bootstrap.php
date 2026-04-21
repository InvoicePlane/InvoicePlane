<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/kernel.php';

if ( ! defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}
