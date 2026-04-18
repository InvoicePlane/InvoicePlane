<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * @param obj|int $client     (or id - since 1.6.3)
 * @param bool    $show_title - since 1.6.3
 */
function format_client($client, $show_title = true): string
{
    // Get an id
    if ($client && is_numeric($client)) {
        $CI = & get_instance();
        if ( ! property_exists($CI, 'mdl_clients')) {
            $CI->load->model('clients/mdl_clients');
        }

        $client = $CI->mdl_clients->get_by_id($client);
    }

    // Not exist or find, Stop.
    if (empty($client->client_name)) {
        return '';
    }

    $client_title = '';
    if ($show_title && ! empty($client->client_title)) {
        $client_title = ucfirst(in_array($client->client_title, ClientTitleEnum::VALUES, true) ? trans($client->client_title) : $client->client_title) . ' ';
    }

    return $client_title . $client->client_name . (empty($client->client_surname) ? '' : ' ' . $client->client_surname);
}

/**
 * @param string $gender
 *
 * @return string
 */
function format_gender($gender)
{
    if ($gender == 0) {
        return trans('gender_male');
    }

    if ($gender == 1) {
        return trans('gender_female');
    }

    return trans('gender_other');
}

if (!function_exists('get_zip_before_city_countries')) {
    /**
     * Returns an array of country codes that use the "ZIP before City" format.
     * @return array
     */
    function get_zip_before_city_countries()
    {
        return [
            'AT', 'BE', 'CH', 'CZ', 'DE', 'DK', 'ES', 'FI', 
            'FR', 'GR', 'IT', 'LU', 'NL', 'NO', 'PL', 'PT', 
            'SE', 'SK', 'TR', 'VN', 'CN'
        ];
    }
}

if (!function_exists('is_zip_before_city')) {
    /**
     * @param string $country_code
     * @return bool
     */
    function is_zip_before_city($country_code = '')
    {
        if (empty($country_code)) {
            $country_code = get_setting('default_country');
        }

        return in_array(strtoupper($country_code), get_zip_before_city_countries());
    }
}
