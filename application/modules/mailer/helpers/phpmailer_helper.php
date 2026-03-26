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
 * Custom debug output function for PHPMailer
 * Logs debug messages to CodeIgniter's log files instead of echoing to output
 * This prevents AJAX requests from breaking due to unexpected output
 *
 * Note: The file_security helper must be loaded before PHPMailer is configured.
 * This is handled in phpmail_send() which loads the helper at initialization.
 *
 * @param string $str   Debug message from PHPMailer
 * @param int    $level Debug level (not currently used by PHPMailer)
 *
 * @return void
 */
function phpmailer_debug_output(string $str, int $level = 0): void
{
    // Sanitize the debug output before logging to prevent log injection
    // Note: sanitize_for_logging is available because file_security helper
    // is loaded at the start of phpmail_send()
    $sanitized = sanitize_for_logging($str);
    
    // Log with 'debug' level so it respects log_threshold setting
    log_message('debug', 'PHPMailer: ' . $sanitized);
}

/**
 * @param $from
 * @param $to
 * @param $subject
 * @param $message
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
    $CI->load->helper('file_security');

    // Create the basic mailer object
    $mail          = new \PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->isHTML();

    // Set msg from PHPMailer in user lang. Only work with 2 letters. See phpmailer.lang-fr.php (in vendor dir).
    $mail->setLanguage(trans('cldr')); // Default ($langcode = 'en', $lang_path = '')

    switch (get_setting('email_send_method')) {
        case 'smtp':
            $mail->isSMTP();
            // Enable debug output: 0 = off, 1 = client messages, 2 = client and server messages
            $mail->SMTPDebug   = env_bool('ENABLE_DEBUG') ? 2 : 0;
            // Use custom callable function to log to CodeIgniter logs instead of echo/error_log
            $mail->Debugoutput = 'phpmailer_debug_output';

            // Set the basic properties
            $mail->Host = get_setting('smtp_server_address');
            $mail->Port = get_setting('smtp_port');
            
            // Log SMTP connection attempt
            if (env_bool('ENABLE_DEBUG')) {
                log_message('debug', 'PHPMailer: Attempting SMTP connection to ' . 
                    sanitize_for_logging($mail->Host) . ':' . $mail->Port);
            }

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
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ];
            }

            break;
        case 'sendmail':
        case 'phpmail':
        case 'default':
            $mail->IsMail();
            break;
    }

    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = $mail->normalizeBreaks($mail->html2text($message));

    if (is_array($from)) {
        // This array should be address, name
        $mail->setFrom($from[0], $from[1]);
    } else {
        // This is just an address
        $mail->setFrom($from);
    }

    // Allow multiple recipients delimited by comma or semicolon
    $to = (str_contains($to, ',')) ? explode(',', $to) : explode(';', $to);

    // Add the addresses
    foreach ($to as $address) {
        $mail->addAddress($address);
    }

    if ($cc) {
        // Allow multiple CC's delimited by comma or semicolon
        $cc = (str_contains($cc, ',')) ? explode(',', $cc) : explode(';', $cc);

        // Add the CC's
        foreach ($cc as $address) {
            $mail->addCC($address);
        }
    }

    if ($bcc) {
        // Allow multiple BCC's delimited by comma or semicolon
        $bcc = (str_contains($bcc, ',')) ? explode(',', $bcc) : explode(';', $bcc);
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

    $xml_del = false;
    // Add the attachments if needed
    if ($attachment_path && get_setting('email_pdf_attachment')) {
        $mail->addAttachment($attachment_path);

        // eInvoicing replace ARCHIVE (pdf) to TEMP (xml) for no embed_xml - since 1.6.3
        $attachment_path = strtr($attachment_path, [UPLOADS_ARCHIVE_FOLDER => UPLOADS_TEMP_FOLDER]);

        // The XML eInvoicing file exist in temporary?
        $xml_file = mb_rtrim($attachment_path, '.pdf') . '.xml';
        if (file_exists($xml_file)) {
            // Attach eInvoicing temp file
            if ( ! empty($_SERVER['CIIname'])) {
                // Need Specific eInvoice filename? (see mailer helper)
                $mail->addAttachment($xml_file, $_SERVER['CIIname']); // phpmailer-sent-attachment-as-other-name
            } else {
                $mail->addAttachment($xml_file);
            }

            // Need Delete
            $xml_del = true;
        }
    }

    // Add the other attachments if supplied
    if ($more_attachments) {
        foreach ($more_attachments as $paths) {
            $mail->addAttachment($paths['path'], $paths['filename']);
        }
    }

    // And away it goes...
    $ok = $mail->send();

    // Delete the tmp CII-XML file
    if ($xml_del) {
        unlink($xml_file);
    }

    // Log the result - handle failure case first (early return pattern)
    if (!$ok) {
        // Log the error with sanitized ErrorInfo
        log_message('error', 'PHPMailer: Email sending failed - ' . 
            sanitize_for_logging($mail->ErrorInfo));
        
        // Set flashdata for user notification
        $CI->session->set_flashdata('alert_error', $mail->ErrorInfo);
        
        return false;
    }
    
    // Log success if debug is enabled
    if (env_bool('ENABLE_DEBUG')) {
        // Format recipient list for logging
        $recipient_list = 'unknown';
        if (is_array($to)) {
            $recipient_list = implode(', ', $to);
        } elseif (is_string($to)) {
            $recipient_list = $to;
        }
        
        log_message('debug', 'PHPMailer: Email sent successfully to ' . 
            sanitize_for_logging($recipient_list));
    }

    return true;
}
