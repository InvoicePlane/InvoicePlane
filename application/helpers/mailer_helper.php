<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

function mailer_configured()
{
    $CI = & get_instance();

    return (($CI->mdl_settings->setting('email_send_method') == 'phpmail') OR
        ($CI->mdl_settings->setting('email_send_method') == 'sendmail') OR
        (($CI->mdl_settings->setting('email_send_method') == 'smtp') AND ($CI->mdl_settings->setting('smtp_server_address')))
        );
}

function email_invoice($invoice_id, $invoice_template, $from, $to, $subject, $body, $cc = NULL, $bcc = NULL)
{
    $CI = & get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('invoice');
    $CI->load->helper('pdf');

    $invoice = generate_invoice_pdf($invoice_id, FALSE, $invoice_template);

    $db_invoice = $CI->mdl_invoices->where('fi_invoices.invoice_id', $invoice_id)->get()->row();

    $message = nl2br(parse_template($db_invoice, $body));

    return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc);
}

function email_quote($quote_id, $quote_template, $from, $to, $subject, $body, $cc = NULL, $bcc = NULL)
{
    $CI = & get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('pdf');

    $quote = generate_quote_pdf($quote_id, FALSE, $quote_template);

    $db_quote = $CI->mdl_quotes->where('fi_quotes.quote_id', $quote_id)->get()->row();

    $message = nl2br(parse_template($db_quote, $body));

    return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc);
}