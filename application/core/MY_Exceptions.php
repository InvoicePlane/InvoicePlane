<?php

class MY_Exceptions extends CI_Exceptions
{
    /**
     * In test subprocesses, convert application errors into throwable exceptions
     * so PHPUnit sees the real error instead of a silent HTTP 200 with an error page.
     *
     * 404 pages are exempt — tests legitimately assert on 404 responses.
     */
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if (defined('CI_TEST_SUBPROCESS') && $template !== 'error_404') {
            $text = is_array($message) ? implode(' | ', $message) : $message;
            throw new RuntimeException('[CI3 ' . $heading . '] ' . strip_tags($text));
        }

        return parent::show_error($heading, $message, $template, $status_code);
    }

    public function show_404($page = '', $log_error = true)
    {
        if (defined('CI_TESTING')) {
            throw new RuntimeException('CI 404 triggered: ' . $page);
        }

        parent::show_404($page, $log_error);
    }
}
