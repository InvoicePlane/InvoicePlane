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

#[AllowDynamicProperties]
class Cron extends Base_Controller
{
    /**
     * @param string|null $cron_key
     */
    public function recur($cron_key = null)
    {
        $this->load->helper('file_security');

        // Reject once the failed-attempt threshold is hit, before even checking the key,
        // so this endpoint's only credential can't be brute-forced at unlimited speed.
        if ($this->_is_cron_rate_limited()) {
            log_message('warning', '[Cron Recurring Invoices] Rate limit exceeded from: '
                . sanitize_for_logging($this->input->ip_address()));
            show_error(trans('wrong_cron_key_provided'), 429);
            exit('Too many attempts.');
        }

        // Check the provided cron key
        if ( ! hash_equals((string) get_setting('cron_key'), (string) $cron_key)) {
            $this->_record_cron_rate_limit_attempt();
            log_message('error', '[Cron Recurring Invoices] Wrong cron key provided! '
                . sanitize_for_logging((string) $cron_key));
            show_error(trans('wrong_cron_key_provided'), 500);
            exit('Wrong cron key!');
        }

        $this->_reset_cron_rate_limit();

        $this->load->model([
            'invoices/mdl_invoices_recurring',
            'invoices/mdl_invoices',
            'invoices/mdl_invoice_amounts',
        ]);
        $this->load->helper('mailer');

        // Gather a list of recurring invoices to generate
        $invoices_recurring = $this->mdl_invoices_recurring->active()->get()->result();
        $recurInfo          = [];
        foreach ($invoices_recurring as $invoice_recurring) {
            $recurInfo = [
                'invoice_id'           => $invoice_recurring->invoice_id,
                'client_id'            => $invoice_recurring->client_id,
                'invoice_group_id'     => $invoice_recurring->invoice_group_id,
                'invoice_status_id'    => $invoice_recurring->invoice_status_id,
                'invoice_number'       => $invoice_recurring->invoice_number,
                'invoice_recurring_id' => $invoice_recurring->invoice_recurring_id,
                'recur_start_date'     => $invoice_recurring->recur_start_date,
                'recur_end_date'       => $invoice_recurring->recur_end_date,
                'recur_frequency'      => $invoice_recurring->recur_frequency,
                'recur_next_date'      => $invoice_recurring->recur_next_date,
                'recur_status'         => $invoice_recurring->recur_status,
            ];

            if (IP_DEBUG) {
                log_message('debug', '[Cron Recurring Invoices] Recurring Info: ' . json_encode($recurInfo, JSON_PRETTY_PRINT));
            }

            // This is the original invoice id
            $source_id = $invoice_recurring->invoice_id;

            // This is the original invoice
            $invoice = $this->mdl_invoices->get_by_id($source_id);

            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                $this->load->helper('e-invoice');
                // Only for shift legacy_calculation mode
                get_einvoice_usage($invoice, [], false);
            }

            // Create the new invoice
            $db_array = [
                'client_id'                => $invoice->client_id,
                'payment_method'           => $invoice->payment_method,
                'invoice_date_created'     => $invoice_recurring->recur_next_date,
                'invoice_date_due'         => $this->mdl_invoices->get_date_due($invoice_recurring->recur_next_date),
                'invoice_group_id'         => $invoice->invoice_group_id,
                'user_id'                  => $invoice->user_id,
                'invoice_number'           => $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id),
                'invoice_url_key'          => $this->mdl_invoices->get_url_key(),
                'invoice_terms'            => $invoice->invoice_terms,
                'invoice_discount_amount'  => $invoice->invoice_discount_amount,
                'invoice_discount_percent' => $invoice->invoice_discount_percent,
            ];

            // Claim the recurring invoice and create/copy it in one transaction.
            // Claiming (the conditioned recur_next_date update) prevents a concurrent
            // cron run from also processing this row; wrapping it with the invoice
            // creation means that if creation/copy fails, the claim rolls back too,
            // so this billing cycle is retried on the next run instead of the
            // schedule silently advancing with no invoice ever generated.
            $this->db->trans_start();

            $claimed = $this->mdl_invoices_recurring->claim_for_processing(
                $invoice_recurring->invoice_recurring_id,
                $invoice_recurring->recur_next_date,
                $invoice_recurring->recur_frequency
            );

            $target_id = null;
            if ($claimed) {
                // This is the new invoice id
                $target_id = $this->mdl_invoices->create($db_array, false);

                // Copy the original invoice to the new invoice
                $this->mdl_invoices->copy_invoice($source_id, $target_id, false);
            }

            $this->db->trans_complete();

            if ( ! $claimed) {
                if (IP_DEBUG) {
                    log_message('debug', '[Cron Recurring Invoices] Recurring Invoice with id '
                        . $invoice_recurring->invoice_recurring_id . ' was already claimed by another run');
                }
                continue;
            }

            if ( ! $this->db->trans_status()) {
                log_message('error', '[Cron Recurring Invoices] Failed to create/copy invoice for recurring id '
                    . $invoice_recurring->invoice_recurring_id . '; schedule advance rolled back, will retry next run');
                continue;
            }

            if (IP_DEBUG) {
                log_message('debug', '[Cron Recurring Invoices] Recurring Invoice with id ' . $target_id . ' was created');
                log_message('debug', '[Cron Recurring Invoices] Recurring Invoice with sourceId ' . $source_id . ' was copied to id ' . $target_id);
            }

            // Email the new invoice if applicable
            if (get_setting('automatic_email_on_recur') && mailer_configured()) {
                $new_invoice = $this->mdl_invoices->get_by_id($target_id);

                // Set the email body, use default email template if available
                $this->load->model('email_templates/mdl_email_templates');

                $email_template_id = get_setting('email_invoice_template');
                if ( ! $email_template_id) {
                    log_message('error', '[Cron Recurring Invoices] No email template set in the system settings!');
                    continue;
                }

                $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();
                if ($email_template->num_rows() == 0) {
                    log_message('error', '[Cron Recurring Invoices] No email template set in the system settings!');
                    continue;
                }

                $tpl = $email_template->row();

                // Prepare the attachments
                $this->load->model('upload/mdl_uploads');
                $attachment_files = $this->mdl_uploads->get_invoice_uploads($target_id);

                // Load helper for email body processing
                $this->load->helper('html_sanitizer');

                // Prepare the body
                // Re-sanitize template body to ensure legacy DB rows are cleaned.
                // This provides defense-in-depth protection against any templates that may have
                // been stored before HTML Purifier sanitization was implemented.
                $body = sanitize_email_template_html($tpl->email_template_body);

                // Apply nl2br only to plain text content (after sanitization)
                if (is_plain_text($body)) {
                    // Plain text - convert line breaks to <br> tags
                    $body = nl2br($body);
                }
                // Note: We removed htmlspecialchars_decode() as it was undoing the XSS protection.
                // The sanitized HTML is used directly without decoding.

                // Determine sender email: use template value, then smtp_mail_from setting, then fall back to user email
                if ( ! empty($tpl->email_template_from_email)) {
                    $from = [$tpl->email_template_from_email, $tpl->email_template_from_name];
                } else {
                    $default_from_email = get_setting('smtp_mail_from');
                    if (empty($default_from_email)) {
                        $default_from_email = $invoice->user_email;
                    }
                    $from = [$default_from_email, ''];
                }

                $subject = empty($tpl->email_template_subject)
                    ? trans('invoice') . ' #' . $new_invoice->invoice_number
                    : $tpl->email_template_subject;

                $pdf_template = $tpl->email_template_pdf_template;
                $to           = $invoice->client_email;
                $cc           = $tpl->email_template_cc;
                $bcc          = $tpl->email_template_bcc;

                $email_invoice = email_invoice($target_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files);

                if ($email_invoice) {
                    $this->mdl_invoices->mark_sent($target_id);
                } else {
                    log_message('error', '[Cron Recurring Invoices] Invoice ' . $target_id . 'could not be sent. Please review your Email settings.');
                }
            } else {
                log_message('error', '[Cron Recurring Invoices] Automatic_email_on_recur was not set or mailer was not configured');
            }
        }

        if (IP_DEBUG) {
            log_message('debug', '[Cron Recurring Invoices] ' . count($invoices_recurring) . ' recurring invoices processed');
        }
    }

    /**
     * Returns true when the current IP has exceeded the wrong-cron-key attempt
     * threshold. Backed by the ip_login_log table (mirrors the session-login
     * and password-reset IP rate limiters) since this endpoint is unauthenticated
     * and has no session to key off.
     */
    private function _is_cron_rate_limited(): bool
    {
        $max_attempts   = (int) env('CRON_IP_MAX_ATTEMPTS', 10);
        $window_minutes = (int) env('CRON_IP_WINDOW_MINUTES', 15);
        $log            = $this->_cron_rate_limit_log();

        if (empty($log) || $log->log_count < $max_attempts) {
            return false;
        }

        return $this->_cron_rate_limit_log_is_within_window($log, $window_minutes * 60);
    }

    /**
     * Records one wrong-cron-key attempt for the current IP, resetting the
     * counter first if the previous window has already elapsed.
     */
    private function _record_cron_rate_limit_attempt(): void
    {
        $window_minutes = (int) env('CRON_IP_WINDOW_MINUTES', 15);
        $key            = $this->_cron_rate_limit_key();
        $log            = $this->_cron_rate_limit_log();

        if ( ! empty($log) && ! $this->_cron_rate_limit_log_is_within_window($log, $window_minutes * 60)) {
            $this->_reset_cron_rate_limit();
        }

        // Atomic upsert: concurrent wrong-key attempts from the same IP would race
        // on a read-then-write log_count + 1, undercounting attempts under load.
        // login_name is this table's primary key, so this increments in one step.
        $this->db->query(
            'INSERT INTO ip_login_log (login_name, log_count, log_create_timestamp)
             VALUES (?, 1, ?)
             ON DUPLICATE KEY UPDATE log_count = log_count + 1, log_create_timestamp = VALUES(log_create_timestamp)',
            [$key, date('c')]
        );
    }

    /**
     * Clears the wrong-cron-key attempt counter for the current IP.
     */
    private function _reset_cron_rate_limit(): void
    {
        $this->db->delete('ip_login_log', ['login_name' => $this->_cron_rate_limit_key()]);
    }

    private function _cron_rate_limit_log()
    {
        return $this->db->where('login_name', $this->_cron_rate_limit_key())->get('ip_login_log')->row();
    }

    private function _cron_rate_limit_log_is_within_window(object $log, int $window_seconds): bool
    {
        try {
            $timestamp = new DateTime($log->log_create_timestamp);
        } catch (Exception) {
            return false;
        }

        return $timestamp->getTimestamp() > (time() - $window_seconds);
    }

    private function _cron_rate_limit_key(): string
    {
        return 'cron_key:' . hash('sha256', $this->input->ip_address());
    }
}
