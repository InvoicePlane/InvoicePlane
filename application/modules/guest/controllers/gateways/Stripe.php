<?php
use Stripe\StripeClient;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

    public function create_checkout_session($invoice_url_key) {

        $this->load->model('invoices/mdl_invoices');

        //retrive the invoice
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)
        ->get()->row();

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            //TODO: redirect to invoice page
        }

        $checkout_session = $this->stripe->checkout->sessions->create([
            'ui_mode'       => 'embedded',
            'return_url'   => site_url('guest/gateways/stripe/callback/{CHECKOUT_SESSION_ID}'),
            'client_reference_id'   => $invoice->invoice_id,
            'line_items'    => array(
                [
                    'price_data' => [
                        'currency' => get_setting('gateway_stripe_currency'),
                        'product_data' => [
                            'name' => 'invoice nr. '.$invoice->invoice_number
                        ],
                        'unit_amount' => $invoice->invoice_balance*100
                    ],
                    'quantity' => 1
                ],
            ),
            'mode'  => 'payment',
        ]);

        //TODO: handle exceptions in checkout session

        echo json_encode(array('clientSecret' => $checkout_session->client_secret)); //TODO create a well formatted answer
    }
}