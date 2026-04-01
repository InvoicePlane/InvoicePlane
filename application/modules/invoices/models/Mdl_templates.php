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
     * @param string $type
     *
     * @return array
     */
    public function get_invoice_templates($type = 'pdf')
    {
        $this->load->helper('directory');

        if ($type == 'pdf') {
            $templates = $this->merge_custom_templates('invoice_templates/pdf', APPPATH . '/views/invoice_templates/pdf');
        } elseif ($type == 'public') {
            $templates = $this->merge_custom_templates('invoice_templates/public', APPPATH . '/views/invoice_templates/public');
        }

        return $this->remove_extension($templates ?? []);
    }

    /**
     * @param string $type
     *
     * @return array|mixed
     */
    public function get_quote_templates($type = 'pdf')
    {
        $this->load->helper('directory');

        if ($type == 'pdf') {
            $templates = $this->merge_custom_templates('quote_templates/pdf', APPPATH . '/views/quote_templates/pdf');
        } elseif ($type == 'public') {
            $templates = $this->merge_custom_templates('quote_templates/public', APPPATH . '/views/quote_templates/public');
        }

        return $this->remove_extension($templates ?? []);
    }

    /**
     * Returns a merged list of template filenames from the built-in directory and,
     * when configured, the custom templates folder.  Custom templates are listed
     * first so that duplicates (same filename) are deduplicated in their favour.
     *
     * @param string $subpath    Relative sub-path, e.g. 'invoice_templates/pdf'
     * @param string $builtin    Absolute path to the built-in template directory
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
