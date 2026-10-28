<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * In the testing environment show_error() throws instead of rendering the error view and exiting,
 * so the functional test harness can observe the status code and message.
 */
class MY_Exceptions extends CI_Exceptions
{
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'testing') {
            $message = is_array($message) ? implode(' ', $message) : (string) $message;

            throw new RuntimeException(strip_tags($message), (int) $status_code);
        }

        return parent::show_error($heading, $message, $template, $status_code);
    }
}
