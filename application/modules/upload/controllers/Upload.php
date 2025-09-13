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
        $filename = urldecode($this->input->post('name'));

        $finalPath = $this->targetPath . $url_key . '_' . $filename;

        if (realpath($this->targetPath) === mb_substr(realpath($finalPath), 0, mb_strlen(realpath($this->targetPath))) && ( ! file_exists($finalPath) || @unlink($finalPath))) {
            $this->mdl_uploads->delete_file($url_key, $filename);
            $this->respond_message(200, 'upload_file_deleted_successfully', $filename);
        }

        $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
        $this->respond_message(410, 'upload_error_file_delete', $finalPath . $ref);
    }

    public function get_file($filename): void
    {
        $filename = urldecode($filename);

        $underscorePos = mb_strpos($filename, '_');
        if ($underscorePos === false) {
            $this->respond_message(400, 'upload_error_invalid_filename', $filename);
        }

        $url_key       = mb_substr($filename, 0, $underscorePos);
        $real_filename = mb_substr($filename, $underscorePos + 1);
        $fullPath      = $this->get_target_file_path($url_key, $real_filename);
        if ( ! file_exists($fullPath)) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
            $this->respond_message(404, 'upload_error_file_not_found', $fullPath . $ref);
        }
        $path_parts = pathinfo($fullPath);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size  = filesize($fullPath);
        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $real_filename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);
        readfile($fullPath);
    }

    private function sanitize_file_name(string $filename): string
    {
        // Clean filename (same in dropzone script)
        return preg_replace("/[^\p{L}\p{N}\s\-_'’.]/u", '', mb_trim($filename));
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
