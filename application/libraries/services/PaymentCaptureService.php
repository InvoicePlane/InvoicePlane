<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use GuzzleHttp\Exception\ClientException;

class PaymentCaptureService
{
    private $CI;
    private $paypal_lib;
    private $invoices_model;
    private $payments_model;
    private $db;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->paypal_lib = $this->CI->lib_paypal;
        $this->CI->load->model('invoices/mdl_invoices');
        $this->CI->load->model('payments/mdl_payments');
        $this->invoices_model = $this->CI->mdl_invoices;
        $this->payments_model = $this->CI->mdl_payments;
        $this->db = $this->CI->db;
    }

    /**
     * Process PayPal payment capture with comprehensive validation.
     *
     * @throws Exception on critical errors
     * @return void Sets session flashdata with outcome
     */
    public function capturePayment(string $order_id): void
    {
        $response = $this->paypal_lib->captureOrder($order_id);
        if ( ! $response['status']) {
            $this->handleCaptureOrderError($response['error'], $order_id);

            return;
        }

        $paypal_object = json_decode($response['response']->getBody());
        $capture_status = PaypalResponseExtractor::extractCaptureStatus($paypal_object);

        if ($capture_status !== 'COMPLETED' && $capture_status !== 'PENDING') {
            $this->handleCaptureFailure($paypal_object, $capture_status, $order_id);

            return;
        }

        $capture_data = PaypalResponseExtractor::extractCaptureData($paypal_object);
        if ( ! $capture_data) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invalid PayPal response structure: missing capture data');
            throw new Exception('Invalid PayPal response structure');
        }

        $invoice_id = $capture_data->invoice_id ?? null;
        $amount = $capture_data->amount->value ?? null;
        $capture_id = $capture_data->id ?? null;

        if (empty($invoice_id) || empty($amount) || empty($capture_id)) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Missing required PayPal data fields');
            throw new Exception('Missing required PayPal data');
        }

        if ( ! $this->validateInvoiceAccessible($invoice_id)) {
            return;
        }

        if ($this->isDuplicatePayment($capture_id)) {
            $this->handleDuplicatePayment($invoice_id);

            return;
        }

        $invoice = $this->getGuestVisibleInvoice($invoice_id);
        if ($this->shouldRejectPayment($invoice)) {
            return;
        }

        if ( ! $this->validatePaymentAmountAndCurrency($invoice, $capture_data, $amount)) {
            return;
        }

        $this->recordPayment($invoice_id, $amount, $capture_id, $capture_status);
        $this->recordSuccessfulMerchantResponse($invoice_id, $capture_status, $paypal_object->id);
        $this->CI->session->set_flashdata('alert_success', sprintf(trans('online_payment_payment_successful'), htmlsc($invoice->invoice_number)));
        $this->CI->session->keep_flashdata('alert_success');
    }

    /**
     * Validate that invoice is guest-visible and accessible.
     */
    private function validateInvoiceAccessible(string $invoice_id): bool
    {
        $invoice = $this->getGuestVisibleInvoice($invoice_id);
        if ($invoice) {
            return true;
        }

        log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Attempted payment capture for non-public invoice: ' . sanitize_for_logging($invoice_id));
        $this->CI->session->set_flashdata('alert_error', trans('invoice_not_found'));
        $this->CI->session->keep_flashdata('alert_error');

        return false;
    }

    /**
     * Get a guest-visible invoice by ID.
     */
    private function getGuestVisibleInvoice(string $invoice_id): ?object
    {
        return $this->invoices_model->guest_visible()
            ->where('ip_invoices.invoice_id', $invoice_id)
            ->get()
            ->row();
    }

    /**
     * Check if payment with this capture ID already exists (duplicate detection).
     */
    private function isDuplicatePayment(string $capture_id): bool
    {
        $capture_id = (string) $capture_id;
        if (mb_strlen($capture_id) > 255) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal capture ID too long: ' . mb_strlen($capture_id) . ' characters');
            throw new Exception('Invalid capture ID length');
        }

        $existing = $this->db
            ->where('payment_external_id', $capture_id)
            ->get('ip_payments')
            ->row();

        return (bool) $existing;
    }

    /**
     * Determine if payment should be rejected for this invoice.
     */
    private function shouldRejectPayment(?object $invoice): bool
    {
        if ( ! $invoice) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invoice no longer guest-visible during payment capture');
            $this->CI->session->set_flashdata('alert_error', trans('invoice_not_found'));
            $this->CI->session->keep_flashdata('alert_error');

            return true;
        }

        if ($invoice->invoice_balance <= 0) {
            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Payment rejected. Invoice ' . sanitize_for_logging($invoice->invoice_number) . ' already fully paid. Balance: ' . sanitize_for_logging($invoice->invoice_balance));
            $this->CI->session->set_flashdata('alert_info', trans('invoice_already_paid'));
            $this->CI->session->keep_flashdata('alert_info');

            return true;
        }

        return false;
    }

    /**
     * Validate payment amount and currency match expected values.
     */
    private function validatePaymentAmountAndCurrency(object $invoice, object $capture_data, string $amount): bool
    {
        $expected_currency = mb_strtoupper((string) get_setting('gateway_paypal_currency'));
        $capture_currency = mb_strtoupper((string) ($capture_data->amount->currency_code ?? ''));

        if ($capture_currency !== $expected_currency) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: currency mismatch for invoice ' . sanitize_for_logging($invoice->invoice_id) . '. Expected: ' . $expected_currency . ', received: ' . $capture_currency);
            $this->CI->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
            $this->CI->session->keep_flashdata('alert_error');

            return false;
        }

        if ((float) $amount + 0.0001 < (float) $invoice->invoice_balance) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: amount mismatch for invoice ' . sanitize_for_logging($invoice->invoice_id) . '. Expected: ' . sanitize_for_logging($invoice->invoice_balance) . ', received: ' . sanitize_for_logging($amount));
            $this->CI->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
            $this->CI->session->keep_flashdata('alert_error');

            return false;
        }

        return true;
    }

    /**
     * Record a successful payment in the database.
     */
    private function recordPayment(string $invoice_id, string $amount, string $capture_id, string $capture_status): void
    {
        $payment_note = ($capture_status === 'PENDING') ? trans('online_payment_pending') : '';

        $this->payments_model->save(null, [
            'invoice_id' => $invoice_id,
            'payment_date' => date('Y-m-d'),
            'payment_amount' => $amount,
            'payment_method_id' => get_setting('gateway_paypal_payment_method'),
            'payment_note' => $payment_note,
            'payment_external_id' => $capture_id,
        ]);
    }

    /**
     * Record a successful merchant response.
     */
    private function recordSuccessfulMerchantResponse(string $invoice_id, string $capture_status, string $resource_id): void
    {
        $this->db->insert('ip_merchant_responses', [
            'invoice_id' => $invoice_id,
            'merchant_response_successful' => true,
            'merchant_response_date' => date('Y-m-d'),
            'merchant_response_driver' => 'paypal',
            'merchant_response' => $capture_status,
            'merchant_response_reference' => 'Resource ID:' . $resource_id,
        ]);
    }

    /**
     * Handle failed payment capture.
     */
    private function handleCaptureFailure(object $paypal_object, ?string $capture_status, string $order_id): void
    {
        $invoice_id = PaypalResponseExtractor::extractInvoiceId($paypal_object);
        if ( ! $invoice_id) {
            $invoice_id = $this->getInvoiceIdFromOrderDetails($order_id);
        }

        $processor_response_code = PaypalResponseExtractor::extractProcessorResponseCode(
            PaypalResponseExtractor::extractCaptureData($paypal_object) ?? (object) []
        );

        if ($invoice_id) {
            $this->db->insert('ip_merchant_responses', [
                'invoice_id' => $invoice_id,
                'merchant_response_successful' => false,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => 'paypal',
                'merchant_response' => ($capture_status ?? 'UNKNOWN') . ': ' . $processor_response_code,
                'merchant_response_reference' => 'Resource ID:' . $paypal_object->id,
            ]);
        }

        $this->CI->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
        $this->CI->session->keep_flashdata('alert_error');
    }

    /**
     * Handle duplicate payment attempt.
     */
    private function handleDuplicatePayment(string $invoice_id): void
    {
        $invoice = $this->getGuestVisibleInvoice($invoice_id);

        if ( ! $invoice) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invoice no longer guest-visible during duplicate check: ' . sanitize_for_logging($invoice_id));
            $this->CI->session->set_flashdata('alert_error', trans('invoice_not_found'));
            $this->CI->session->keep_flashdata('alert_error');
        } else {
            $this->CI->session->set_flashdata('alert_info', trans('online_payment_already_processed'));
            $this->CI->session->keep_flashdata('alert_info');
        }
    }

    /**
     * Handle captureOrder() error (API call or validation failure).
     */
    private function handleCaptureOrderError($error, string $order_id): void
    {
        if ($error instanceof ClientException) {
            $response_error = json_decode($error->getResponse()->getBody());
            $error_summary = 'name: ' . ($response_error->name ?? 'unknown_error')
                . '; details: ' . ($response_error->details[0]->description ?? $error->getMessage());
        } else {
            $error_summary = 'name: invalid_order_id; details: ' . $error->getMessage();
        }

        $invoice_id = $this->getInvoiceIdFromOrderDetails($order_id);

        if ($invoice_id) {
            $this->db->insert('ip_merchant_responses', [
                'invoice_id' => $invoice_id,
                'merchant_response_successful' => false,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => 'paypal',
                'merchant_response' => $error_summary,
                'merchant_response_reference' => 'Resource ID:' . $order_id,
            ]);
        } else {
            log_message('error', __CLASS__ . '::' . __FUNCTION__
                . ' - Could not resolve invoice_id for a failed PayPal capture. '
                . $error_summary);
        }

        $this->CI->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
        $this->CI->session->keep_flashdata('alert_error');
    }

    /**
     * Best-effort lookup of invoice_id from PayPal order details.
     */
    private function getInvoiceIdFromOrderDetails(string $order_id): ?string
    {
        $response = $this->paypal_lib->showOrderDetails($order_id);

        if ( ! $response['status']) {
            return null;
        }

        $order_details = json_decode($response['response']->getBody());

        return $order_details->purchase_units[0]->payments->captures[0]->invoice_id ?? null;
    }
}
