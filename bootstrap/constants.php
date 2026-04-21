<?php

$base = dirname(__DIR__);

define('ENVIRONMENT', $_SERVER['CI_ENV'] ?? 'testing');

define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
define('APPPATH', $base . '/application/');
define('FCPATH', $base . '/');
define('VIEWPATH', APPPATH . 'views/');
