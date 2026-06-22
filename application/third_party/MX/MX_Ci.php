<?php

use core\CiKernel;

class CI
{
    public static $APP;

    protected $kernel;

    public function __construct($kernel = null)
    {
        $this->kernel = $kernel ?: CiKernel::instance();

        self::$APP = $this->kernel;
    }

    public function app()
    {
        return $this->kernel;
    }
}
