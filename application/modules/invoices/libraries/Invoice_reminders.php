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
 * Sends the payment reminders that are due today.
 *
 * Runs unattended from Cron::recur(), so the overriding concerns are that it never
 * sends the same reminder twice and never aborts the whole run over one bad record.
 * The scheduling decisions themselves live in invoice_reminder_helper.php as pure
 * functions; this class is the part that touches the database and the mailer.
 */
#[AllowDynamicProperties]
class Invoice_reminders
{
    /** @var CI_Controller */
    private $CI;

    /** Language the run started in, restored after each invoice. */
    private string $run_language = '';

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->helper(['invoice_reminder', 'mailer', 'template', 'html_sanitizer', 'file_security']);
        $this->CI->load->model([
            'invoices/mdl_invoices',
            'invoices/mdl_invoice_reminders',
            'email_templates/mdl_email_templates',
            'upload/mdl_uploads',
        ]);
    }

    /**
     * Send every reminder that is due. Returns a summary for the cron log.
     *
     * @return array{candidates: int, sent: int, failed: int, skipped: int}
     */
    public function run(): array
    {
        $result = ['candidates' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        $schedule = $this->load_schedule();
        if ($schedule === null) {
            return $result;
        }

        $templates = $this->resolve_templates();

        $candidates           = $this->CI->mdl_invoice_reminders->eligible_invoices();
        $result['candidates'] = count($candidates);

        if ($candidates === []) {
            return $result;
        }

        $invoice_ids = array_map(static fn ($invoice): int => (int) $invoice->invoice_id, $candidates);
        $logged      = $this->CI->mdl_invoice_reminders->logged_slots_for($invoice_ids);
        $sent_counts = $this->CI->mdl_invoice_reminders->sent_counts_for($invoice_ids);

        $this->run_language = (string) get_setting('default_language');

        foreach ($candidates as $candidate) {
            $invoice_id = (int) $candidate->invoice_id;

            try {
                $this->process($candidate, $invoice_id, $schedule, $templates, $logged, $sent_counts, $result);
            } catch (Throwable $e) {
                // One unreadable upload, one broken PDF template or one mPDF failure
                // must not cost every later invoice its reminder.
                $result['failed']++;
                log_message(
                    'error',
                    '[Cron Invoice Reminders] Invoice ' . $invoice_id . ' failed: '
                    . sanitize_for_logging($e->getMessage())
                );
            } finally {
                // generate_invoice_pdf() switches the process language to the client's
                // and never switches back, so without this every later iteration — and
                // the recurring pass that follows — would run in the last client's
                // language.
                if ($this->run_language !== '') {
                    set_language($this->run_language);
                }
            }
        }

        return $result;
    }

    /**
     * Decide and send for one invoice.
     *
     * @param array<int, string[]> $logged
     * @param array<int, int>      $sent_counts
     */
    private function process(
        object $candidate,
        int $invoice_id,
        array $schedule,
        array $templates,
        array $logged,
        array $sent_counts,
        array &$result
    ): void {
        $plan = reminder_schedule_for_invoice(
            (int) $candidate->days_relative,
            $schedule['before'],
            $schedule['after'],
            $schedule['repeat'],
            $logged[$invoice_id] ?? []
        );

        // Offsets whose day has passed without being sent are recorded as skipped so
        // they are never reconsidered — this is what keeps a cron outage from turning
        // into a burst of stale reminders on the next run.
        foreach ($plan['skip'] as $slot) {
            $this->CI->mdl_invoice_reminders->claim($invoice_id, $slot['type'], $slot['offset'], 'skipped');
            $result['skipped']++;
        }

        if ($plan['send'] === null) {
            return;
        }

        $type   = $plan['send']['type'];
        $offset = $plan['send']['offset'];

        if ($schedule['max_total'] > 0 && ($sent_counts[$invoice_id] ?? 0) >= $schedule['max_total']) {
            $result['skipped']++;

            return;
        }

        $template = $templates[$type] ?? null;
        if ($template === null) {
            // Already logged once per run by resolve_templates(); staying silent here
            // keeps a misconfigured install from writing one line per invoice nightly.
            $result['skipped']++;

            return;
        }

        // The unique index on (invoice_id, reminder_type, reminder_offset) is the lock:
        // a concurrent run that got here first leaves nothing to claim.
        if ( ! $this->CI->mdl_invoice_reminders->claim($invoice_id, $type, $offset)) {
            $result['skipped']++;

            return;
        }

        // The slot is claimed at this point, so every exit from here must leave it in a
        // final state. An exception escaping to run() would otherwise strand the row on
        // 'pending', which reads as "still going out" in the history but never retries.
        try {
            $sent = $this->send($invoice_id, $candidate, $type, $offset, $template);
        } catch (Throwable $e) {
            $this->fail($invoice_id, $type, $offset, $e->getMessage());
            $result['failed']++;

            return;
        }

        if ($sent) {
            $this->CI->mdl_invoice_reminders->mark_result(
                $invoice_id,
                $type,
                $offset,
                'sent',
                (string) $candidate->client_email
            );
            $result['sent']++;

            return;
        }

        $result['failed']++;
    }

    /**
     * Build and send one reminder. Returns false if it could not be delivered; the
     * slot has already been marked failed by then.
     */
    private function send(int $invoice_id, object $candidate, string $type, int $offset, object $template): bool
    {
        $invoice = $this->CI->mdl_invoices->get_by_id($invoice_id);

        if ($invoice === null) {
            $this->fail($invoice_id, $type, $offset, 'invoice no longer readable');

            return false;
        }

        $to  = (string) $candidate->client_email;
        $cc  = $template->email_template_cc ?: null;
        $bcc = $template->email_template_bcc ?: null;

        $from_email = (string) $template->email_template_from_email;
        if (trim($from_email) === '') {
            $from_email = (string) get_setting('smtp_mail_from');
        }

        if (trim($from_email) === '') {
            $from_email = (string) $invoice->user_email;
        }

        $from = [$from_email, (string) $template->email_template_from_name];

        // email_invoice() runs parse_template() over from/cc/bcc and then hands the
        // parsed values to check_mail_errors(), which calls redirect() — i.e. exit() —
        // on an invalid address. In a cron request that would abandon every remaining
        // invoice with nothing in the log to explain it, so the same parsed values are
        // validated here first and a bad one fails just this invoice.
        $addresses = [
            'to'   => $to,
            'from' => parse_template($invoice, $from[0]),
        ];

        if ($cc) {
            $addresses['cc'] = parse_template($invoice, $cc);
        }

        if ($bcc) {
            $addresses['bcc'] = parse_template($invoice, $bcc);
        }

        foreach ($addresses as $field => $address) {
            if ($address === '' || ! validate_email_address($address)) {
                $this->fail($invoice_id, $type, $offset, 'invalid ' . $field . ' address');

                return false;
            }
        }

        // Re-sanitise the stored body: templates saved before HTML Purifier was
        // introduced are still in the database on upgraded installs.
        $body = sanitize_email_template_html((string) $template->email_template_body);
        if (is_plain_text($body)) {
            $body = nl2br($body);
        }

        $subject = trim((string) $template->email_template_subject) !== ''
            ? $template->email_template_subject
            : trans($type === REMINDER_TYPE_OVERDUE ? 'invoice_overdue' : 'invoice_reminder')
              . ' - ' . trans('invoice') . ' #' . $invoice->invoice_number;

        $sent = email_invoice(
            (string) $invoice_id,
            $template->email_template_pdf_template,
            $from,
            $to,
            $subject,
            $body,
            $cc,
            $bcc,
            $this->CI->mdl_uploads->get_invoice_uploads($invoice_id),
            (bool) get_setting('email_pdf_attachment')
        );

        if ( ! $sent) {
            $this->fail($invoice_id, $type, $offset, 'mailer reported failure');

            return false;
        }

        return true;
    }

    private function fail(int $invoice_id, string $type, int $offset, string $reason): void
    {
        $this->CI->mdl_invoice_reminders->mark_result($invoice_id, $type, $offset, 'failed');

        log_message(
            'error',
            '[Cron Invoice Reminders] Invoice ' . $invoice_id . ' ' . $type . '/' . $offset
            . ' not sent: ' . sanitize_for_logging($reason)
        );
    }

    /**
     * Parsed schedule settings, or null when nothing could be sent anyway.
     *
     * @return array{before: int[], after: int[], repeat: int, max_total: int}|null
     */
    private function load_schedule(): ?array
    {
        $before = parse_reminder_offsets((string) get_setting('invoice_reminder_days_before'));
        $after  = parse_reminder_offsets((string) get_setting('invoice_reminder_days_after'));
        $repeat = (int) get_setting('invoice_reminder_repeat_days', 0);

        if ($before === [] && $after === [] && $repeat < 1) {
            log_message('error', '[Cron Invoice Reminders] Enabled but no reminder days are configured');

            return null;
        }

        return [
            'before'    => $before,
            'after'     => $after,
            'repeat'    => max(0, $repeat),
            'max_total' => max(0, (int) get_setting('invoice_reminder_max_total', 0)),
        ];
    }

    /**
     * The configured template row per reminder type. A type with no usable template is
     * absent from the result and logged once for the whole run.
     *
     * @return array<string, object>
     */
    private function resolve_templates(): array
    {
        // Overdue reminders reuse email_invoice_template_overdue, the setting that
        // already means "the email template for an overdue invoice" and that
        // select_email_invoice_template() uses when an admin emails one by hand. A
        // second setting for the same idea would only let the two drift apart.
        // Before-due has no existing equivalent -- email_invoice_template is the
        // original invoice email, not a nudge -- so it gets one new key in the same
        // email_invoice_template_* family.
        $configured = [
            REMINDER_TYPE_BEFORE_DUE => (int) get_setting('email_invoice_template_reminder'),
            REMINDER_TYPE_OVERDUE    => (int) get_setting('email_invoice_template_overdue'),
        ];

        $templates = [];
        foreach ($configured as $type => $template_id) {
            if ($template_id < 1) {
                log_message('error', '[Cron Invoice Reminders] No email template selected for ' . $type . ' reminders');
                continue;
            }

            $row = $this->CI->mdl_email_templates->where('email_template_id', $template_id)->get()->row();

            if ($row === null) {
                log_message(
                    'error',
                    '[Cron Invoice Reminders] Email template ' . $template_id . ' for ' . $type . ' reminders no longer exists'
                );
                continue;
            }

            $templates[$type] = $row;
        }

        return $templates;
    }
}
