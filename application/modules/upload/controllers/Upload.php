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
        $file_ext         = mb_strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $tempFile         = $_FILES['file']['tmp_name'];

        if (extension_loaded('fileinfo')) {
            $this->validate_mime_type(mime_content_type($tempFile));
        }
        if ( ! in_array($file_ext, $this->allowed_extensions, true)) {
            $this->respond_message(400, 'upload_error_invalid_extension', $file_ext);
        }

        $randomName = bin2hex(random_bytes(16)) . '.' . $file_ext;
        $filePath   = $this->get_target_file_path($url_key, $randomName);

        $this->move_uploaded_file($tempFile, $filePath, $randomName);
        $this->save_file_metadata($customerId, $url_key, $originalFilename, $randomName);

        // Return JSON with both original and new filename
        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'original' => $originalFilename,
            'name'     => $randomName,
        ]);
        exit;
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

        $result = $this->mdl_uploads->get_files($url_key);
        if ( ! $result) {
            exit(json_encode([]));
        }

        // Ensure each file entry has both original and new filename
        $files = [];
        foreach ($result as $file) {
            $files[] = [
                'name'     => $file['file_name_original'] ?? '',
                'fullname' => $file['file_name_new'] ?? '',
                'size'     => isset($file['file_name_new']) ? filesize(FCPATH . 'uploads/archive/' . $file['file_name_new']) : 0,
            ];
        }
        exit(json_encode($files));
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
dd($filename);

        $filename    = urldecode($filename);
        $archivePath = FCPATH . 'uploads/archive/';
        $fullPath    = $archivePath . $filename;

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
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);
        readfile($fullPath);
    }

    /**
     * Allow only safe characters: letters, numbers, spaces, dashes, underscores, dots.
     */
    private function sanitize_file_name(string $filename): string
    {
        // Remove path separators and directory traversal sequences
        $filename = str_replace(['/', '\\'], '', $filename);
        // Remove any leading/trailing dots and collapse multiple dots
        $filename = preg_replace('/\.+/', '.', $filename);
        $filename = mb_trim($filename, '.');
        // Remove any remaining unsafe characters
        $filename = preg_replace('/[^\w\s\-.]/u', '', $filename);
        $file_ext = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ( ! in_array($file_ext, $this->allowed_extensions, true)) {
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.txt';
        }

        return $filename;
    }

    private function get_target_file_path(string $url_key, string $filename): string
    {
        $archivePath = FCPATH . 'uploads/archive/';
        $this->create_dir($archivePath);

        return $archivePath . $url_key . '_' . $filename;
    }

    private function validate_mime_type(string $mimeType): void
    {
        $allowedTypes = array_values($this->content_types);
        if ( ! in_array($mimeType, $allowedTypes, true)) {
            $this->respond_message(415, 'upload_error_unsupported_file_type', $mimeType);
        }
    }

    private function save_file_metadata(int $customerId, string $url_key, string $originalFilename, string $randomName): void
    {
        $data = [
            'client_id'          => $customerId,
            'url_key'            => $url_key,
            'file_name_original' => $originalFilename,
            'file_name_new'      => $url_key . '_' . $originalFilename,
        ];

        if ( ! $this->mdl_uploads->create($data)) {
            $this->respond_message(500, 'upload_error_database', $originalFilename);
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
