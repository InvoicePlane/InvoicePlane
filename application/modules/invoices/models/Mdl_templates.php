<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Templates extends CI_Model
{
    /**
     * Static whitelist of allowed invoice templates.
     *
     * Security: This is a hardcoded list to prevent RCE vulnerability.
     * Templates are NEVER loaded dynamically from the filesystem.
     * Only templates in this list can be used, even if other PHP files exist in the templates directory.
     *
     * To add a new template:
     * 1. Add the template file to the appropriate directory
     * 2. Add the template name (without .php extension) to this array
     * 3. Deploy both changes together
     *
     * @var array
     */
    private const ALLOWED_INVOICE_TEMPLATES = [
        'pdf' => [
            'InvoicePlane',
            'InvoicePlane - paid',
            'InvoicePlane - overdue',
        ],
        'public' => [
            'InvoicePlane_Web',
        ],
    ];

    /**
     * Static whitelist of allowed quote templates.
     *
     * Security: This is a hardcoded list to prevent RCE vulnerability.
     * Templates are NEVER loaded dynamically from the filesystem.
     *
     * @var array
     */
    private const ALLOWED_QUOTE_TEMPLATES = [
        'pdf' => [
            'InvoicePlane',
        ],
        'public' => [
            'InvoicePlane_Web',
        ],
    ];

    /**
     * Get the list of allowed invoice templates.
     *
     * Security: Built-in templates are returned from the static whitelist only — the
     * application's own template directories are NEVER scanned to prevent RCE.
     * When CUSTOM_TEMPLATES_FOLDER is configured, templates from that admin-supplied
     * directory are discovered, strictly validated, and merged with the built-in list.
     *
     * @param string $type Template type ('pdf' or 'public')
     *
     * @return array List of allowed template names (without .php extension)
     */
    public function get_invoice_templates($type = 'pdf')
    {
        if ($type === 'pdf') {
            $built_in = self::ALLOWED_INVOICE_TEMPLATES['pdf'];
        } elseif ($type === 'public') {
            $built_in = self::ALLOWED_INVOICE_TEMPLATES['public'];
        } else {
            return [];
        }

        return $this->_merge_custom('invoice_templates/' . $type, $built_in);
    }

    /**
     * Get the list of allowed quote templates.
     *
     * Security: Built-in templates are returned from the static whitelist only — the
     * application's own template directories are NEVER scanned to prevent RCE.
     * When CUSTOM_TEMPLATES_FOLDER is configured, templates from that admin-supplied
     * directory are discovered, strictly validated, and merged with the built-in list.
     *
     * @param string $type Template type ('pdf' or 'public')
     *
     * @return array List of allowed template names (without .php extension)
     */
    public function get_quote_templates($type = 'pdf')
    {
        if ($type === 'pdf') {
            $built_in = self::ALLOWED_QUOTE_TEMPLATES['pdf'];
        } elseif ($type === 'public') {
            $built_in = self::ALLOWED_QUOTE_TEMPLATES['public'];
        } else {
            return [];
        }

        return $this->_merge_custom('quote_templates/' . $type, $built_in);
    }

    /**
     * Check if template directories have insecure permissions.
     *
     * Security: Warns administrators if template directories are writable by the web server.
     * This is a defense-in-depth measure - the static whitelist already prevents exploitation,
     * but writable template directories are still a security misconfiguration that should be fixed.
     *
     * @return array Array of warnings (empty if no issues found)
     */
    public function check_template_directory_permissions(): array
    {
        $warnings = [];

        $directories = [
            APPPATH . 'views/invoice_templates/pdf',
            APPPATH . 'views/invoice_templates/public',
            APPPATH . 'views/quote_templates/pdf',
            APPPATH . 'views/quote_templates/public',
        ];

        foreach ($directories as $dir) {
            // Check if directory is writable
            if (is_writable($dir)) {
                $warnings[] = [
                    'directory'      => $dir,
                    'message'        => 'Template directory is writable by web server. This is a security risk.',
                    'recommendation' => 'Set directory permissions to read-only (e.g., chmod 555)',
                ];
            }
        }

        return $warnings;
    }

    /**
     * Merge built-in templates with any validated templates found in CUSTOM_TEMPLATES_FOLDER.
     *
     * Security:
     * - Only the admin-configured CUSTOM_TEMPLATES_FOLDER is scanned, NEVER the application's
     *   own template directories (the RCE fix remains intact).
     * - Template file names are validated against a strict allowlist regex before use.
     *   Any name that does not match is silently skipped and logged.
     * - Custom templates are listed first so admins can shadow a built-in name if needed;
     *   array_unique() deduplicates the merged list.
     *
     * @param string $subpath  Relative sub-path, e.g. 'invoice_templates/pdf'
     * @param array  $built_in Hardcoded whitelist entries from the class constants
     */
    private function _merge_custom(string $subpath, array $built_in): array
    {
        if ( ! CUSTOM_TEMPLATES_FOLDER) {
            return $built_in;
        }

        $CI = &get_instance();
        $CI->load->helper('directory');

        $custom_dir = CUSTOM_TEMPLATES_FOLDER . $subpath;

        if ( ! is_dir($custom_dir)) {
            return $built_in;
        }

        $files        = directory_map($custom_dir, 1) ?: [];
        $custom_names = [];

        foreach ($files as $file) {
            if ( ! is_string($file) || ! str_ends_with($file, '.php')) {
                continue;
            }

            $name = substr($file, 0, -4); // strip .php extension

            // Strict validation: only alphanumeric characters, spaces, hyphens and underscores.
            // Rejects path traversal sequences, null bytes, and any other special characters.
            if (preg_match('/^[a-zA-Z0-9 _-]+$/', $name)) {
                $custom_names[] = $name;
            } else {
                // Sanitize before logging: strip control characters to prevent log injection.
                $safe_name = preg_replace('/[\x00-\x1f\x7f]/', '', substr($file, 0, 64));
                log_message('warning', 'Mdl_Templates: skipping invalid custom template name: ' . $safe_name);
            }
        }

        return array_values(array_unique(array_merge($custom_names, $built_in)));
    }
}
