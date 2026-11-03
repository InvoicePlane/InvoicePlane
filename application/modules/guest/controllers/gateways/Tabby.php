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

require_once APPPATH . 'libraries/PaymentCallbackLock.php';
require_once APPPATH . 'libraries/gateways/TabbyLib.php';

/**
 * Tabby Pay in 4. checkout() sends the customer to Tabby's hosted payment page, callback() is
 * where Tabby sends them back, and webhook() catches the payments of customers who never come
 * back. Both of the last two settle the payment: verify it with Tabby, capture it, record it.
 *
 * @see https://docs.tabby.ai/pay-in-4-custom-integration/quick-start
 */
#[AllowDynamicProperties]
class Tabby extends Base_Controller
{
    private const DRIVER = 'Tabby';

    private ?TabbyLib $tabby = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('crypt');
        $this->load->model(['invoices/mdl_invoices', 'payments/mdl_payments']);
        $this->load->helper(['currency', 'file_security', 'tabby']);
    }

    /**
     * Creates a checkout session for the invoice balance and redirects the customer to Tabby.
     */
    public function checkout(?string $invoice_url_key = null): void
    {
        if ($this->input->method() !== 'post' || $invoice_url_key === null || ! verify_csrf_token()) {
            show_404();
        }

        $invoice = $this->guest_invoice('invoice_url_key', $invoice_url_key);

        if ($invoice === null) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Tabby checkout requested for a non-public or non-existent invoice with key: ' . sanitize_for_logging($invoice_url_key));
            show_404();
        }

        $invoice_url = 'guest/view/invoice/' . $invoice->invoice_url_key;

        if ( ! tabby_available($invoice)) {
            $this->session->set_flashdata('alert_error', trans((float) $invoice->invoice_balance > 0 ? 'tabby_unavailable' : 'invoice_already_paid'));
            redirect($invoice_url);
        }

        $return_url = 'guest/gateways/tabby/callback/' . $invoice->invoice_url_key . '/';
        $payload    = tabby_checkout_payload($invoice, (string) get_setting('gateway_tabby_merchantCode'), [
            'success' => site_url($return_url . 'success'),
            'cancel'  => site_url($return_url . 'cancel'),
            'failure' => site_url($return_url . 'failure'),
        ], $this->paid_invoice_count((int) $invoice->client_id));

        try {
            $session = $this->tabby()->create_checkout($payload);
        } catch (Throwable $e) {
            log_message('error', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Invoice ' . $invoice->invoice_number . ': ' . $e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('tabby_unavailable'));
            redirect($invoice_url);
        }

        $payment_id = $session['payment']['id'] ?? '';
        $payment_id = is_string($payment_id) ? $payment_id : '';
        $web_url    = $session['configuration']['available_products']['installments'][0]['web_url'] ?? '';
        $web_url    = is_string($web_url) ? $web_url : '';

        if (($session['status'] ?? null) !== 'created' || ! TabbyLib::is_payment_id($payment_id) || ! tabby_valid_checkout_url($web_url)) {
            $reason = $session['configuration']['products']['installments']['rejection_reason'] ?? null;
            $reason = is_string($reason) && preg_match('/^[a-z_]{1,64}$/', $reason) === 1 ? $reason : null;

            $this->log_response((int) $invoice->invoice_id, false, 'Checkout rejected: ' . ($reason ?? 'no payment link'), $payment_id);
            $this->session->set_flashdata('alert_error', tabby_rejection_message($reason));
            redirect($invoice_url);
        }

        // Settling finds the invoice of a payment through this row, which also proves the payment was created here.
        if ( ! $this->log_response((int) $invoice->invoice_id, true, 'Checkout created', $payment_id)) {
            log_message('error', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Could not save Tabby payment ' . $payment_id . ' for invoice ' . $invoice->invoice_number . '; checkout stopped'));
            $this->session->set_flashdata('alert_error', trans('tabby_unavailable'));
            redirect($invoice_url);
        }

        redirect($web_url);
    }

    /**
     * Tabby sends the customer back to one of the merchant_urls with ?payment_id=. The outcome in
     * the URL only picks the message for a payment that is not settled; the status comes from Tabby.
     */
    public function callback(?string $invoice_url_key = null, ?string $outcome = null): void
    {
        if ($invoice_url_key === null || ! in_array($outcome, ['success', 'cancel', 'failure'], true)) {
            show_404();
        }

        $invoice = $this->guest_invoice('invoice_url_key', $invoice_url_key);

        if ($invoice === null) {
            show_404();
        }

        $payment_id = $this->input->get('payment_id');
        $payment_id = is_string($payment_id) ? $payment_id : '';

        if (TabbyLib::is_payment_id($payment_id) && $this->invoice_id_for_payment($payment_id) === (int) $invoice->invoice_id) {
            $result = $this->settle($payment_id, (int) $invoice->invoice_id);
        } else {
            log_message('error', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Return for invoice ' . $invoice->invoice_number . ' with an unknown Tabby payment id: ' . $payment_id));
            $result = 'pending';
        }

        [$type, $message] = $this->result_alert($result, $outcome, $invoice);
        $this->session->set_flashdata('alert_' . $type, $message);

        redirect('guest/view/invoice/' . $invoice->invoice_url_key);
    }

    /**
     * Tabby posts here on every status change of a payment. Only the payment id is read from the
     * body: status, amount and currency are fetched from Tabby. Any answer but 200 makes Tabby retry.
     *
     * @see https://docs.tabby.ai/pay-in-4-custom-integration/webhooks
     */
    public function webhook(): void
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $secret = trim((string) get_setting('gateway_tabby_webhookSecret'));

        if ($secret !== '' && ! hash_equals($secret, (string) $this->input->get_request_header('X-Auth-Key'))) {
            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Refused a Tabby webhook without the configured X-Auth-Key header');
            $this->output->set_status_header(401);

            return;
        }

        $body       = json_decode($this->input->raw_input_stream, true);
        $payment_id = is_array($body) && is_string($body['id'] ?? null) ? $body['id'] : '';

        if ( ! TabbyLib::is_payment_id($payment_id)) {
            $this->output->set_status_header(400);

            return;
        }

        $invoice_id = $this->invoice_id_for_payment($payment_id);

        // Not created by this installation: the merchant code may be shared with another shop.
        if ($invoice_id === null) {
            return;
        }

        if (in_array($this->settle($payment_id, $invoice_id), ['busy', 'error'], true)) {
            $this->output->set_status_header(503);
        }
    }

    /**
     * Brings the invoice in line with the Tabby payment: captures and records an authorized one,
     * records one captured in the Tabby dashboard, cancels one the invoice can no longer take.
     * The redirect and the webhook can both get here for one payment: the capture is idempotent
     * through its reference_id, and record() runs under the invoice's payment lock.
     */
    private function settle(string $payment_id, int $invoice_id): string
    {
        try {
            if ($this->payment_recorded($payment_id)) {
                return 'paid';
            }

            $payment  = $this->tabby()->get_payment($payment_id);
            $status   = mb_strtoupper((string) ($payment['status'] ?? ''));
            $amount   = amount_to_minor_units((string) ($payment['amount'] ?? '0'), 100);
            $captured = $this->sum_amounts($payment['captures'] ?? []);

            if ($status === 'AUTHORIZED' && $captured === 0) {
                $refusal = $this->refusal($payment, $invoice_id, $amount);

                if ($refusal !== null) {
                    $this->tabby()->close_payment($payment_id);
                    log_message('warning', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Cancelled Tabby payment ' . $payment_id . ' for invoice ' . $invoice_id . ': ' . $refusal));
                    $this->log_response($invoice_id, false, 'Cancelled at Tabby: ' . $refusal, $payment_id);

                    return 'voided';
                }

                $payment  = $this->tabby()->capture_payment($payment_id, tabby_amount(amount_from_minor_units($amount, 100)), 'capture-' . $payment_id);
                $status   = mb_strtoupper((string) ($payment['status'] ?? ''));
                $captured = $this->sum_amounts($payment['captures'] ?? []);
            }

            // A partial capture made in the Tabby dashboard is recorded once the payment is closed.
            if ($status === 'CLOSED' || ($status === 'AUTHORIZED' && $captured > 0 && $captured >= $amount)) {
                $settled = $captured - $this->sum_amounts($payment['refunds'] ?? []);

                return $settled > 0 ? $this->record($payment, $payment_id, $invoice_id, $settled) : 'voided';
            }

            return match ($status) {
                'CREATED'  => 'pending',
                'REJECTED' => 'rejected',
                'EXPIRED'  => 'expired',
                default    => 'unconfirmed',
            };
        } catch (Throwable $e) {
            log_message('error', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Tabby payment ' . $payment_id . ': ' . $e->getMessage()));

            return 'error';
        }
    }

    private function record(array $payment, string $payment_id, int $invoice_id, int $amount): string
    {
        $lock = new PaymentCallbackLock($this->db);

        try {
            if ( ! $lock->acquire($invoice_id)) {
                return 'busy';
            }

            if ($this->payment_recorded($payment_id)) {
                return 'paid';
            }

            $refusal = $this->refusal($payment, $invoice_id, $amount);

            if ($refusal === null) {
                $recorded = $this->mdl_payments->record_external_payment([
                    'invoice_id'          => $invoice_id,
                    'payment_date'        => date_from_mysql(date('Y-m-d'), true),
                    'payment_amount'      => amount_from_minor_units($amount, 100),
                    'payment_method_id'   => get_setting('gateway_tabby_payment_method'),
                    'payment_note'        => trans('tabby_payment_id') . ': ' . $payment_id,
                    'payment_external_id' => $payment_id,
                ]);

                if ($recorded) {
                    $this->log_response($invoice_id, true, 'Captured ' . tabby_amount(amount_from_minor_units($amount, 100)) . ' AED' . (empty($payment['is_test']) ? '' : ' (test)'), $payment_id);

                    return 'paid';
                }

                $refusal = 'the balance no longer covers it';
            }

            log_message('error', sanitize_for_logging(__CLASS__ . '::' . __FUNCTION__ . ' - Tabby payment ' . $payment_id . ' is captured but not recorded against invoice ' . $invoice_id . ' (' . $refusal . '); manual reconciliation required'));
            $this->log_response($invoice_id, false, 'Captured at Tabby, NOT recorded locally: ' . $refusal, $payment_id);

            return 'unrecorded';
        } finally {
            $lock->release();
        }
    }

    /**
     * Why the invoice cannot take this amount (in fils), or null when it can.
     */
    private function refusal(array $payment, int $invoice_id, int $amount): ?string
    {
        $invoice  = $this->guest_invoice('invoice_id', $invoice_id);
        $currency = mb_strtoupper((string) ($payment['currency'] ?? ''));

        if ($invoice === null) {
            return 'the invoice is not public';
        }

        if ($currency !== 'AED') {
            return 'the currency is not AED';
        }

        if ($amount <= 0 || $amount > amount_to_minor_units($invoice->invoice_balance, 100)) {
            return 'amount ' . tabby_amount(amount_from_minor_units($amount, 100)) . ' AED, balance ' . tabby_amount($invoice->invoice_balance);
        }

        return null;
    }

    /**
     * The total of a payment's captures or refunds, in fils.
     */
    private function sum_amounts(mixed $entries): int
    {
        $total = 0;

        foreach (is_array($entries) ? $entries : [] as $entry) {
            $total += is_array($entry) ? amount_to_minor_units((string) ($entry['amount'] ?? '0'), 100) : 0;
        }

        return $total;
    }

    /**
     * Built on first use: decrypting the key throws when it cannot be read, which must not turn
     * a request that is refused before any Tabby call into a 500.
     */
    private function tabby(): TabbyLib
    {
        return $this->tabby ??= new TabbyLib(['secret_key' => $this->crypt->decode(get_setting('gateway_tabby_secretKey'))]);
    }

    /**
     * @return array{0: string, 1: string} the alert type and the message shown on the invoice
     */
    private function result_alert(string $result, string $outcome, object $invoice): array
    {
        if ($result === 'pending') {
            $result = match ($outcome) {
                'cancel'  => 'expired',
                'failure' => 'rejected',
                default   => 'unconfirmed',
            };
        }

        return match ($result) {
            'paid'       => ['success', sprintf(trans('online_payment_successful'), '#' . htmlsc($invoice->invoice_number))],
            'expired'    => ['info', trans('tabby_payment_aborted')],
            'rejected'   => ['error', trans('tabby_rejected')],
            'voided'     => ['error', trans('tabby_payment_voided')],
            'unrecorded' => ['error', trans('tabby_payment_unrecorded')],
            default      => ['warning', trans('tabby_payment_unconfirmed')],
        };
    }

    private function guest_invoice(string $column, int|string $value): ?object
    {
        return $this->mdl_invoices->guest_visible()->where('ip_invoices.' . $column, $value)->get()->row();
    }

    private function invoice_id_for_payment(string $payment_id): ?int
    {
        $row = $this->db->select('invoice_id')
            ->where('merchant_response_driver', self::DRIVER)
            ->where('merchant_response_reference', $payment_id)
            ->order_by('merchant_response_id', 'ASC')
            ->limit(1)
            ->get('ip_merchant_responses')
            ->row();

        return $row ? (int) $row->invoice_id : null;
    }

    private function payment_recorded(string $payment_id): bool
    {
        return $this->db->where('payment_external_id', $payment_id)->count_all_results('ip_payments') > 0;
    }

    private function paid_invoice_count(int $client_id): int
    {
        return $this->db->where('client_id', $client_id)->where('invoice_status_id', 4)->count_all_results('ip_invoices');
    }

    private function log_response(int $invoice_id, bool $successful, string $response, string $payment_id): bool
    {
        return (bool) $this->db->insert('ip_merchant_responses', [
            'invoice_id'                   => $invoice_id,
            'merchant_response_successful' => (int) $successful,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => self::DRIVER,
            'merchant_response'            => mb_substr($response, 0, 255),
            'merchant_response_reference'  => TabbyLib::is_payment_id($payment_id) ? $payment_id : 'none',
        ]);
    }
}
