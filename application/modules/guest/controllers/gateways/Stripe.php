<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

use Stripe\StripeClient;

#[AllowDynamicProperties]
class Stripe extends Base_Controller
{
    protected StripeClient $stripe;

    protected $Mdl_settings;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('crypt');
        $this->load->model('invoices/mdl_invoices');
        $this->load->helper('file_security');
        $this->load->helper(['currency', 'stripe']);

        $this->stripe = new StripeClient($this->crypt->decode(get_setting('gateway_stripe_apiKey')));
    }

    /**
     * Creates a checkout session on Stripe
     * that is then retrieved to execute the payment.
     *
     * @param string $invoice_url_key the url key that is used to retrive the invoice
     *
     * @return json the client secret in a json format
     */
    public function create_checkout_session($invoice_url_key)
    {
        // Require POST request to prevent CSRF attacks
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

        // Security: Verify the invoice exists and is guest-visible
        if ( ! $invoice) {
            log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Attempted checkout session creation for non-public or non-existent invoice with key: ' . sanitize_for_logging($invoice_url_key));
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));

            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

        $checkout_session = $this->stripe->checkout->sessions->create([
            'mode'                => 'payment',
            'ui_mode'             => 'embedded',
            'return_url'          => site_url('guest/gateways/stripe/callback/{CHECKOUT_SESSION_ID}'),
            'client_reference_id' => $invoice->invoice_url_key, // More privacy of invoice_id
            'line_items'          => [
                [
                    'price_data' => [
                        'currency'     => get_setting('gateway_stripe_currency'),
                        'unit_amount'  => stripe_amount_to_minor_units($invoice->invoice_balance, get_setting('gateway_stripe_currency')),
                        'product_data' => [
                            'name' => trans('invoice') . ' #' . $invoice->invoice_number,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->output->set_output(json_encode(['clientSecret' => $checkout_session->client_secret]));
    }

    /**
     * The callback endpoint called by stripe once the
     * card transaction has been completed or aborted
     * Handle exceptions Improved by @Matthias-Ab.
     *
     *
     * @return void
     */
    public function callback(string $checkout_session_id)
    {
        try {
            // Retrieve the Checkout Session from Stripe
            $session = $this->stripe->checkout->sessions->retrieve($checkout_session_id);

            // Debug logging
            log_message('debug', __CLASS__ . '::' . __FUNCTION__ . ' reached, status: ' . $session->status . ' payment_status: ' . $session->payment_status . ', checkout_session_id: ' . sanitize_for_logging($checkout_session_id));

            // Determine which invoice we’re dealing with
            $invoice_key = $session->client_reference_id;

            // Retrieve the invoice
            $invoice = $this->mdl_invoices->guest_visible()->where('ip_invoices.invoice_url_key', $invoice_key)->get()->row();

            // Security: Verify the invoice exists and is guest-visible
            if ( ! $invoice) {
                log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Attempted payment callback for non-public or non-existent invoice with key: ' . sanitize_for_logging($invoice_key));
                throw new Exception('Invoice not found or not accessible');
            }

            // Check the session payment_status is 'paid'
            // See: https://github.com/stripe/stripe-php/blob/044f9dd190967b8fb7e55fd0ea25f11c625c00a4/lib/Checkout/Session.php#L101
            $paid = $session->payment_status === $session::PAYMENT_STATUS_PAID; // +2 status: *_NO_PAYMENT_REQUIRED *_UNPAID

            // Is paid? (intent flow 'succeeded')
            if ($paid) {
                $this->load->model('payments/mdl_payments');

                // Validate and sanitize the payment_intent ID
                $payment_intent = (string) $session->payment_intent;
                if (empty($payment_intent) || mb_strlen($payment_intent) > 255) {
                    log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Invalid payment_intent ID format');
                    throw new Exception('Invalid payment intent ID');
                }

                // Check if this payment_intent has already been processed (deduplication check)
                $existing_payment = $this->db
                    ->where('payment_external_id', $payment_intent)
                    ->get('ip_payments')
                    ->row();

                if ($existing_payment) {
                    // Duplicate payment attempt detected
                    log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Duplicate payment attempt blocked. Payment intent: ' . sanitize_for_logging($payment_intent) . ' already exists as payment_id: ' . sanitize_for_logging($existing_payment->payment_id));
                    $paid     = false; // Mark as not paid to show info message instead of success
                    $user_msg = trans('online_payment_already_processed');
                } elseif ($invoice->invoice_balance <= 0) {
                    // Invoice is already fully paid
                    log_message('warning', __CLASS__ . '::' . __FUNCTION__ . ' - Payment rejected. Invoice ' . sanitize_for_logging($invoice->invoice_number) . ' already fully paid. Balance: ' . sanitize_for_logging($invoice->invoice_balance));
                    $paid     = false; // Mark as not paid to show info message instead of success
                    $user_msg = trans('invoice_already_paid');
                } else {
                    // Validate currency and amount before recording payment
                    $expected_currency = mb_strtoupper((string) get_setting('gateway_stripe_currency'));
                    $capture_currency  = mb_strtoupper((string) ($session->currency ?? ''));
                    $capture_amount    = stripe_amount_from_minor_units($session->amount_total, $capture_currency);

                    if ($capture_currency !== $expected_currency) {
                        log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: currency mismatch for invoice ' . sanitize_for_logging($invoice_key) . '. Expected: ' . $expected_currency . ', received: ' . $capture_currency);
                        $paid     = false;
                        $user_msg = trans('online_payment_payment_failed');
                    } elseif ((float) $capture_amount + 0.0001 < (float) $invoice->invoice_balance) {
                        log_message('error', __CLASS__ . '::' . __FUNCTION__ . ' - Rejected capture: amount mismatch for invoice ' . sanitize_for_logging($invoice_key) . '. Expected: ' . sanitize_for_logging($invoice->invoice_balance) . ', received: ' . sanitize_for_logging($capture_amount));
                        $paid     = false;
                        $user_msg = trans('online_payment_payment_failed');
                    } else {
                        // Save the payment (visible in guest user)
                        $this->mdl_payments->save(null, [
                            'invoice_id'          => $invoice->invoice_id,
                            'payment_date'        => date('Y-m-d'),
                            'payment_amount'      => $capture_amount,
                            'payment_method_id'   => get_setting('gateway_stripe_payment_method'),
                            'payment_note'        => trans('online_payment_intent_id') . ': ' . $payment_intent,
                            'payment_external_id' => $payment_intent,
                        ]);
                    }
                }
            }

            // paid / cancel (+other flow)
            // Admin (& error log) message
            $response = $paid ? '. livemode: ' . trans($session->livemode ? 'yes' : 'no')
                                . ', currency: ' . $session->currency
                                . ', amount: ' . stripe_amount_from_minor_units($session->amount_received, $session->currency)              // 0 in test. Set in live mode?
                                . ', fee: ' . stripe_amount_from_minor_units($session->application_fee_amount, $session->currency)       // 0 in test. Set in live mode?
                                . ', session ID: ' . $session->id                                   // Unique identifier for the object.
                                : ($session->cancel ? $session->cancellation_reason : $session->last_payment_error); // Cancelled
            // User (& error) message
            $user_msg = $paid ? sprintf(trans('online_payment_successful'), '#' . htmlsc($invoice->invoice_number))
                              : trans('online_payment_failed') . '<br>' . sprintf(trans('online_payment_incomplete'), __CLASS__, $session->payment_status);
        } catch (Error|Exception|ErrorException $e) {
            $user_msg = trans('online_payment_error') . (empty($user_msg) ? '' : '<br>' . $user_msg);
            $paid     = 'error'; // tweak to reuse
            // Log the error so you can debug
            $response = __CLASS__ . '::' . __FUNCTION__ . ' exception: ' . $e->getMessage() . (empty($response) ? '' : ' - response: ' . $response);
            log_message('error', sanitize_for_logging(strtr($response . ' user_msg: ' . $user_msg, ['<br>' => ' ']))); // No br's
        } finally {
            $paid = is_bool($paid) ? ($paid ? 'success' : 'info') : $paid; // Tweak to reuse (flashdata alert_*)
            // Check stripe server ok
            $ok = $session->status !== null; // Stripe is accessible?
            // Record a succeeded/canceled and other merchant response (This helps you keep track of incomplete attempts)
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => (int) $ok, // response server API (no)ok
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => __CLASS__,
                'merchant_response'            => ($ok ? $session->mode . ': ' . $session->payment_status . ', ' : '') . $response,
                'merchant_response_reference'  => $ok ? 'intent_id: ' . $session->payment_intent : 'none',
            ]);

            // Notify user
            $this->session->set_flashdata('alert_' . $paid, $user_msg);
            // Attempt to redirect them to the invoice. invoice_url_key? No, return to invoices view
            redirect('guest/view/invoice' . (empty($invoice->invoice_url_key) ? 's' : '/' . $invoice->invoice_url_key));
        }
    }
}
