<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
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

    $db_invoice = $CI->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

    $message = nl2br(parse_template($db_invoice, $body));
    $subject = parse_template($db_invoice, $subject);
    $cc = parse_template($db_invoice, $cc);
    $bcc = parse_template($db_invoice, $bcc);
    $from = array(parse_template($db_invoice, $from[0]), parse_template($db_invoice, $from[1]));

    return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc);
}

function email_quote($quote_id, $quote_template, $from, $to, $subject, $body, $cc = NULL, $bcc = NULL)
{
    $CI = & get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('pdf');

    $quote = generate_quote_pdf($quote_id, FALSE, $quote_template);

    $db_quote = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();

    $message = nl2br(parse_template($db_quote, $body));
    $subject = parse_template($db_quote, $subject);
    $cc = parse_template($db_quote, $cc);
    $bcc = parse_template($db_quote, $bcc);
    $from = array(parse_template($db_quote, $from[0]), parse_template($db_quote, $from[1]));

    return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc);
}