<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Shorthand for htmlspecialchars()
 *
 * @param $output
 *
 * @return string
 */
function htmlsc($output)
{
    if(!is_null($output))
    return htmlspecialchars($output, ENT_QUOTES | ENT_IGNORE);
    return $output;
}

/**
 * Echo something with escaped HTML special chars
 *
 * @param mixed $output
 *
 * @return void
 */
function _htmlsc($output)
{
    if ($output==null) return '';
    echo htmlspecialchars($output, ENT_QUOTES | ENT_IGNORE);
}

/**
 * Echo something with escaped HTML entities
 *
 * @param mixed $output
 *
 * @return void
 */
function _htmle($output)
{
    if ($output==null) return '';
    echo htmlentities($output, ENT_COMPAT);
}

/**
 * Echo a language string with the trans helper
 *
 * @param string $line
 * @param string $id
 * @param null|string $default
 *
 * @return void
 */
function _trans($line, $id = '', $default = null)
{
    echo trans($line, $id, $default);
}

/**
 * Echo for the auto link function with special chars handling
 *
 * @param $str
 * @param string $type
 * @param bool $popup
 *
 * @return void
 */
function _auto_link($str, $type = 'both', $popup = false)
{
    echo auto_link(htmlsc($str), $type, $popup);
}

/**
 * Output the standard CSRF protection field
 *
 * @return void
 */
function _csrf_field()
{
    $CI = &get_instance();
    echo '<input type="hidden" name="' . $CI->config->item('csrf_token_name');
    echo '" value="' . $CI->security->get_csrf_hash() . '">';
}

/**
 * Returns the correct URL for a asset within the theme directory
 * Also appends the current version to the asset to prevent browser caching issues
 *
 * @param string $asset
 *
 * @return void
 */
function _theme_asset($asset)
{
    echo base_url() . 'assets/' . get_setting('system_theme', 'invoiceplane');
    echo '/' . $asset . '?v=' . get_setting('current_version');
}

/**
 * Returns the correct URL for a asset within the core directory
 * Also appends the current version to the asset to prevent browser caching issues
 *
 * @param string $asset
 *
 * @return void
 */
function _core_asset($asset)
{
    echo base_url() . 'assets/core/' . $asset . '?v=' . get_setting('current_version');
}
