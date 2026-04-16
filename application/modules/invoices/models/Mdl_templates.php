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
     * Security: Returns only the static whitelist, never scans the filesystem.
     * This prevents attackers from bypassing validation by writing malicious files.
     *
     * @param string $type Template type ('pdf' or 'public')
     *
     * @return array List of allowed template names (without .php extension)
     */
    public function get_invoice_templates($type = 'pdf')
    {
        // Security: Return static whitelist only - NEVER scan filesystem
        if ($type === 'pdf') {
            return self::ALLOWED_INVOICE_TEMPLATES['pdf'];
        }
        if ($type === 'public') {
            return self::ALLOWED_INVOICE_TEMPLATES['public'];
        }

        // Invalid type - return empty array
        return [];
    }

    /**
     * Get the list of allowed quote templates.
     *
     * Security: Returns only the static whitelist, never scans the filesystem.
     *
     * @param string $type Template type ('pdf' or 'public')
     *
     * @return array List of allowed template names (without .php extension)
     */
    public function get_quote_templates($type = 'pdf')
    {
        // Security: Return static whitelist only - NEVER scan filesystem
        if ($type === 'pdf') {
            return self::ALLOWED_QUOTE_TEMPLATES['pdf'];
        }
        if ($type === 'public') {
            return self::ALLOWED_QUOTE_TEMPLATES['public'];
        }

        // Invalid type - return empty array
        return [];
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
     * Returns a merged list of template filenames from the built-in directory and,
     * when configured, the custom templates folder.  Custom templates are listed
     * first so that duplicates (same filename) are deduplicated in their favour.
     *
     * @param string $subpath Relative sub-path, e.g. 'invoice_templates/pdf'
     * @param string $builtin Absolute path to the built-in template directory
     *
     * @return array
     */
    private function merge_custom_templates(string $subpath, string $builtin): array
    {
        $builtin_list = directory_map($builtin, true) ?: [];
        $custom_list  = [];

        if (CUSTOM_TEMPLATES_FOLDER) {
            $custom_dir  = CUSTOM_TEMPLATES_FOLDER . $subpath;
            $custom_list = is_array(directory_map($custom_dir, true)) ? directory_map($custom_dir, true) : [];
        }

        return array_values(array_unique(array_merge($custom_list, $builtin_list)));
    }

    /**
     * @param $files
     */
    private function remove_extension(array $files): array
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.php', '', $file);
        }

        return $files;
    }
}
