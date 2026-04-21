<?php

define('CI_HTTP_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/bootstrap/constants.php';
require __DIR__ . '/bootstrap/app.php';

\core\CiKernel::http()->handleHttp();
