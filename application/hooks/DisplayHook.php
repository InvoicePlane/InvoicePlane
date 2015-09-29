<?php

class DisplayHook
{
    public $CI;

    public function captureOutput()
    {
        // $this->CI =& get_instance();
        // $output = $this->CI->output->get_output();
        if (ENVIRONMENT == 'testing') {
            log_message('info', 'captureOutput - Testing');
        } else {
            $OUT =& load_class('Output', 'core');
            // like in /system/core/CodeIgniter.php line 381
            $OUT->_display();
            // echo $output;
        }
    }
}
