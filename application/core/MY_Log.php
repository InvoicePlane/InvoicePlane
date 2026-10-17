<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * CodeIgniter 3's logger only knows ERROR, DEBUG, INFO and ALL. log_message('warning', ...) therefore
 * hit an undefined array key and the entry was silently dropped - including security events such as
 * blocked SVG uploads, login / password-reset rate limits and payment-gateway anomalies.
 *
 * WARNING shares ERROR's severity, so it is written at the production threshold (1) as well.
 */
class MY_Log extends CI_Log
{
    public function __construct()
    {
        $this->_levels['WARNING'] = $this->_levels['ERROR'];

        parent::__construct();
    }
}
