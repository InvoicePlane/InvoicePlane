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
    /**
     * The expected length of url_key strings.
     * url_keys are 32-character random alphanumeric strings.
     */
    private const URL_KEY_LENGTH = 32;

    /**
     * Empty JSON response for unauthorized file access.
     * Using a constant for consistency across methods.
     */
    private const EMPTY_JSON_RESPONSE = '{}';

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
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->content_types = $this->mdl_uploads->content_types;
    }

    public function show_files($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Security: Verify url_key belongs to a guest-visible invoice or quote
        // Null check is needed because $url_key can be null from URL parameter
        if ( ! $url_key || ! $this->is_url_key_guest_visible($url_key)) {
            exit(self::EMPTY_JSON_RESPONSE);
        }

        $result = $this->mdl_uploads->get_files($url_key);
        if ( ! $result) {
            exit(self::EMPTY_JSON_RESPONSE);
        }

        $this->json_encode_ajax($result);
    }

    /**
     * Alternative method for downloading attachments via /guest/get/attachment/ URLs.
     * Provides support for the /guest/get/attachment/ URL path.
     *
     * @param string|null $filename The filename to download
     *
     * @return void
     */
    public function attachment(?string $filename = null): void
    {
        $this->get_file($filename);
    }

    public function get_file(?string $filename = null): void
    {
        if ($filename === null || $filename === '') {
            respond_file_message(
                400,
                'upload_error_invalid_filename',
                'Missing filename for guest file download request',
                'guest/get: '
            );
        }

        // Security: Extract url_key from filename to validate parent document status
        // Filename format: {url_key}_{original_filename}
        $url_key = $this->extract_url_key_from_filename($filename);

        // Security: Verify url_key belongs to a guest-visible invoice or quote
        if ( ! $url_key || ! $this->is_url_key_guest_visible($url_key)) {
            respond_file_message(
                404,
                'upload_error_file_not_found',
                'File not found or access denied',
                'guest/get: '
            );
        }

        // Security: Use comprehensive file security validation helper
        // Note: CodeIgniter already URL-decodes parameters during routing
        $validation = validate_file_access($filename, $this->targetPath);

        if ( ! $validation['valid']) {
            $errorMap = [
                'file_not_found'         => [404, 'upload_error_file_not_found', 'File not found'],
                'path_outside_directory' => [403, 'upload_error_unauthorized_access', 'Unauthorized access'],
            ];

            $error = $validation['error'] ?? 'unknown';
            $response = $errorMap[$error] ?? [400, 'upload_error_invalid_filename', 'Invalid filename'];

            respond_file_message($response[0], $response[1], $response[2], 'guest/get: ');
        }

        $realFile = $validation['path'];
        $safeFilename = $validation['basename'];

        $path_parts = pathinfo($realFile);
        $file_ext = mb_strtolower($path_parts['extension'] ?? '');
        $ctype = $this->content_types[$file_ext] ?? $this->ctype_default;
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

    /**
     * Extract url_key from a filename with format {url_key}_{original_filename}.
     * Note: url_keys are case-sensitive alphanumeric strings.
     *
     * @param string $filename The filename to parse
     *
     * @return string|null The extracted url_key or null if not found
     */
    private function extract_url_key_from_filename(string $filename): ?string
    {
        // Match exactly 32 alphanumeric characters followed by underscore
        if (preg_match('/^([a-zA-Z0-9]{32})_/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Verify that the url_key belongs to a guest-visible invoice or quote.
     * Returns true if the url_key is valid and guest-visible, false otherwise.
     *
     * @param string $url_key The url_key to validate
     *
     * @return bool
     */
    private function is_url_key_guest_visible(string $url_key): bool
    {
        // Validate url_key format before database query (defense in depth)
        if ( ! $this->is_valid_url_key_format($url_key)) {
            return false;
        }

        // Check if url_key belongs to a guest-visible invoice (status_id IN (2,3,4))
        if ($this->check_document_visibility($this->mdl_invoices, 'invoice_url_key', $url_key)) {
            return true;
        }

        // Check if url_key belongs to a guest-visible quote (status_id IN (2,3,4,5))
        return (bool) ($this->check_document_visibility($this->mdl_quotes, 'quote_url_key', $url_key));
    }

    /**
     * Validate that a url_key matches the expected format.
     * url_keys must be exactly 32 alphanumeric characters.
     *
     * @param string $url_key The url_key to validate
     *
     * @return bool True if format is valid, false otherwise
     */
    private function is_valid_url_key_format(string $url_key): bool
    {
        // url_key must be exactly 32 alphanumeric characters (no special chars, no directory traversal)
        return preg_match('/^[a-zA-Z0-9]{' . self::URL_KEY_LENGTH . '}$/', $url_key) === 1;
    }

    /**
     * Check if a document with the given url_key is guest-visible.
     *
     * @param Response_Model $model     The model (mdl_invoices or mdl_quotes)
     * @param string         $key_field The field name for the url_key
     * @param string         $url_key   The url_key to check
     *
     * @return bool True if the document is guest-visible, false otherwise
     */
    private function check_document_visibility(Response_Model $model, string $key_field, string $url_key): bool
    {
        $result = $model->guest_visible()
            ->where($key_field, $url_key)
            ->get();

        return $result->num_rows() === 1;
    }
}
