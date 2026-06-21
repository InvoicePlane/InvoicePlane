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

    /**
     * In test subprocesses, CI3's parent show_404() would throw a RuntimeException
     * (because ENVIRONMENT=testing) which propagates out before http_response_code(404)
     * is set. Bypass that: emit the 404 status and render the 404 template normally so
     * tests can assert $response->statusCode() === 404.
     */
    public function show_404($page = '', $log_error = true)
    {
        if (defined('CI_TEST_SUBPROCESS')) {
            if ($log_error) {
                log_message('error', '404 Page Not Found --> ' . $page);
            }

            // Subprocesses run as CLI, so CI3's set_status_header() and HTML template
            // selection are both no-ops. Set the code directly and render the HTML
            // error template so the test runner captures a meaningful response body.
            http_response_code(404);

            $templates_path = config_item('error_views_path');
            if (empty($templates_path)) {
                $templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
            } else {
                $templates_path = rtrim($templates_path, '/\\') . DIRECTORY_SEPARATOR;
            }

            $heading = '404 Page Not Found';
            $message = '<p>The page you requested was not found.</p>';

            ob_start();
            include $templates_path . 'html' . DIRECTORY_SEPARATOR . 'error_404.php';
            echo ob_get_clean();

            exit(4);
        }

        parent::show_404($page, $log_error);
    }
}
