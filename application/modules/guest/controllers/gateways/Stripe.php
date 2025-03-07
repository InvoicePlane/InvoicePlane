<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

        $this->stripe = new StripeClient($this->crypt->decode(get_setting('gateway_stripe_apiKey')));
    }

    /**
     * Creates a checkout session on Stripe
     * that is then retrieved to execute the payment
     *
     * @param  string  $invoice_url_key  the url key that is used to retrive the invoice
     * @return json the client secret in a json format
     */
    public function create_checkout_session($invoice_url_key)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()->row();

        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));

            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

        $checkout_session = $this->stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'return_url' => site_url('guest/gateways/stripe/callback/{CHECKOUT_SESSION_ID}'),
            'client_reference_id' => $invoice->invoice_id,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => get_setting('gateway_stripe_currency'),
                        'product_data' => [
                            'name' => 'invoice nr. ' . $invoice->invoice_number
                        ],
                        'unit_amount' => $invoice->invoice_balance * 100
                    ],
                    'quantity' => 1
                ],
            ],
            'mode' => 'payment',
        ]);

        //TODO: handle exceptions in checkout session

        $this->output->set_output(json_encode(['clientSecret' => $checkout_session->client_secret]));
    }

    /**
     * The callback endpoint called by stripe once the
     * card transaction has been completed or aborted
     *
     * @param  string  $checkout_session_id
     * @return void
     */
public function callback($checkout_session_id)
{
    try {
        // 1) Retrieve the Checkout Session from Stripe
        $session = $this->stripe->checkout->sessions->retrieve($checkout_session_id);

        // 2) Determine which invoice we’re dealing with
        $invoice_id = $session->client_reference_id;
        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)
            ->get()->row();

        // 3) DEBUG LOGGING (optional but recommended)
        //    Make sure your PHP error log is set up (log_errors=On in php.ini)
        error_log("Stripe callback reached. Checkout session id: " . $checkout_session_id);
        error_log("Stripe session payment_status: " . $session->payment_status);

        // 4) Check the session status before marking paid
        if ($session->payment_status === 'paid') {

            // ----------------------
            // SUCCESS FLOW
            // ----------------------
            // a) Show success message
            $this->session->set_flashdata(
                'alert_success',
                sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number)
            );
            $this->session->keep_flashdata('alert_success');

            // b) Record the payment in the invoiceplane DB
            $this->load->model('payments/mdl_payments');
            $this->mdl_payments->save(null, [
                'invoice_id'        => $invoice_id,
                'payment_date'      => date('Y-m-d'),
                'payment_amount'    => $session->amount_total / 100,
                'payment_method_id' => get_setting('gateway_stripe_payment_method'),
                'payment_note'      => 'payment intent ID: ' . $session->payment_intent,
            ]);

            // c) Record the “merchant response”
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice_id,
                'merchant_response_successful' => true,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'stripe',
                'merchant_response'            => '',
                'merchant_response_reference'  => 'payment intent ID: ' . $session->payment_intent,
            ]);

            // d) Redirect back to invoice view
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));

        } else {

            // ----------------------
            // FAILURE / CANCELLED FLOW
            // ----------------------
            // a) Show an error or “payment not completed” notice to the user
            //    (Adjust text as you wish)
            $this->session->set_flashdata('alert_error',
                sprintf(trans('online_payment_payment_failed')) .
                '<br/>' .
                'Payment was not completed. Stripe session status: ' . $session->payment_status
            );
            $this->session->keep_flashdata('alert_error');

            // b) Optionally record a “failed” or “canceled” merchant response
            //    (This helps you keep track of incomplete attempts)
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice_id,
                'merchant_response_successful' => false,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'stripe',
                'merchant_response'            => 'Transaction not completed or canceled',
                'merchant_response_reference'  => 'payment intent ID: ' . $session->payment_intent,
            ]);

            // c) Redirect back to invoice view
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }

    } catch (Error|Exception|ErrorException $e) {
        // LOG THE ERROR so you can debug
        error_log("Stripe callback exception: " . $e->getMessage());

        // Show the user an error message
        $this->session->set_flashdata(
            'alert_error',
            trans('online_payment_payment_failed') . '<br/>' . $e->getMessage()
        );
        $this->session->keep_flashdata('alert_error');

        // attempt to redirect them to the invoice
        // (optional defensive: in case $invoice is undefined or $invoice->invoice_url_key is empty)
        redirect(site_url('guest/view/invoices'));
    }
}
}
