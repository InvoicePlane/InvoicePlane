<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

class Get extends Base_Controller
{
    public $targetPath = UPLOADS_CFILES_FOLDER; // UPLOADS_FOLDER . 'customer_files/'

    public $ctype_default = 'application/octet-stream';

    public $content_types = [];

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');
        $this->load->model('upload/mdl_uploads');
        $this->content_types = $this->mdl_uploads->content_types;
    }

    public function show_files($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = $this->mdl_uploads->get_files($url_key)) {
            exit('{}');
        }

        echo json_encode($result);
        exit;
    }

    public function get_file($filename): void
    {
        // Security: Use comprehensive file security validation helper
        // Note: CodeIgniter already URL-decodes parameters during routing
        $validation = validate_file_access($filename, $this->targetPath);
        
        if (!$validation['valid']) {
            $errorMap = [
                'file_not_found' => [404, 'upload_error_file_not_found', 'File not found'],
                'path_outside_directory' => [403, 'upload_error_unauthorized_access', 'Unauthorized access'],
            ];
            
            $error = $validation['error'] ?? 'unknown';
            $response = $errorMap[$error] ?? [400, 'upload_error_invalid_filename', 'Invalid filename'];
            
            $this->respond_message($response[0], $response[1], $response[2]);
        }

        $realFile = $validation['path'];
        $safeFilename = $validation['basename'];
        
        $path_parts = pathinfo($realFile);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size = filesize($realFile);
        
        // Security: Sanitize filename for Content-Disposition header to prevent header injection
        $sanitizedFilename = sanitize_filename_for_header($safeFilename);
        
        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $sanitizedFilename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);
        readfile($realFile);
    }

    private function respond_message(int $httpCode, string $messageKey, string $dynamicLogValue = ''): void
    {
        log_message('debug', 'guest/get: ' . trans($messageKey) . ': (status ' . $httpCode . ') ' . $dynamicLogValue);
        http_response_code($httpCode);
        _trans($messageKey);
        exit;
    }
}
