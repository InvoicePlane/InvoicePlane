<?php

if ( ! defined('BASEPATH')) { exit('No direct script access allowed'); }

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * @AllowDynamicProperties
 */
/**
 * @param null $attachment_path
 * @param null $cc
 * @param null $bcc
 * @param null $more_attachments
 *
 * @return bool
 */
function phpmail_send(
    $from,
    $to,
    $subject,
    $message,
    $attachment_path = null,
    $cc = null,
    $bcc = null,
    $more_attachments = null
) {
    $CI = &get_instance();
    $CI->load->library('crypt');

    // Create the basic mailer object
    $mail = new \PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->isHTML();

    switch (get_setting('email_send_method')) {
        case 'smtp':
            $mail->IsSMTP();

            // Set the basic properties
            $mail->Host = get_setting('smtp_server_address');
            $mail->Port = get_setting('smtp_port');

            // Is SMTP authentication required?
            if (get_setting('smtp_authentication')) {
                $mail->SMTPAuth = true;

                $decoded = $CI->crypt->decode($CI->mdl_settings->get('smtp_password'));

                $mail->Username = get_setting('smtp_username');
                $mail->Password = $decoded;
            }

            // Is a security method required?
            if (get_setting('smtp_security')) {
                $mail->SMTPSecure = get_setting('smtp_security');
            }

            // Check if certificates should not be verified
            if ( ! get_setting('smtp_verify_certs', true)) {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];
            }

            break;
        case 'sendmail':
            $mail->IsMail();
            break;
        case 'phpmail':
        case 'default':
            $mail->IsMail();
            break;
    }

    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = $mail->normalizeBreaks($mail->html2text($message));

    if (is_array($from)) {
        // This array should be address, name
        $mail->setFrom($from[0], $from[1]);
    } else {
        // This is just an address
        $mail->setFrom($from);
    }

    // Allow multiple recipients delimited by comma or semicolon
    $to = (mb_strpos($to, ',')) ? explode(',', $to) : explode(';', $to);

    // Add the addresses
    foreach ($to as $address) {
        $mail->addAddress($address);
    }

    if ($cc) {
        // Allow multiple CC's delimited by comma or semicolon
        $cc = (mb_strpos($cc, ',')) ? explode(',', $cc) : explode(';', $cc);

        // Add the CC's
        foreach ($cc as $address) {
            $mail->addCC($address);
        }
    }

    if ($bcc) {
        // Allow multiple BCC's delimited by comma or semicolon
        $bcc = (mb_strpos($bcc, ',')) ? explode(',', $bcc) : explode(';', $bcc);
        // Add the BCC's
        foreach ($bcc as $address) {
            $mail->addBCC($address);
        }
    }

    if (get_setting('bcc_mails_to_admin') == 1) {
        // Get email address of admin account and push it to the array
        $CI->load->model('users/mdl_users');
        $CI->db->where('user_id', 1);
        $admin = $CI->db->get('ip_users')->row();
        $mail->addBCC($admin->user_email);
    }

    // Add the attachments if needed - eInvoicing++
    if ($attachment_path && get_setting('email_pdf_attachment')) {
        $attachFile = pathinfo($attachment_path);
        $dirname = $attachFile['dirname'];
        $filename = $attachFile['filename'];
        $file = $dirname . '/' . $filename;
        // check the date prefix: '2017-02-01'_ == today, then strip, copy and attach the new file)
        if (mb_substr($filename, 0, 10) == date('Y-m-d')) {
            $file = $dirname . '/' . substr_replace($filename, '', 0, 11);
            copy($attachment_path, $file . '.pdf');
        }
        $mail->addAttachment($file . '.pdf');

        // attach the UBL+ file
        $fullUblname = $dirname . '/' . $filename . '.xml';
        if (file_exists($file . '.xml')) {
            $mail->addAttachment($file . '.xml');
        } elseif (file_exists($fullUblname)) {
            $mail->addAttachment($fullUblname);
        }
    }
    // Add the other attachments if supplied
    if ($more_attachments) {
        foreach ($more_attachments as $paths) {
            $mail->addAttachment($paths['path'], $paths['filename']);
        }
    }

    // And away it goes...
    if ($mail->send()) {
        $CI->session->set_flashdata('alert_success', 'The email has been sent');

        return true;
    }
    // Or not...
    $CI->session->set_flashdata('alert_error', $mail->ErrorInfo);

    return false;
}
