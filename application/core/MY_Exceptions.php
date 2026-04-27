<?php

class MY_Exceptions extends CI_Exceptions
{
    public function show_404($page = '', $log_error = true)
    {
        if (defined('CI_TESTING')) {
            throw new RuntimeException('CI 404 triggered: ' . $page);
        }

        parent::show_404($page, $log_error);
    }
}
