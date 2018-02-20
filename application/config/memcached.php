<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Your Memcached servers can be specified below.
|
|	See: https://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/
$config = [
    'default' => [
        'hostname' => env('MEMCACHED_HOST', '127.0.0.1'),
        'port' => env('MEMCACHED_PORT', '11211'),
        'weight' => env('MEMCACHED_WEIGHT', '1'),
    ],
];
