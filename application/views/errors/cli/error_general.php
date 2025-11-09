<?php

defined('BASEPATH') || exit('No direct script access allowed');

// Simple translation function for error pages
function error_trans($key) {
    static $translations = null;
    if ($translations === null) {
        $lang_file = APPPATH . 'language/english/ip_lang.php';
        if (file_exists($lang_file)) {
            include $lang_file;
            if (isset($lang) && is_array($lang)) {
                $translations = $lang;
            }
        }
        if ($translations === null) {
            $translations = [];
        }
    }
    return isset($translations[$key]) ? $translations[$key] : $key;
}

echo "\n", error_trans('error_prefix'), ": ",
$heading,
"\n\n",
$message,
"\n\n";
