<?php

use core\CiKernel;

class MX_Router extends CI_Router
{
    public function locate($segments)
    {
        $ci = $this->ci();

        $this->module = null;

        return parent::locate($segments);
    }

    public function fetch_module()
    {
        return $this->module;
    }

    protected function ci()
    {
        return CiKernel::instance();
    }
}
