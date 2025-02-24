<?php

if (! defined('BASEPATH'))
{
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
    public $targetPath;
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
        $this->targetPath = UPLOADS_CFILES_FOLDER; // UPLOADS_FOLDER . 'customer_files/'; // double slash fix
    }

    public function upload_file($customerId, $url_key): void
    {
        // Show files Legacy (obsolete)
        if (empty($_FILES['file']))
        {
            $this->show_files($customerId, $url_key);
        }
        elseif (empty($_FILES['file']['name']))
        {
            $this->respond_message(400, 'upload_error_no_file');
        }

        $filename = $this->sanitize_file_name($_FILES['file']['name']);
        $filePath = $this->get_target_file_path($url_key, $filename);

        if (file_exists($filePath))
        {
            $this->respond_message(409, 'upload_error_duplicate_file', $filename);
        }

        $tempFile = $_FILES['file']['tmp_name'];
        $this->validate_mime_type(mime_content_type($tempFile));
        $this->move_uploaded_file($tempFile, $filePath, $filename);

        $this->save_file_metadata($customerId, $url_key, $filename);

        $this->respond_message(200, 'upload_file_uploaded_successfully', $filename);
    }

    public function create_dir($path, $chmod = '0755'): bool
    {
        if (! (is_dir($path) || is_link($path)))
        {
            return mkdir($path, $chmod);
        }
        return true;
    }

    public function show_files($customerId = null, $url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = $this->mdl_uploads->get_files($url_key))
        {
            exit('{}');
        }

        echo json_encode($result);
        exit;
    }

    public function delete_file($url_key): void
    {
        $path = $this->targetPath;
        $filename = urldecode($this->input->post('name'));

        $finalPath = $this->targetPath . $url_key . '_' . $filename;

        if (realpath($this->targetPath) === substr(realpath($finalPath), 0, strlen(realpath($this->targetPath))))
        {
            if (! file_exists($finalPath) || @unlink($finalPath))
            {
                $this->mdl_uploads->delete_file($url_key, $filename);
                $this->respond_message(200, 'upload_file_deleted_successfully', $filename);
            }
        }
        $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
        $this->respond_message(410, 'upload_error_file_delete', $finalPath . $ref);
    }

    public function get_file($filename): void
    {
        $filename = urldecode($filename);
        if (! file_exists($this->targetPath . $filename))
        {
            $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
            $this->respond_message(404, 'upload_error_file_not_found', $this->targetPath . $filename . $ref);
        }

        $path_parts = pathinfo($this->targetPath . $filename);
        $file_ext = strtolower($path_parts['extension'] ?? '');
        $ctype = $this->content_types[$file_ext] ?? $this->ctype_default;

        $file_size = filesize($this->targetPath . $filename);

        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);

        readfile($this->targetPath . $filename);
    }

    private function sanitize_file_name(string $filename): string
    {
        // Clean filename (same in dropzone script)
        return preg_replace("/[^\p{L}\p{N}\s\-_'’.]/u", '', trim($filename));
    }

    private function get_target_file_path(string $url_key, string $filename): string
    {
        return $this->targetPath . $url_key . '_' . $filename;
    }

    private function validate_mime_type(string $mimeType): void
    {
        $allowedTypes = array_values($this->content_types);
        if (! in_array($mimeType, $allowedTypes, true))
        {
            $this->respond_message(415, 'upload_error_unsupported_file_type', $mimeType);
        }
    }

    private function save_file_metadata(int $customerId, string $url_key, string $filename): void
    {
        $data =
        [
            'client_id'          => $customerId,
            'url_key'            => $url_key,
            'file_name_original' => $filename,
            'file_name_new'      => $url_key . '_' . $filename,
        ];

        if (! $this->mdl_uploads->create($data))
        {
            $this->respond_message(500, 'upload_error_database', $filename);
        }
    }

    private function move_uploaded_file(string $tempFile, string $filePath, string $filename): void
    {
        // Create the target dir (if unexist)
        $this->create_dir($this->targetPath);

        // Checks to ensure that the target dir is writable
        if (! is_writable($this->targetPath))
        {
            $this->respond_message(410, 'upload_error_folder_not_writable', $this->targetPath);
        }
        // Checks to ensure that the tempFile is a valid upload file AND it was uploaded via PHP's HTTP POST upload.
        elseif (! move_uploaded_file($tempFile, $filePath))
        {
            $this->respond_message(400, 'upload_error_invalid_move_uploaded_file', $filename);
        }
    }

    private function respond_message(int $httpCode, string $messageKey, string $dynamicLogValue = ''): void
    {
        log_message('debug', trans($messageKey)  .': (status ' . $httpCode . ') ' . $dynamicLogValue);
        http_response_code($httpCode);
        _trans($messageKey);
        if ($httpCode == 410)
        {
            echo PHP_EOL . PHP_EOL . '"' . basename(UPLOADS_FOLDER) . DIRECTORY_SEPARATOR . basename($this->targetPath) . '"';
        }
        exit;
    }
}
