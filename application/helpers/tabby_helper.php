<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2026 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Tabby is offered only for invoices in dirhams: the integration talks to Tabby UAE (api.tabby.ai).
 */
function tabby_enabled(): bool
{
    return get_setting('enable_online_payments') == 1
        && get_setting('gateway_tabby_enabled') == 1
        && mb_strtoupper(trim((string) get_setting('currency_code'))) === 'AED';
}

/**
 * Whether the "Pay later with Tabby" button is shown for an invoice. Tabby needs the
 * buyer's email address and mobile number, so it is hidden when the client has none.
 */
function tabby_available(object $invoice): bool
{
    if ( ! tabby_enabled()
        || ! in_array((int) $invoice->invoice_status_id, [2, 3], true)
        || (float) $invoice->invoice_balance <= 0
    ) {
        return false;
    }

    $invoice_method = (int) $invoice->payment_method;
    $tabby_method   = (int) get_setting('gateway_tabby_payment_method');

    if ($invoice_method !== 0 && $tabby_method !== 0 && $invoice_method !== $tabby_method) {
        return false;
    }

    return tabby_buyer_email($invoice) !== '' && tabby_buyer_phone($invoice) !== '';
}

/**
 * The first valid address: client_email may hold a comma separated list.
 */
function tabby_buyer_email(object $invoice): string
{
    foreach (explode(',', (string) ($invoice->client_email ?? '')) as $email) {
        $email = trim($email);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            return $email;
        }
    }

    return '';
}

function tabby_buyer_phone(object $invoice): string
{
    $mobile = trim((string) ($invoice->client_mobile ?? ''));

    return $mobile !== '' ? $mobile : trim((string) ($invoice->client_phone ?? ''));
}

function tabby_amount(int|float|string $amount): string
{
    return number_format((float) $amount, 2, '.', '');
}

/**
 * The Hosted Payment Page link of a new checkout session must point at Tabby before we redirect to it.
 */
function tabby_valid_checkout_url(string $url): bool
{
    $parts = parse_url($url);

    return is_array($parts)
        && ($parts['scheme'] ?? '') === 'https'
        && preg_match('/(^|\.)tabby\.ai$/i', (string) ($parts['host'] ?? '')) === 1;
}

/**
 * Tabby's approved wording for a rejected checkout session.
 */
function tabby_rejection_message(?string $reason): string
{
    return match ($reason) {
        'order_amount_too_high' => trans('tabby_order_amount_too_high'),
        'order_amount_too_low'  => trans('tabby_order_amount_too_low'),
        default                 => trans('tabby_rejected'),
    };
}

/**
 * The checkout session request for the invoice balance. The invoice is sent as a single
 * order line so the amount always matches the sum of the lines, as Tabby requires.
 */
function tabby_checkout_payload(object $invoice, string $merchant_code, array $merchant_urls, int $loyalty_level): array
{
    $amount    = tabby_amount($invoice->invoice_balance);
    $reference = (string) ($invoice->invoice_number ?: $invoice->invoice_id);
    $title     = trans('invoice') . ' #' . $reference;
    $city      = trim((string) $invoice->client_city) ?: trim((string) $invoice->client_state);
    $address   = trim($invoice->client_address_1 . ' ' . $invoice->client_address_2) ?: $city;
    $created   = strtotime((string) $invoice->client_date_created);

    return [
        'payment' => [
            'amount'      => $amount,
            'currency'    => 'AED',
            'description' => $title,
            'buyer'       => [
                'name'  => format_client($invoice, false),
                'email' => tabby_buyer_email($invoice),
                'phone' => tabby_buyer_phone($invoice),
            ],
            'shipping_address' => [
                'city'    => $city,
                'address' => $address,
                'zip'     => trim((string) $invoice->client_zip) ?: '00000',
            ],
            'order' => [
                'reference_id' => $reference,
                'items'        => [
                    [
                        'title'      => $title,
                        'quantity'   => 1,
                        'unit_price' => $amount,
                        'category'   => 'Invoice',
                    ],
                ],
            ],
            'buyer_history' => [
                'registered_since' => gmdate('Y-m-d\TH:i:s\Z', $created ?: time()),
                'loyalty_level'    => $loyalty_level,
            ],
            'order_history' => [],
        ],
        'lang'          => str_starts_with((string) trans('cldr'), 'ar') ? 'ar' : 'en',
        'merchant_code' => $merchant_code,
        'merchant_urls' => $merchant_urls,
    ];
}
