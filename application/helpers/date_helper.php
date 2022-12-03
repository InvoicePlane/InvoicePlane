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
 * Available date formats
 * The setting value represents the PHP date() formatting, the datepicker value represents the
 * DatePicker formatting (see http://bootstrap-datepicker.readthedocs.io/en/stable/options.html#format)
 *
 * @return array
 */
function date_formats()
{
    return [
        'd/m/Y' => [
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy',
        ],
        'd-m-Y' => [
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy',
        ],
        'd-M-Y' => [
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy',
        ],
        'd.m.Y' => [
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy',
        ],
        'j.n.Y' => [
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy',
        ],
        'd M,Y' => [
            'setting' => 'd M,Y',
            'datepicker' => 'dd M,yyyy',
        ],
        'm/d/Y' => [
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy',
        ],
        'm-d-Y' => [
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy',
        ],
        'm.d.Y' => [
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy',
        ],
        'Y/m/d' => [
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd',
        ],
        'Y-m-d' => [
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd',
        ],
        'Y.m.d' => [
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd',
        ],
    ];
}

/**
 * @param      $date
 * @param bool $ignore_post_check
 * @return bool|DateTime|string
 */
function date_from_mysql($date, $ignore_post_check = false)
{
    if ($date) {
        if (!$_POST or $ignore_post_check) {
            $CI = &get_instance();

            if ($date != null) {
                $date = DateTime::createFromFormat('Y-m-d', $date);
                return $date->format($CI->mdl_settings->setting('date_format'));
            }
            else {
                return '';
            }
        }
        return $date;
    }
    return '';
}

/**
 * @param $timestamp
 * @return string
 */
function date_from_timestamp($timestamp)
{
    $CI = &get_instance();

    $date = new DateTime();
    $date->setTimestamp($timestamp);
    return $date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * @param $date
 * @return string
 */
function date_to_mysql($date)
{
    $CI = &get_instance();
    if ($date == null) {
        return '';
    }
    $date = DateTime::createFromFormat($CI->mdl_settings->setting('date_format'), $date);
    return $date->format('Y-m-d');
}

/**
 * @param $date
 * @return bool
 */
function is_date($date)
{
    $CI = &get_instance();
    $format = $CI->mdl_settings->setting('date_format');
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * @return string
 */
function date_format_setting()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['setting'];
}

/**
 * @return string
 */
function date_format_datepicker()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['datepicker'];
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * To be used when date is being output back to user.
 *
 * @param $date      - user formatted date
 * @param $increment - interval (1D, 2M, 1Y, etc)
 * @return string
 */
function increment_user_date($date, $increment)
{
    $CI = &get_instance();

    if ($date == null) {
        return '';
    }

    $mysql_date = date_to_mysql($date);

    $new_date = new DateTime($mysql_date);

    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 *
 * @param $date
 * @param $increment
 * @return string
 */
function increment_date($date, $increment)
{
    if ($date==null) {
        return '';
    }
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');

}
