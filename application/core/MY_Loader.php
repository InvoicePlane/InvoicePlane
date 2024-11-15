<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/* load the MX_Loader class */
require APPPATH.'third_party/MX/Loader.php';

#[AllowDynamicProperties]
class MY_Loader extends MX_Loader {}
