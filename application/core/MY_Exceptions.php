<?php

defined('BASEPATH') || exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if (ENVIRONMENT === 'testing') {
            $text = is_array($message) ? implode(' | ', $message) : (string) $message;
            throw new RuntimeException($text, $status_code);
        }

        return parent::show_error($heading, $message, $template, $status_code);
    }
}
