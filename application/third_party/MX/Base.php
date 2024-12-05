<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/* load MX core classes */
require_once dirname(__FILE__) . '/Lang.php';
require_once dirname(__FILE__) . '/Config.php';

#[AllowDynamicProperties]
class CI extends CI_Controller
{
    public static $APP;

    public function __construct()
    {

        /* assign the application instance */
        self::$APP = $this;

        global $LANG, $CFG;

        /* re-assign language and config for modules */
        if (!$LANG instanceof MX_Lang) {
            $LANG = new MX_Lang;
        }
        if (!$CFG instanceof MX_Config) {
            $CFG = new MX_Config;
        }

        parent::__construct();
    }
}

/* create the application object */
new CI;
