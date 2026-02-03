<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Upload extends Admin_Controller
{
    public $targetPath = UPLOADS_CFILES_FOLDER; // UPLOADS_FOLDER . 'customer_files/';

    public $ctype_default = 'application/octet-stream';

    public $content_types = [];

    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'gif', 'webp'];

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

    public function upload_file(int $customerId, string $url_key): void
    {
        if (empty($_FILES['file']['name'])) {
            $this->respond_message(400, 'upload_error_no_file');
        }

        $originalFilename = $_FILES['file']['name'];
        $fileName         = $this->sanitize_file_name($originalFilename);
        $file_ext         = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $filePath         = $this->get_target_file_path($url_key, $fileName);

        if (file_exists($filePath)) {
            $this->respond_message(409, 'upload_error_duplicate_file', $fileName);
        }

        $tempFile = $_FILES['file']['tmp_name'];
        if (extension_loaded('fileinfo')) {
            $this->validate_mime_type(mime_content_type($tempFile));
        }

        if ( ! in_array($file_ext, $this->allowed_extensions, true)) {
            $this->respond_message(400, 'upload_error_invalid_extension', $file_ext);
        }

        $this->move_uploaded_file($tempFile, $filePath, $fileName);

        $this->save_file_metadata($customerId, $url_key, $fileName);

        $this->respond_message(200, 'upload_file_uploaded_successfully', $fileName);
    }

    public function create_dir($path, $chmod = '0755'): bool
    {
        if ( ! is_dir($path) && ! is_link($path)) {
            return mkdir($path, $chmod);
        }

        return true;
    }

    public function show_files($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = $this->mdl_uploads->get_files($url_key)) {
            exit('{}');
        }

        exit(json_encode($result));
    }

    public function delete_file(string $url_key): void
    {
        // Security: Get POST data (CodeIgniter already handles basic decoding)
        // Note: Removed urldecode() to prevent double-decoding vulnerability
        $filename = $this->input->post('name');

        // Security: Sanitize filename to prevent path traversal
        $filename = $this->sanitize_file_name($filename);

        if (empty($filename)) {
            // Use hash for secure logging
            $filenameHash = hash_for_logging($this->input->post('name') ?: '');
            log_message('error', 'upload: Invalid filename in delete request (hash: ' . $filenameHash . ')');
            $this->respond_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        $finalPath = $this->targetPath . $url_key . '_' . $filename;

        if (realpath($this->targetPath) === mb_substr(realpath($finalPath), 0, mb_strlen(realpath($this->targetPath))) && ( ! file_exists($finalPath) || @unlink($finalPath))) {
            $this->mdl_uploads->delete_file($url_key, $filename);
            $this->respond_message(200, 'upload_file_deleted_successfully', $filename);
        }

        // Security: Don't leak file paths or referrer in logs
        log_message('debug', 'upload: File delete failed');
        $this->respond_message(410, 'upload_error_file_delete', 'File delete failed');
    }

    public function get_file($filename): void
    {
        // Security: Removed urldecode() - CodeIgniter already handles URL decoding
        // First sanitize to handle the url_key_filename format
        $filename = $this->sanitize_file_name($filename);

        $underscorePos = mb_strpos($filename, '_');
        if ($underscorePos === false) {
            $filenameHash = hash_for_logging($filename);
            log_message('error', 'upload: Invalid filename format (hash: ' . $filenameHash . ')');
            $this->respond_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        $url_key       = mb_substr($filename, 0, $underscorePos);
        $real_filename = mb_substr($filename, $underscorePos + 1);

        // Security: Validate the real filename component for security issues
        $filenameValidation = validate_safe_filename($real_filename);
        if ( ! $filenameValidation['valid']) {
            $error = $filenameValidation['error'];
            log_message('error', sprintf('upload: Invalid filename - %s (hash: %s)', $error, $filenameValidation['hash']));
            $this->respond_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        // Construct the full path with url_key prefix
        $fullPath = $this->get_target_file_path($url_key, $real_filename);

        if ( ! file_exists($fullPath)) {
            log_message('debug', 'upload: File not found in uploads directory');
            $this->respond_message(404, 'upload_error_file_not_found', 'File not found');
        }

        // Security: Validate the resolved path is within allowed directory
        if ( ! validate_file_in_directory($fullPath, $this->targetPath)) {
            $filenameHash = hash_for_logging($filename);
            log_message('error', 'upload: Path traversal detected (hash: ' . $filenameHash . ')');
            $this->respond_message(403, 'upload_error_unauthorized_access', 'Unauthorized access');
        }

        $path_parts = pathinfo($fullPath);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size  = filesize($fullPath);

        // Security: Sanitize filename for header to prevent header injection
        $sanitizedFilename = sanitize_filename_for_header($real_filename);

        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $sanitizedFilename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);
        readfile($fullPath);
    }

    private function sanitize_file_name(string $filename): string
    {
        // Security: Remove any path components
        $filename = basename($filename);

        // Security: Check for path traversal attempts before sanitization
        if (str_contains($filename, '..')
            || str_contains($filename, '/')
            || str_contains($filename, '\\')
            || str_contains($filename, "\0")) {
            log_message('error', 'Path traversal attempt detected in filename: ' . $filename);

            return '';
        }

        // Clean filename (same in dropzone script)
        $sanitizedFileName = preg_replace("/[^\p{L}\p{N}\s\-_'’.]/u", '', mb_trim($filename));

        // Security: Additional check to ensure no path traversal sequences remain
        $sanitizedFileName = str_replace('..', '', $sanitizedFileName);

        return $sanitizedFileName;
    }

    private function get_target_file_path(string $url_key, string $filename): string
    {
        return $this->targetPath . $url_key . '_' . $filename;
    }

    private function validate_mime_type(string $mimeType): void
    {
        $allowedTypes = array_values($this->content_types);
        if ( ! in_array($mimeType, $allowedTypes, true)) {
            $this->respond_message(415, 'upload_error_unsupported_file_type', $mimeType);
        }
    }

    private function save_file_metadata(int $customerId, string $url_key, string $filename): void
    {
        $data = [
            'client_id'          => $customerId,
            'url_key'            => $url_key,
            'file_name_original' => $filename,
            'file_name_new'      => $url_key . '_' . $filename,
        ];

        if ( ! $this->mdl_uploads->create($data)) {
            $this->respond_message(500, 'upload_error_database', $filename);
        }
    }

    private function move_uploaded_file(string $tempFile, string $filePath, string $filename): void
    {
        $this->create_dir(dirname($filePath));

        if ( ! is_writable(dirname($filePath))) {
            $this->respond_message(410, 'upload_error_folder_not_writable', dirname($filePath));
        } elseif ( ! move_uploaded_file($tempFile, $filePath)) {
            $this->respond_message(400, 'upload_error_invalid_move_uploaded_file', $filename);
        }
    }

    private function respond_message(int $httpCode, string $messageKey, string $dynamicLogValue = ''): void
    {
        log_message('debug', trans($messageKey) . ': (status ' . $httpCode . ') ' . $dynamicLogValue);
        http_response_code($httpCode);
        _trans($messageKey);
        if ($httpCode == 410) {
            echo PHP_EOL . PHP_EOL . '"' . basename(UPLOADS_FOLDER) . DIRECTORY_SEPARATOR . basename($this->targetPath) . '"';
        }

        exit;
    }
}
