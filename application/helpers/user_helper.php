<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * @param mixed id or object $user - since 1.6.3
 * @return string
 */
function format_user($user): string
{
    // Get an id
    if ($user && is_numeric($user)) {
        $CI = & get_instance();
        if (! property_exists($CI, 'mdl_users')) {
            $CI->load->model('users/mdl_users');
        }

        $user = $CI->mdl_users->get_by_id($user);
    }

    // Not exist or find, Stop.
    if (empty($user->user_name)) {
        return '';
    }

    $user_company = empty($user->user_company) ? '' : ' - ' . $user->user_company;
    $contact = empty($user->user_invoicing_contact) ? '' : ' - ' . $user->user_invoicing_contact;

    return ucfirst($user->user_name) . $user_company . $contact;
}
