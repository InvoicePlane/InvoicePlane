<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 * 
 * @author        InvoicePlane Developers
 * @copyright     InvoicePlane
 * @license       https://invoiceplane.com/license.txt
 * @link          https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Upload extends Admin_Controller
{
    public $targetPath;

    public $ctype_default = 'application/octet-stream';
    public $content_types = [
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'pdf'  => 'application/pdf',
        'png'  => 'image/png',
        'txt'  => 'text/plain',
        'xml'  => 'application/xml',
    ];

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->targetPath = UPLOADS_FOLDER . '/customer_files';
    }

    public function upload_file($customerId, $url_key)
    {
        if (!$this->validate_csrf()) {
            $this->respond_error(403, 'ip_lang.error_csrf', 'ip_lang.log_error_csrf');
            return;
        }

        if (empty($_FILES) || !isset($_FILES['file'])) {
            $this->respond_error(400, 'ip_lang.error_no_file', 'ip_lang.log_error_no_file');
            return;
        }

        $fileName = $this->sanitize_file_name($_FILES['file']['name']);
        $tempFile = $_FILES['file']['tmp_name'];
        $filePath = $this->get_target_file_path($url_key, $fileName);

        if (!$this->validate_upload($tempFile, $fileName)) {
            return;
        }

        $fileType = mime_content_type($tempFile);
        if (!$this->validate_mime_type($fileType)) {
            return;
        }

        if (file_exists($filePath)) {
            $this->respond_error(409, 'ip_lang.error_duplicate_file', 'ip_lang.log_info_duplicate_file', $fileName);
            return;
        }

        if (!$this->save_file_metadata($customerId, $url_key, $fileName)) {
            return;
        }

        if (!$this->move_uploaded_file($tempFile, $filePath, $fileName)) {
            return;
        }

        log_message('info', sprintf(_trans('ip_lang.log_info_file_uploaded'), $fileName));
        echo json_encode(['success' => true, 'message' => _trans('ip_lang.file_uploaded_successfully')]);
    }

    public function create_dir($path, $chmod = '0777')
    {
        if (!(is_dir($path) || is_link($path))) {
            return mkdir($path, $chmod);
        }
        return false;
    }

    public function show_files($url_key, $customerId = null)
    {
        $result = [];
        $path = $this->targetPath;

        $files = scandir($path);
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (strpos($file, $url_key) !== 0) {
                continue;
            }
            if (realpath($path) !== substr(realpath($file), 0, strlen(realpath($path)))) {
                continue;
            }

            $result[] = [
                'name' => substr($file, strpos($file, '_', 1) + 1),
                'fullname' => $file,
                'size' => filesize($path . '/' . $file),
            ];
        }

        echo json_encode($result);
    }

    public function delete_file($url_key)
    {
        $path = $this->targetPath;
        $fileName = $this->input->post('name');

        $this->mdl_uploads->delete_file($url_key, $fileName);

        $finalPath = $path . '/' . $url_key . '_' . $fileName;

        if (realpath($path) === substr(realpath($finalPath), 0, strlen(realpath($path)))) {
            unlink($finalPath);
        }
    }

    public function get_file($filename)
    {
        $base_path = realpath(UPLOADS_CFILES_FOLDER);
        if (!$base_path) {
            log_message('error', sprintf(_trans('ip_lang.log_error_invalid_directory'), UPLOADS_CFILES_FOLDER));
            show_404();
            return;
        }

        $file_path = realpath($base_path . DIRECTORY_SEPARATOR . basename($filename));
        if (!$file_path || strpos($file_path, $base_path) !== 0) {
            log_message('error', sprintf(_trans('ip_lang.log_error_unauthorized_access'), $filename));
            show_404();
            return;
        }

        $path_parts = pathinfo($file_path);
        $file_ext = strtolower($path_parts['extension'] ?? '');

        if (!isset($this->content_types[$file_ext])) {
            log_message('error', sprintf(_trans('ip_lang.log_error_unsupported_file_type'), $file_ext));
            show_error(_trans('ip_lang.error_unsupported_file_type'), 403);
            return;
        }

        if (!file_exists($file_path)) {
            log_message('error', sprintf(_trans('ip_lang.log_error_file_not_found'), $file_path));
            show_404();
            return;
        }

        $file_size = filesize($file_path);
        $ctype = $this->content_types[$file_ext] ?? $this->ctype_default;

        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);

        readfile($file_path);
    }

    private function validate_csrf(): bool
    {
        return $this->security->csrf_verify();
    }

    private function sanitize_file_name(string $fileName): string
    {
        return preg_replace('/\s+/', '_', $fileName);
    }

    private function get_target_file_path(string $url_key, string $fileName): string
    {
        return $this->targetPath . '/' . $url_key . '_' . $fileName;
    }

    private function validate_upload(string $tempFile, string $fileName): bool
    {
        if (!is_uploaded_file($tempFile)) {
            $this->respond_error(400, 'ip_lang.error_invalid_file_upload', 'ip_lang.log_error_invalid_file_upload', $fileName);
            return false;
        }
        return true;
    }

    private function validate_mime_type(string $mimeType): bool
    {
        $allowedTypes = array_values($this->content_types);
        if (!in_array($mimeType, $allowedTypes, true)) {
            $this->respond_error(415, 'ip_lang.error_unsupported_file_type', 'ip_lang.log_error_unsupported_file_type', $mimeType);
            return false;
        }
        return true;
    }

    private function save_file_metadata(int $customerId, string $url_key, string $fileName): bool
    {
        $data = [
            'client_id' => $customerId,
            'url_key' => $url_key,
            'file_name_original' => $fileName,
            'file_name_new' => $url_key . '_' . $fileName,
        ];

        if (!$this->mdl_uploads->create($data)) {
            $this->respond_error(500, 'ip_lang.error_database', 'ip_lang.log_error_database', $fileName);
            return false;
        }

        return true;
    }

    private function move_uploaded_file(string $tempFile, string $filePath, string $fileName): bool
    {
        if (!move_uploaded_file($tempFile, $filePath)) {
            $this->respond_error(500, 'ip_lang.error_file_save', 'ip_lang.log_error_file_save', $fileName);
            return false;
        }

        return true;
    }

    private function respond_error(int $httpCode, string $messageKey, string $logKey, string $dynamicValue = '')
    {
        log_message('error', sprintf(_trans($logKey), $dynamicValue));
        http_response_code($httpCode);
        echo json_encode(['success' => false, 'message' => _trans($messageKey)]);
    }
}
