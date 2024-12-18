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
            $session = $this->stripe->checkout->sessions->retrieve($checkout_session_id);

            //get invoice id
            $invoice_id = $session->client_reference_id;

            $this->load->model('invoices/mdl_invoices');

            //retrieve the invoice
            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)
                ->get()->row();

            $this->session->set_flashdata('alert_success', sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number));
            $this->session->keep_flashdata('alert_success');

            //record the payment
            $this->load->model('payments/mdl_payments');

            $this->mdl_payments->save(null, [
                'invoice_id' => $invoice_id,
                'payment_date' => date('Y-m-d'),
                'payment_amount' => $session->amount_total / 100,
                'payment_method_id' => get_setting('gateway_stripe_payment_method'),
                'payment_note' => 'payment intent ID: ' . $session->payment_intent,
            ]);

            //record the online payment
            $this->db->insert('ip_merchant_responses', [
                'invoice_id' => $invoice_id,
                'merchant_response_successful' => true,
                'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => 'stripe',
                'merchant_response' => '',
                'merchant_response_reference' => 'payment intent ID: ' . $session->payment_intent,
            ]);

            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        } catch (Error|Exception|ErrorException $e) {
            //TODO: log error

            $this->session->set_flashdata('alert_error',
                trans('online_payment_payment_failed') . '<br/>' . $$e->getMessage());
            $this->session->keep_flashdata('alert_error');

            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }
    }
}
