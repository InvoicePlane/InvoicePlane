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
        $filename = urldecode($filename);
        // Prevent directory traversal
        $safeFilename = basename($filename); // Remove any path components
        $fullPath     = $this->targetPath . $safeFilename;
        $realBase     = realpath($this->targetPath);
        if ($realBase === false) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
            $this->respond_message(404, 'upload_error_file_not_found', $this->targetPath . $ref);
        }
        // Ensure $realBase ends with a separator for robust comparison
        $realBaseWithSep = rtrim($realBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $realFile     = realpath($fullPath);
        // Check that the file exists and is within the allowed directory
        if (
            $realFile === false ||
            (substr($realFile, 0, strlen($realBaseWithSep)) !== $realBaseWithSep) ||
            ! file_exists($realFile)
        ) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
            $this->respond_message(404, 'upload_error_file_not_found', $fullPath . $ref);
        }
        $path_parts = pathinfo($realFile);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size  = filesize($realFile);
        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $safeFilename . '"');
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
