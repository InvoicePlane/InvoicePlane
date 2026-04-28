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
        if ( ! $url_key) {
            return false;
        }

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');

        // Check if url_key belongs to a guest-visible invoice (status_id IN (2,3,4))
        $invoice = $this->mdl_invoices->guest_visible()
            ->where('invoice_url_key', $url_key)
            ->get();

        if ($invoice->num_rows() === 1) {
            return true;
        }

        // Check if url_key belongs to a guest-visible quote (status_id IN (2,3,4,5))
        $quote = $this->mdl_quotes->guest_visible()
            ->where('quote_url_key', $url_key)
            ->get();

        if ($quote->num_rows() === 1) {
            return true;
        }

        return false;
    }

    public function show_files($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Security: Verify url_key belongs to a guest-visible invoice or quote
        if ( ! $url_key || ! $this->is_url_key_guest_visible($url_key)) {
            exit('{}');
        }

        $result = $this->mdl_uploads->get_files($url_key);
        if ( ! $result) {
            exit('{}');
        }

        echo json_encode($result);
        exit;
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
        $url_key = null;
        if (preg_match('/^([a-zA-Z0-9]{32})_/', $filename, $matches)) {
            $url_key = $matches[1];
        }

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

            $error    = $validation['error'] ?? 'unknown';
            $response = $errorMap[$error] ?? [400, 'upload_error_invalid_filename', 'Invalid filename'];

            respond_file_message($response[0], $response[1], $response[2], 'guest/get: ');
        }

        $realFile     = $validation['path'];
        $safeFilename = $validation['basename'];

        $path_parts = pathinfo($realFile);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size  = filesize($realFile);

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
}
