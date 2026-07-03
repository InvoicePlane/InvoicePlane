<?php

defined('BASEPATH') || exit('No direct script access allowed');

enum RequestMethod: string
{
    case GET  = 'GET';
    case POST = 'POST';
}
