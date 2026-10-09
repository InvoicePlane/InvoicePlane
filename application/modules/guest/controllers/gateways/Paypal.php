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

use GuzzleHttp\Exception\ClientException;

#[AllowDynamicProperties]
class Paypal extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');
        $this->_create_client();
    }

    /**
     * Create the order on PayPal that is then processed when
     * the user inserts the payment method.
     *
     * @param string $invoice_url_key
     *
     * @return json the PayPal object to be loaded in the JS SDK script
     */
    public function paypal_create_order($invoice_url_key)
    {
        // Require POST request to prevent CSRF attacks
        if ($this->input->method() !== 'post') {
            show_404();
        }

        // Check if the invoice exists and is billable
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

        // Security: Verify the invoice exists and is guest-visible
        if ( ! $invoice) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Attempted order creation for non-public or non-existent invoice with key: ' . sanitize_for_logging($invoice_url_key));
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

        //create the order
        $paypal_client = $this->lib_paypal->createOrder([
            'invoice_id'    => $invoice->invoice_id,
            'currency_code' => get_setting('gateway_paypal_currency'),
            'value'         => $invoice->invoice_balance,
            'custom_id'     => $invoice_url_key,
        ]);

        // Decode the PayPal response
        $paypal_response = json_decode($paypal_client, true);

        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'PayPal createOrder JSON decode error for invoice ' . sanitize_for_logging($invoice_url_key) . ': ' . sanitize_for_logging(json_last_error_msg()));
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid response from payment gateway']));

            return;
        }

        // Validate required fields from PayPal response
        if (empty($paypal_response['id'])) {
            log_message('error', 'PayPal createOrder missing order ID for invoice ' . sanitize_for_logging($invoice_url_key));
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid response from payment gateway']));

            return;
        }

        // Add refreshed CSRF token to response (token regenerates on each POST)
        $response = [
            'id'         => $paypal_response['id'],
            'status'     => $paypal_response['status'] ?? null,
            'csrf_token' => $this->security->get_csrf_hash(),
        ];

        // Preserve any additional fields from PayPal response
        foreach ($paypal_response as $key => $value) {
            if ( ! isset($response[$key])) {
                $response[$key] = $value;
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Capture the payment which is put on hold on PayPal
     * after the user has set the card details.
     *
     *
     * @return void
     */
    public function paypal_capture_payment(string $order_id)
    {
        // Require POST request to prevent CSRF attacks
        if ($this->input->method() !== 'post') {
            show_404();
        }

        // Verify the order against the current invoice BEFORE capturing: once captureOrder() runs the
        // funds have moved at PayPal and cannot be undone here, so every check that can still fail
        // locally has to happen first.
        $validated_invoice_id = null;
        $preflight_error      = $this->_capture_preflight_error($order_id, $validated_invoice_id);
        if ($preflight_error !== null) {
            $this->session->set_flashdata('alert_error', $preflight_error);
            $this->session->keep_flashdata('alert_error');

            return;
        }

        $paypal_response = $this->lib_paypal->captureOrder($order_id);

        //handle the payment
        if ($paypal_response['status']) {
            $paypal_object = json_decode($paypal_response['response']->getBody());

            // Set the status of the actual transaction (not just the API call result.)
            $capture_status = mb_strtoupper($paypal_object->purchase_units[0]->payments->captures[0]->status) ?? null;

            // Only COMPLETED captures should be recorded as settled payments.
            // PENDING captures represent transactions that have not yet reached a final state
            // and should not reduce invoice balance or mark invoices as paid.
            if ($capture_status === 'COMPLETED') {
                // Extract payment data with defensive null safety checks at each level
                $purchase_units = $paypal_object->purchase_units ?? null;
                $payments       = $purchase_units[0]->payments ?? null;
                $captures       = $payments->captures ?? null;
                $capture_data   = $captures[0] ?? null;

                if ( ! $capture_data) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invalid PayPal response structure: missing capture data');
                    throw new Exception('Invalid PayPal response structure');
                }

                $invoice_id = $capture_data->invoice_id ?? null;
                $amount     = $capture_data->amount->value ?? null;
                $capture_id = $capture_data->id ?? null;

                // Validate required fields
                if (empty($invoice_id) || empty($amount) || empty($capture_id)) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Missing required PayPal data fields');
                    throw new Exception('Missing required PayPal data');
                }

                $capture_id = (string) $capture_id; // Ensure string type
                $settled    = false; // set once the capture is recorded locally (or was already)

                // Validate and sanitize the capture_id
                if (mb_strlen($capture_id) > 255) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal capture ID too long: ' . mb_strlen($capture_id) . ' characters');
                    throw new Exception('Invalid capture ID length');
                }

                // Defense-in-depth: the capture response's invoice_id must still be the
                // one the preflight check verified moments ago. PayPal controls both values
                // normally, but nothing downstream should ever trust the capture response's
                // invoice_id over the preflight-verified one without this check.
                if ((string) $invoice_id !== $validated_invoice_id) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Capture invoice_id (' . sanitize_for_logging($invoice_id) . ') does not match the preflight-verified invoice_id (' . sanitize_for_logging($validated_invoice_id) . '); payment not recorded');
                    $this->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
                    $this->session->keep_flashdata('alert_error');
                } else {
                    //record the payment
                    $this->load->model('payments/mdl_payments');

                    // Check if this capture_id has already been processed (deduplication check)
                    $existing_payment = $this->db
                        ->where('payment_external_id', $capture_id)
                        ->get('ip_payments')
                        ->row();

                    if ($existing_payment) {
                        // Duplicate payment attempt detected
                        log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Duplicate payment attempt blocked. PayPal capture ID: ' . sanitize_for_logging($capture_id) . ' already exists as payment_id: ' . sanitize_for_logging($existing_payment->payment_id));
                        $settled = true;

                        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

                        // Security: Verify the invoice exists and is guest-visible
                        if ( ! $invoice) {
                            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invoice no longer guest-visible during duplicate payment check: ' . sanitize_for_logging($invoice_id));
                            $this->session->set_flashdata('alert_error', trans('invoice_not_found'));
                            $this->session->keep_flashdata('alert_error');
                        } else {
                            $this->session->set_flashdata('alert_info', trans('online_payment_already_processed'));
                            $this->session->keep_flashdata('alert_info');
                        }
                    } else {
                        // Check if invoice is already fully paid
                        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

                        // Security: Verify the invoice exists and is guest-visible
                        if ( ! $invoice) {
                            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invoice no longer guest-visible during payment capture: ' . sanitize_for_logging($invoice_id));
                            $this->session->set_flashdata('alert_error', trans('invoice_not_found'));
                            $this->session->keep_flashdata('alert_error');
                        } elseif ($invoice->invoice_balance <= 0) {
                            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Payment rejected. Invoice ' . sanitize_for_logging($invoice->invoice_number) . ' already fully paid. Balance: ' . sanitize_for_logging($invoice->invoice_balance));
                            $this->session->set_flashdata('alert_info', trans('invoice_already_paid'));
                            $this->session->keep_flashdata('alert_info');
                        } else {
                            // Validate currency and amount before recording payment
                            $expected_currency = mb_strtoupper((string) get_setting('gateway_paypal_currency'));
                            $capture_currency  = mb_strtoupper((string) ($capture_data->amount->currency_code ?? ''));

                            if ($capture_currency !== $expected_currency) {
                                log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: currency mismatch for invoice ' . sanitize_for_logging($invoice_id) . '. Expected: ' . $expected_currency . ', received: ' . $capture_currency);
                                $this->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
                                $this->session->keep_flashdata('alert_error');
                            } elseif ((float) $amount + 0.0001 < (float) $invoice->invoice_balance) {
                                log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: amount mismatch for invoice ' . sanitize_for_logging($invoice_id) . '. Expected: ' . sanitize_for_logging($invoice->invoice_balance) . ', received: ' . sanitize_for_logging($amount));
                                $this->session->set_flashdata('alert_error', trans('online_payment_payment_failed'));
                                $this->session->keep_flashdata('alert_error');
                            } else {
                                // Record the payment atomically: the balance guard
                                // and the insert are one conditional UPDATE, so a
                                // concurrent capture with a different capture_id
                                // cannot also pass a stale balance and double-credit.
                                $recorded = $this->mdl_payments->record_external_payment([
                                    'invoice_id'          => $invoice_id,
                                    'payment_date'        => date('Y-m-d'),
                                    'payment_amount'      => $amount,
                                    'payment_method_id'   => get_setting('gateway_paypal_payment_method'),
                                    'payment_note'        => '',
                                    'payment_external_id' => $capture_id,
                                ]);

                                if ($recorded) {
                                    $settled = true;
                                    $this->session->set_flashdata('alert_success', sprintf(trans('online_payment_payment_successful'), htmlsc($invoice->invoice_number)));
                                } else {
                                    $this->session->set_flashdata('alert_info', trans('online_payment_already_processed'));
                                }
                                $this->session->keep_flashdata('alert_success');
                                $this->session->keep_flashdata('alert_info');
                            }
                        }
                    }

                }

                if ( ! $settled) {
                    // Funds were captured at PayPal but no payment was recorded (the invoice changed
                    // after the pre-capture check). Flag it loudly for manual reconciliation instead
                    // of logging a success.
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal capture ' . sanitize_for_logging($capture_id) . ' succeeded but was not recorded against invoice ' . sanitize_for_logging($invoice_id) . '; manual reconciliation required');
                }

                // Record COMPLETED capture in merchant responses
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice_id,
                    'merchant_response_successful' => $settled,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'paypal',
                    'merchant_response'            => $settled ? $capture_status : $capture_status . ' (captured at PayPal, NOT recorded locally)',
                    'merchant_response_reference'  => 'Resource ID:' . $paypal_object->id,
                ]);
            } elseif ($capture_status === 'PENDING') {
                // PENDING captures are acknowledged but NOT recorded as settled payments.
                // The invoice balance is not updated; the transaction awaits PayPal settlement confirmation.
                // This prevents reconciliation issues where invoices are marked paid before funds are actually received.

                // $capture_data / $capture_id only exist inside the COMPLETED branch above, so read the capture here.
                $pending_capture = $paypal_object->purchase_units[0]->payments->captures[0] ?? null;
                $invoice_id      = $pending_capture->invoice_id ?? null;
                $capture_id      = $pending_capture->id ?? null;

                // Log the pending capture for audit purposes
                if ($invoice_id) {
                    log_message('info', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal capture pending settlement. Invoice: ' . sanitize_for_logging($invoice_id) . ', Capture ID: ' . sanitize_for_logging((string) $capture_id));

                    $this->db->insert('ip_merchant_responses', [
                        'invoice_id'                   => $invoice_id,
                        'merchant_response_successful' => true,
                        'merchant_response_date'       => date('Y-m-d'),
                        'merchant_response_driver'     => 'paypal',
                        'merchant_response'            => 'PENDING - awaiting settlement',
                        'merchant_response_reference'  => 'Resource ID:' . $paypal_object->id,
                    ]);
                }

                // Notify the user that payment is pending
                $this->session->set_flashdata('alert_info', trans('online_payment_pending'));
                $this->session->keep_flashdata('alert_info');
            } else {
                // Payment failed (DECLINED or any other non-success status)
                $invoice_id = $paypal_object->purchase_units[0]->payments->captures[0]->invoice_id ?? null;

                // If we can't get invoice_id from captures, try to get it from order details
                if ( ! $invoice_id) {
                    $invoice_id = $this->_paypal_order_invoice_id($order_id);
                }

                // Get processor response code if available.
                $processor_response_code = $paypal_object->purchase_units[0]->payments->captures[0]->processor_response->response_code ?? 'Unknown error';

                // Record the failed transaction in the logs along with processor response code.
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'paypal',
                    'merchant_response'            => $capture_status . ': ' . $processor_response_code,
                    'merchant_response_reference'  => 'Resource ID:' . $paypal_object->id,
                ]);

                //set error message to be flashed
                $this->session->set_flashdata(
                    'alert_error',
                    trans('online_payment_payment_failed')
                );
                $this->session->keep_flashdata('alert_error');
            }
        } else {
            // captureOrder() failed. Its 'error' is either a ClientException (PayPal
            // rejected the request) or an InvalidArgumentException (order_id failed
            // local format validation and never reached PayPal) — only the former
            // has a getResponse() to read a body from.
            $error = $paypal_response['error'];

            if ($error instanceof ClientException) {
                $response_error = json_decode($error->getResponse()->getBody());
                $error_summary  = 'name: ' . ($response_error->name ?? 'unknown_error')
                    . '; details: ' . ($response_error->details[0]->description ?? $error->getMessage());
            } else {
                $error_summary = 'name: invalid_order_id; details: ' . $error->getMessage();
            }

            //get the order details to have the invoice id from paypal, if possible
            $invoice_id = $this->_paypal_order_invoice_id($order_id);

            //record the failed transaction in the logs
            if ($invoice_id !== null) {
                $this->db->insert('ip_merchant_responses', [
                    'invoice_id'                   => $invoice_id,
                    'merchant_response_successful' => false,
                    'merchant_response_date'       => date('Y-m-d'),
                    'merchant_response_driver'     => 'paypal',
                    'merchant_response'            => $error_summary,
                    'merchant_response_reference'  => 'Resource ID:' . $order_id,
                ]);
            } else {
                log_message('error', __CLASS__ . '::' . __FUNCTION__
                    . ' - Could not resolve invoice_id for a failed PayPal capture; skipping merchant response log. '
                    . $error_summary);
            }

            //set error message to be flashed
            $this->session->set_flashdata(
                'alert_error',
                trans('online_payment_payment_failed')
            );
            $this->session->keep_flashdata('alert_error');
        }
    }

    protected function _create_client(): void
    {
        $this->load->library('crypt');

        //load the REST API consumer library
        $this->load->library('gateways/PaypalLib', [
            'client_id'     => get_setting('gateway_paypal_clientId'),
            'client_secret' => $this->crypt->decode(get_setting('gateway_paypal_clientSecret')),
            'demo'          => get_setting('gateway_paypal_testMode') == 1,
        ], 'lib_paypal');
    }

    /**
     * Checks a PayPal order against the current invoice before any funds are captured.
     *
     * Returns the user-facing error message when the order must not be captured (order unreadable,
     * invoice no longer public, already paid, or the order no longer matches the invoice's
     * currency / balance), or null when it is safe to capture. On success, the invoice id
     * the order was verified against is written to $validated_invoice_id so the caller can
     * confirm the capture response still names the same invoice.
     */
    private function _capture_preflight_error(string $order_id, ?string &$validated_invoice_id = null): ?string
    {
        $response = $this->lib_paypal->showOrderDetails($order_id);

        if ( ! $response['status']) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Could not read PayPal order before capture; capture skipped');

            return trans('online_payment_payment_failed');
        }

        $unit       = json_decode($response['response']->getBody())->purchase_units[0] ?? null;
        $invoice_id = $unit->invoice_id ?? null;
        $amount     = $unit->amount->value ?? null;
        $currency   = mb_strtoupper((string) ($unit->amount->currency_code ?? ''));

        if (empty($invoice_id) || $amount === null) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - PayPal order is missing invoice or amount; capture skipped');

            return trans('online_payment_payment_failed');
        }

        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

        if ( ! $invoice) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Capture skipped: invoice not public or not found: ' . sanitize_for_logging($invoice_id));

            return trans('invoice_not_found');
        }

        if ($invoice->invoice_balance <= 0) {
            log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Capture skipped: invoice ' . sanitize_for_logging($invoice->invoice_number) . ' is already fully paid');

            return trans('invoice_already_paid');
        }

        if ($currency !== mb_strtoupper((string) get_setting('gateway_paypal_currency'))
            || abs((float) $amount - (float) $invoice->invoice_balance) > 0.005
        ) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Capture skipped: order no longer matches invoice ' . sanitize_for_logging($invoice_id) . '. Order: ' . sanitize_for_logging($amount) . ' ' . sanitize_for_logging($currency) . ', invoice balance: ' . sanitize_for_logging($invoice->invoice_balance));

            return trans('online_payment_payment_failed');
        }

        $validated_invoice_id = (string) $invoice_id;

        return null;
    }

    /**
     * Best-effort lookup of the invoice_id from PayPal's order details, used as a
     * fallback when a capture response doesn't carry it. Returns null on any
     * failure (network error, invalid order_id, malformed response) instead of
     * throwing, since this only ever runs while already handling another failure.
     */
    private function _paypal_order_invoice_id(string $order_id): ?string
    {
        $response = $this->lib_paypal->showOrderDetails($order_id);

        if ( ! $response['status']) {
            return null;
        }

        $order_details = json_decode($response['response']->getBody());

        return $order_details->purchase_units[0]->payments->captures[0]->invoice_id ?? null;
    }
}
