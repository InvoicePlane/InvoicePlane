<?php

use core\CiKernel;

class MX_Loader extends CI_Loader
{
    protected $kernel;

    public function __construct($kernel = null)
    {
        $this->kernel = $kernel ?: CiKernel::instance();

        parent::__construct();
    }

    public function __get($key)
    {
        return $this->kernel->{$key};
    }

    public function config($file, $use_sections = false, $fail_gracefully = false)
    {
        return $this->kernel->config->load($file, $use_sections, $fail_gracefully);
    }

    public function database($params = '', $return = false, $query_builder = null)
    {
        $ci = $this->kernel;

        require_once BASEPATH . 'database/DB.php';

        if ($return) {
            return DB($params, $query_builder);
        }

        $ci->db = DB($params, $query_builder);

        return $this;
    }

    protected function ci()
    {
        return $this->kernel;
    }
}
