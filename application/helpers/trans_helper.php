<?php

if (!defined('BASEPATH')) {
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

/**
 * Output a language string, supports language fallback if a string wasn't found.
 *
 * @param string      $line
 * @param string      $id
 * @param null|string $default
 *
 * @return string
 */
function trans($line, $id = '', $default = null)
{
    $CI = & get_instance();
    $lang_string = $CI->lang->line($line);

    // Fall back to default language if the current language has no translated string
    if (empty($lang_string)) {
        // Save the current application language (code borrowed from Base_Controller.php)
        $current_language = $CI->session->userdata('user_language');

        if (empty($current_language) || $current_language == 'system') {
            // todo gives error at startup, fix later
            $current_language = 'english'; //get_setting('default_language');
        }

        // Load the default language and translate the string
        set_language('english');
        $lang_string = $CI->lang->line($line);

        // Restore the application language to its previous setting
        set_language($current_language);
    }

    // Fall back to the $line value if the default language has no translation either
    if (empty($lang_string)) {
        $lang_string = $default != null ? $default : $line;
    }

    if ($id != '') {
        $lang_string = '<label for="' . $id . '">' . $lang_string . '</label>';
    }

    return $lang_string;
}

/**
 * Load the translations for the given language.
 *
 * @param string $language
 */
function set_language($language)
{
    // Clear the current loaded language
    $CI = & get_instance();
    $CI->lang->is_loaded = [];
    $CI->lang->language = [];

    // Load system language if no custom language is set
    $default_lang = isset($CI->mdl_settings) ? $CI->mdl_settings->setting('default_language') : 'english';
    $new_language = ($language == 'system' ? $default_lang : $language);

    // Set the new language
    $CI->lang->load('ip', $new_language);
    $CI->lang->load('form_validation', $new_language);
    $CI->lang->load('custom', $new_language);
    $CI->lang->load('gateway', $new_language);
}

/**
 * Returns all available languages.
 *
 * @return array
 */
function get_available_languages()
{
    $CI = & get_instance();
    $CI->load->helper('directory');

    $languages = directory_map(APPPATH . 'language', true);
    sort($languages);

    for ($i = 0; $i < count($languages); $i++) {
        $languages[$i] = str_replace(['\\', '/'], '', $languages[$i]);
    }

    return $languages;
}
