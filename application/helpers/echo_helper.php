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
 * @param $output
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
 */
function _htmlsc($output)
{
    echo htmlspecialchars($output, ENT_QUOTES);
}

/**
 * Echo something with escaped HTML entities
 *
 * @param mixed $output
 */
function _htmle($output)
{
    echo htmlentities($output);
}

/**
 * Echo a language string with the trans helper
 *
 * @param string $line
 * @param string $id
 * @param null|string $default
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
 */
function _auto_link($str, $type = 'both', $popup = FALSE) {
    echo auto_link(htmlsc($str), $type, $popup);
}
