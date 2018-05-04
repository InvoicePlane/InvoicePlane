<?php

/**
 * InvoicePlane Echo Helper
 *
 * @author         InvoicePlane Developers & Contributors
 * @copyright      Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license        https://invoiceplane.com/license.txt
 * @link           https://invoiceplane.com
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
    return htmlspecialchars($output, ENT_QUOTES);
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
    echo htmlspecialchars($output, ENT_QUOTES);
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
    echo htmlentities($output);
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
 * Echo a language string with the trans helper
 *
 * @param string $line
 * @param string $id
 * @param null|string $default
 *
 * @return void
 */
function _trans($line, $id = null, $default = null)
{
    echo trans($line, $id, $default);
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
