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
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();

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
                        'unit_amount'  => $invoice->invoice_balance * 100,
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
            log_message('debug', __CLASS__ . '::' . __FUNCTION__ . ' reached, status: ' . $session->status . ' payment_status: ' . $session->payment_status . ', checkout_session_id: ' . $checkout_session_id);

            // Determine which invoice we’re dealing with
            $invoice_key = $session->client_reference_id;

            // Retrieve the invoice
            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_key)->get()->row();

            // Check the session payment_status is 'paid'
            // See: https://github.com/stripe/stripe-php/blob/044f9dd190967b8fb7e55fd0ea25f11c625c00a4/lib/Checkout/Session.php#L101
            $paid = $session->payment_status === $session::PAYMENT_STATUS_PAID; // +2 status: *_NO_PAYMENT_REQUIRED *_UNPAID

            // Is paid? (intent flow 'succeeded')
            if ($paid) {
                // Save the payment (visible in guest user)
                $this->load->model('payments/mdl_payments');
                $this->mdl_payments->save(null, [
                    'invoice_id'        => $invoice->invoice_id,
                    'payment_date'      => date('Y-m-d'),
                    'payment_amount'    => $session->amount_total / 100,
                    'payment_method_id' => get_setting('gateway_stripe_payment_method'),
                    'payment_note'      => trans('online_payment_intent_id') . ': ' . $session->payment_intent,
                ]);
            }

            // paid / cancel (+other flow)
            // Admin (& error log) message
            $response = $paid ? '. livemode: ' . trans($session->livemode ? 'yes' : 'no')
                                . ', currency: ' . $session->currency
                                . ', amount: ' . ($session->amount_received / 100)              // 0 in test. Set in live mode?
                                . ', fee: ' . ($session->application_fee_amount / 100)       // 0 in test. Set in live mode?
                                . ', session ID: ' . $session->id                                   // Unique identifier for the object.
                              :
                                ($session->cancel ? $session->cancellation_reason : $session->last_payment_error); // Cancelled
            // User (& error) message
            $user_msg = $paid ? sprintf(trans('online_payment_successful'), '#' . $invoice->invoice_number)
                              : trans('online_payment_failed') . '<br>' . sprintf(trans('online_payment_incomplete'), __CLASS__, $session->payment_status);
        } catch (Error|Exception|ErrorException $e) {
            $user_msg = trans('online_payment_error') . (empty($user_msg) ? '' : '<br>' . $user_msg);
            $paid     = 'error'; // tweak to reuse
            // Log the error so you can debug
            $response = __CLASS__ . '::' . __FUNCTION__ . ' exception: ' . $e->getMessage() . (empty($response) ? '' : ' - response: ' . $response);
            log_message('error', strtr($response . ' user_msg: ' . $user_msg, ['<br>' => ' '])); // No br's
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
