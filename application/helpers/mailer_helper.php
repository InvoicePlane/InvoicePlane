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
 * Check if mail sending is configured in the settings.
 */
function mailer_configured(): bool
{
    $CI = &get_instance();

    return ($CI->mdl_settings->setting('email_send_method') == 'phpmail')
        || ($CI->mdl_settings->setting('email_send_method') == 'sendmail')
        || (($CI->mdl_settings->setting('email_send_method') == 'smtp') && ($CI->mdl_settings->setting('smtp_server_address')));
}

/**
 * Send an invoice via email.
 *
 * @param        $invoice_id
 * @param        $invoice_template
 * @param        $from
 * @param        $to
 * @param        $subject
 * @param string $body
 *
 * @return bool
 */
function email_invoice(
    string $invoice_id,
    $invoice_template,
    array $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null
) {
    $CI = & get_instance();

    $CI->load->helper([
        'mailer/phpmailer',
        'template',
        'invoice',
        'pdf',
    ]);

    $db_invoice = $CI->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

    if ($db_invoice->sumex_id == null) {
        $invoice = generate_invoice_pdf($invoice_id, false, $invoice_template);
    } else {
        $invoice = generate_invoice_sumex($invoice_id, false, $invoice_template, true);
    }

    // Need Specific eInvoice filename?
    if ( ! empty($_SERVER['CIIname'])) {
        // Use $options['CIIname' => '{{{tags}}}'] in your config (helpers/XMLconfigs)
        // Or set $_SERVER['CIIname'] in your generator (libraries/XMLtemplates)
        $_SERVER['CIIname'] = parse_template($db_invoice, $_SERVER['CIIname']);
    }

    $message = parse_template($db_invoice, $body);
    $subject = parse_template($db_invoice, $subject);
    $cc      = parse_template($db_invoice, $cc);
    $bcc     = parse_template($db_invoice, $bcc);
    $from    = [parse_template($db_invoice, $from[0]), parse_template($db_invoice, $from[1])];

    $errors = [];
    if ( ! validate_email_address($to)) {
        $errors[] = 'to_email';
    }

    if ( ! validate_email_address($from[0])) {
        $errors[] = 'from_email';
    }

    if ($cc && ! validate_email_address($cc)) {
        $errors[] = 'cc_email';
    }

    if ($bcc && ! validate_email_address($bcc)) {
        $errors[] = 'bcc_email';
    }

    check_mail_errors($errors, 'mailer/invoice/' . $invoice_id);

    $message = (empty($message) ? ' ' : $message);

    return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc, $attachments);
}

/**
 * Send a quote via email.
 *
 * @param        $quote_id
 * @param        $quote_template
 * @param        $from
 * @param        $to
 * @param        $subject
 * @param string $body
 *
 * @return bool
 */
function email_quote(
    string $quote_id,
    $quote_template,
    array $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null
) {
    $CI = & get_instance();

    $CI->load->helper([
        'mailer/phpmailer',
        'template',
        'pdf',
    ]);

    $quote = generate_quote_pdf($quote_id, false, $quote_template);

    $db_quote = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();

    $message = parse_template($db_quote, $body);
    $subject = parse_template($db_quote, $subject);
    $cc      = parse_template($db_quote, $cc);
    $bcc     = parse_template($db_quote, $bcc);
    $from    = [parse_template($db_quote, $from[0]), parse_template($db_quote, $from[1])];

    $errors = [];
    if ( ! validate_email_address($to)) {
        $errors[] = 'to_email';
    }

    if ( ! validate_email_address($from[0])) {
        $errors[] = 'from_email';
    }

    if ($cc && ! validate_email_address($cc)) {
        $errors[] = 'cc_email';
    }

    if ($bcc && ! validate_email_address($bcc)) {
        $errors[] = 'bcc_email';
    }

    check_mail_errors($errors, 'mailer/quote/' . $quote_id);

    $message = (empty($message) ? ' ' : $message);

    return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc, $attachments);
}

/**
 * Send an email if the status of an email changed.
 *
 * @param        $quote_id
 * @param string $status   string "accepted" or "rejected"
 *
 * @return bool if the email was sent
 */
function email_quote_status(string $quote_id, $status)
{
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);

    if ( ! mailer_configured()) {
        return false;
    }

    $CI = & get_instance();
    $CI->load->helper('mailer/phpmailer');

    $quote    = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();
    $index    = env('REMOVE_INDEXPHP', true) ? '' : 'index.php';
    $base_url = base_url('/' . $index . '/quotes/view/' . $quote_id);

    $user_email = $quote->user_email;
    $subject    = sprintf(
        trans('quote_status_email_subject'),
        $quote->client_name,
        mb_strtolower(lang($status)),
        $quote->quote_number
    );
    $body = sprintf(
        nl2br(trans('quote_status_email_body')),
        $quote->client_name,
        mb_strtolower(lang($status)),
        $quote->quote_number,
        '<a href="' . $base_url . '">' . $base_url . '</a>'
    );

    return phpmail_send($user_email, $user_email, $subject, $body);
}

/**
 * Validate email address syntax
 * $email string can be a single email or a list of emails.
 * The emails can either be comma (,) or semicolon (;) separated.
 *
 * @param string $email
 *
 * @return bool returs true if all emails are valid otherwise false
 */
function validate_email_address(string $email): bool
{
    $emails = (str_contains($email, ',')) ? explode(',', $email) : explode(';', $email);

    foreach ($emails as $emailItem) {
        if ( ! filter_var($emailItem, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    }

    return true;
}

/**
 * @param []  $errors
 * @param string $redirect
 */
function check_mail_errors(array $errors = [], $redirect = ''): void
{
    if ($errors) {
        $CI = & get_instance();
        foreach ($errors as $i => $e) {
            $errors[$i] = strtr(trans('form_validation_valid_email'), ['{field}' => trans($e)]);
        }

        $CI->session->set_flashdata('alert_error', implode('<br>', $errors));

        // Use provided redirect, or validate HTTP_REFERER against base_url
        if (empty($redirect)) {
            $referer  = $_SERVER['HTTP_REFERER'] ?? '';
            $base_url = base_url();
            
            // Parse both URLs and compare scheme, host, and port for same-origin validation
            if ( ! empty($referer)) {
                $referer_parts  = parse_url($referer);
                $base_url_parts = parse_url($base_url);
                
                // Reject if either URL is invalid or missing required components
                if ($referer_parts === false || $base_url_parts === false ||
                    ! isset($referer_parts['scheme']) || ! isset($referer_parts['host']) ||
                    ! isset($base_url_parts['scheme']) || ! isset($base_url_parts['host'])) {
                    $redirect = base_url(); // Safe default
                } else {
                    // Normalize ports (default to 80 for http, 443 for https if not specified)
                    $referer_port  = $referer_parts['port'] ?? ($referer_parts['scheme'] === 'https' ? 443 : 80);
                    $base_url_port = $base_url_parts['port'] ?? ($base_url_parts['scheme'] === 'https' ? 443 : 80);
                    
                    // Compare scheme, host, and port
                    if ($referer_parts['scheme'] === $base_url_parts['scheme'] &&
                        $referer_parts['host'] === $base_url_parts['host'] &&
                        $referer_port === $base_url_port) {
                        $redirect = $referer;
                    } else {
                        $redirect = base_url(); // Safe default
                    }
                }
            } else {
                $redirect = base_url(); // Safe default
            }
        }

        redirect($redirect);
    }
}
