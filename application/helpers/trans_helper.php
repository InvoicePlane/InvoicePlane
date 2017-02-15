<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Output a language string, supports language fallback if a string wasn't found
 *
 * @param $line
 * @param string $id
 * @return string
 */
function trans($line, $id = '')
{
    $CI =& get_instance();
    $lang_string = $CI->lang->line($line);

    // Fall back to default language if the current language has no translated string
    if (empty($lang_string)) {
        // Load the default language
        set_language('english');
        $lang_string = $CI->lang->line($line);
        reset_language();
    }

    // Fall back to the $line value if the default language has no translation either
    if (empty($lang_string)) {
        $lang_string = $line;
    }

    if ($id != '') {
        $lang_string = '<label for="' . $id . '">' . $lang_string . "</label>";
    }

    return $lang_string;
}

/**
 * Load the translations for the given language
 *
 * @param string $language
 * @return void
 */
function set_language($language)
{
    // Clear the current loaded language
    $CI =& get_instance();
    $CI->lang->is_loaded = array();
    $CI->lang->language = array();

    // Set the new language
    $CI->lang->load('ip', $language);
    $CI->lang->load('form_validation', $language);
    $CI->lang->load('custom', $language);
}

/**
 * Reset the langauge to the default one
 *
 * @return void
 */
function reset_language()
{
    // Clear the current loaded language
    $CI =& get_instance();
    $CI->lang->is_loaded = array();
    $CI->lang->language = array();

    // Reset to the default language
    $CI->lang->load('ip', $CI->mdl_settings->setting('default_language'));
    $CI->lang->load('form_validation', $CI->mdl_settings->setting('default_language'));
    $CI->lang->load('custom', $CI->mdl_settings->setting('default_language'));
}

/**
 * Returns all available languages
 *
 * @return array
 */
function get_available_languages() {
    $CI =& get_instance();
    $CI->load->helper('directory');

    $languages = directory_map(APPPATH . 'language', true);
    sort($languages);

    for ($i = 0; $i < count($languages); $i++) {
        $languages[$i] = str_replace('/', '', $languages[$i]);
    }

    return $languages;
}
