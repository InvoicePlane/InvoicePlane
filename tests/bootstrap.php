<?php

use Tests\Kernel\TestKernel;

define('CI_TESTING', true);

$base = dirname(__DIR__);

require_once $base . '/vendor/autoload.php';

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

define('APPPATH', $base . '/application/');
define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
define('VIEWPATH', APPPATH . 'views/');

require_once $base . '/tests/Kernel/TestKernel.php';

TestKernel::boot();
