<?php

$_SERVER['CI_ENV'] = 'testing';
putenv('CI_ENV=testing');

require_once dirname(__DIR__) . '/bootstrap/bootstrap.php';
