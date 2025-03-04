<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/* load the MX_Router class */
require APPPATH . "third_party/MX/Router.php";

#[AllowDynamicProperties]
class MY_Router extends MX_Router
{

}
