<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

function date_formats()
{
    return array(
        'm/d/Y' => array(
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy'
        ),
        'm-d-Y' => array(
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy'
        ),
        'm.d.Y' => array(
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy'
        ),
        'Y/m/d' => array(
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd'
        ),
        'Y-m-d' => array(
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd'
        ),
        'Y.m.d' => array(
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd'
        ),
        'd/m/Y' => array(
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy'
        ),
        'd-m-Y' => array(
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy'
        ),
        'd-M-Y' => array(
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy'
        ),
        'd.m.Y' => array(
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy'
        ),
        'j.n.Y' => array(
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy'
        )
    );
}

function date_from_mysql($date, $ignore_post_check = false)
{
    if ($date <> '0000-00-00') {
        if (!$_POST or $ignore_post_check) {
            $CI = &get_instance();

            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format($CI->mdl_settings->setting('date_format'));
        }
        return $date;
    }
    return '';
}

function date_from_timestamp($timestamp)
{
    $CI = &get_instance();

    $date = new DateTime();
    $date->setTimestamp($timestamp);
    return $date->format($CI->mdl_settings->setting('date_format'));
}

function date_to_mysql($date)
{
    $CI = &get_instance();

    $date = DateTime::createFromFormat($CI->mdl_settings->setting('date_format'), $date);
    return $date->format('Y-m-d');
}

function date_format_setting()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['setting'];
}

function date_format_datepicker()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['datepicker'];
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * To be used when date is being output back to user
 * @param $date - user formatted date
 * @param $increment - interval (1D, 2M, 1Y, etc)
 * @return user formatted date
 */
function increment_user_date($date, $increment)
{
    $CI = &get_instance();

    $mysql_date = date_to_mysql($date);

    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));

    return $new_date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 * @param $date
 * @param $increment
 * @return date
 */
function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}
