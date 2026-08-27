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
            respond_file_message(400, 'upload_error_no_file');
        }

        $originalFilename = $_FILES['file']['name'];
        $fileName         = $this->sanitize_file_name($originalFilename);
        $file_ext         = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $filePath         = $this->get_target_file_path($url_key, $fileName);

        if (file_exists($filePath)) {
            respond_file_message(409, 'upload_error_duplicate_file', $fileName);
        }

        $tempFile = $_FILES['file']['tmp_name'];
        if (extension_loaded('fileinfo')) {
            $this->validate_mime_type(mime_content_type($tempFile));
        }

        if ( ! in_array($file_ext, $this->allowed_extensions, true)) {
            respond_file_message(400, 'upload_error_invalid_extension', $file_ext);
        }

        $this->move_uploaded_file($tempFile, $filePath, $fileName);

        $this->save_file_metadata($customerId, $url_key, $fileName);

        respond_file_message(200, 'upload_file_uploaded_successfully', $fileName);
    }

    public function show_files($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = $this->mdl_uploads->get_files($url_key)) {
            exit('{}');
        }

        $this->json_encode_ajax($result);
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
            respond_file_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        $finalPath = $this->targetPath . $url_key . '_' . $filename;

        // Security: realpath() (used by validate_file_in_directory()) returns false for a
        // nonexistent path, so an already-deleted file must be handled before that check -
        // $filename was already rejected above by sanitize_file_name() if it contained any
        // path separator or traversal sequence, so $finalPath cannot escape $this->targetPath.
        if ( ! file_exists($finalPath)) {
            $this->mdl_uploads->delete_file($url_key, $filename);
            respond_file_message(200, 'upload_file_deleted_successfully', $filename);
        }

        if (validate_file_in_directory($finalPath, $this->targetPath) && @unlink($finalPath)) {
            $this->mdl_uploads->delete_file($url_key, $filename);
            respond_file_message(200, 'upload_file_deleted_successfully', $filename);
        }

        // Security: Don't leak file paths or referrer in logs
        log_message('debug', 'upload: File delete failed');
        respond_file_message(
            410,
            'upload_error_file_delete',
            'File delete failed',
            '',
            PHP_EOL . PHP_EOL . '"' . basename(UPLOADS_FOLDER) . DIRECTORY_SEPARATOR . basename($this->targetPath) . '"'
        );
    }

    public function get_file($filename): void
    {
        // First decode url & sanitize to handle the url_key_filename format
        // Note: Work with all files - $filename is URL encoded (See helpers/dropzone_helper.php)
        // [Old] urldecode() decodes + to a space, so a stored filename containing a literal + gets mangled.
        // [New] rawurldecode() decodes %20/%E2%80%99 etc. identically but leaves + alone.
        //       Still fully guarded by sanitize_file_name → validate_safe_filename → validate_file_in_directory afterwards.
        $filename = $this->sanitize_file_name(rawurldecode($filename)); // rawurldecode: keep literal '+' (urldecode turns it into a space)
        // dump: $filename => 1iKyYIgzZpewUa8EtN0MOXAGdTBDRfsC_Capture d’écran du 2025-10-15 02-43-47.png

        $underscorePos = mb_strpos($filename, '_');
        if ($underscorePos === false) {
            $filenameHash = hash_for_logging($filename);
            log_message('error', 'upload: Invalid filename format (hash: ' . $filenameHash . ')');
            respond_file_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        $url_key       = mb_substr($filename, 0, $underscorePos);
        $real_filename = mb_substr($filename, $underscorePos + 1);

        // Security: Validate the real filename component for security issues
        $filenameValidation = validate_safe_filename($real_filename);
        if ( ! $filenameValidation['valid']) {
            $error = $filenameValidation['error'];
            log_message('error', sprintf('upload: Invalid filename - %s (hash: %s)', $error, $filenameValidation['hash']));
            respond_file_message(400, 'upload_error_invalid_filename', 'Invalid filename');
        }

        // Construct the full path with url_key prefix
        $fullPath = $this->get_target_file_path($url_key, $real_filename);

        if ( ! file_exists($fullPath)) {
            log_message('debug', 'upload: File not found in uploads directory');
            respond_file_message(404, 'upload_error_file_not_found', 'File not found');
        }

        // Security: Validate the resolved path is within allowed directory
        if ( ! validate_file_in_directory($fullPath, $this->targetPath)) {
            $filenameHash = hash_for_logging($filename);
            log_message('error', 'upload: Path traversal detected (hash: ' . $filenameHash . ')');
            respond_file_message(403, 'upload_error_unauthorized_access', 'Unauthorized access');
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

    /**
     * Not public: CI3 routes any public controller method directly via URL
     * segments (e.g. /upload/upload/create_dir/<path>/<chmod>), which would
     * let this be called with an attacker-controlled path and permission
     * bits — an arbitrary mkdir() with no validation, CSRF, or confirmation.
     * Only ever meant to be called internally from move_uploaded_file().
     */
    private function create_dir($path, $chmod = '0755'): bool
    {
        if ( ! is_dir($path) && ! is_link($path)) {
            return mkdir($path, $chmod);
        }

        return true;
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
            // Security: Use hash to prevent log poisoning
            $filenameHash = hash_for_logging($filename);
            log_message('error', 'Path traversal attempt detected in filename (hash: ' . $filenameHash . ')');

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
            respond_file_message(415, 'upload_error_unsupported_file_type', $mimeType);
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
            respond_file_message(500, 'upload_error_database', $filename);
        }
    }

    private function move_uploaded_file(string $tempFile, string $filePath, string $filename): void
    {
        $this->create_dir(dirname($filePath));

        if ( ! is_writable(dirname($filePath))) {
            // Special handling for 410 status: include folder path in output for user feedback
            respond_file_message(410, 'upload_error_folder_not_writable', dirname($filePath), '', PHP_EOL . PHP_EOL . '"' . basename(UPLOADS_FOLDER) . DIRECTORY_SEPARATOR . basename($this->targetPath) . '"');
        } elseif ( ! move_uploaded_file($tempFile, $filePath)) {
            respond_file_message(400, 'upload_error_invalid_move_uploaded_file', $filename);
        }

        // Security: Strip EXIF metadata from uploaded images to prevent privacy leaks
        $this->strip_image_metadata($filePath, $filename);
    }

    private function strip_image_metadata(string $filePath, string $filename): void
    {
        $result = strip_exif_metadata($filePath);

        if ( ! $result['success'] && ! isset($result['skipped'])) {
            // Log the error but don't fail the upload - the file is already uploaded
            log_message('warning', 'Failed to strip EXIF metadata from uploaded file: ' . sanitize_for_logging($filename) . ' - Error: ' . $result['error']);
        } elseif ($result['success'] && ! isset($result['skipped'])) {
            // Successfully stripped EXIF metadata
            log_message('debug', 'EXIF metadata stripped from uploaded file: ' . sanitize_for_logging($filename));
        }
    }
}
