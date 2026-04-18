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

if (!function_exists('is_zip_before_city')) {
    /**
     * Determines the address format (ZIP before City or vice-versa) based on the country.
     *
     * @param string $country_code The ISO country code (e.g., 'DE', 'US').
     * @return bool True if the ZIP code should be placed before the City.
     */
    function is_zip_before_city($country_code = '')
    {
        // If no country is provided, fallback to the system's default country setting
        if (empty($country_code)) {
            $country_code = get_setting('default_country');
        }

        // List of countries that typically use the "ZIP City" format
        $zip_before_city_countries = [
            'AT', 'BE', 'CH', 'CZ', 'DE', 'DK', 'ES', 'FI', 
            'FR', 'GR', 'IT', 'LU', 'NL', 'NO', 'PL', 'PT', 
            'SE', 'SK', 'TR', 'VN', 'CN'
        ];

        return in_array(strtoupper($country_code), $zip_before_city_countries);
    }
}
