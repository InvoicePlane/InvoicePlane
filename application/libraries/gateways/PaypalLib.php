<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PaypalLib {

    protected Client $client;
    protected string $endpoint = 'https://api-m.paypal.com';
    protected string $client_id;
    protected string $client_secret;
    protected string $bearer_token;

    public function __construct($params) {
        $params['demo'] && $this->endpoint = 'https://api-m.sandbox.paypal.com';
        $this->client_id = $params['client_id'];
        $this->client_secret = $params['client_secret'];

        log_message('debug', "Paypal library initialization started");

        $this->client = new Client([
            'base_uri' => $this->endpoint
        ]);

        log_message('debug', "Paypal library client created");

        $this->authorize();

    }

    /**
     * Generate the authentication token
     *
     * @return void
     */
    protected function authorize() {
        log_message('debug', "Paypal library authorization started");
        try {
        $response = $this->client->request('post','v1/oauth2/token',[
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'auth' => [$this->client_id, $this->client_secret],
            'form_params' => ['grant_type' => 'client_credentials'],
            ]);

        $this->bearer_token = json_decode($response->getBody())->access_token;
        log_message('debug', "Paypal library authorization obtained");
        }
        catch(ClientException $e) {
            log_message('error', "Paypal library authorization failed");
            return $e->getResponse()->getBody();
        }
    }

    /**
     * Create an order on paypal
     * 
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     *
     * @param array $order_information
     * @return String
     */
    public function createOrder($order_information) : String {
        log_message('debug', "Paypal library order creation started");
        try{
        $response = $this->client->request('POST','v2/checkout/orders',[
            'headers'   => [
                'Content-Type'  =>  'application/json',
                'Authorization' =>  'Bearer '.$this->bearer_token
            ],
            'body'  =>  json_encode([
                'purchase_units' => array([
                    'invoice_id' => $order_information['invoice_id'],
                    'amount' => [
                        'currency_code' => $order_information['currency_code'],
                        'value' => $order_information['value']
                    ],
                    'custom_id' => $order_information['custom_id']
                ]),
                'intent' => 'CAPTURE'
            ])
        ]);
        log_message('debug', "Paypal library order creation completed");
        return $response->getBody()->getContents();
        }
        catch(ClientException $e) {
            log_message('debug', "Paypal library order creation failed");
            return ['status' => false, 'error' => $e];
        }
    }

    /**
     * Capture the payment of the order
     * 
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     *
     * @param string $order_id
     * @return array
     */
    public function captureOrder($order_id) : array {
        log_message('debug', "Paypal library order capturing started");
        try {
        $response = $this->client->request('POST','v2/checkout/orders/'.$order_id.'/capture',[
            'headers'   => [
                'Content-Type'  =>  'application/json',
                'Authorization' =>  'Bearer '.$this->bearer_token
            ]
            ]);
            log_message('debug', "Paypal library order capturing completed");
            return ['status' => true, 'response' => $response];
        }
        catch(ClientException $e) {
            log_message('debug', "Paypal library order capturing failed");
            return ['status' => false, 'error' => $e];
        }
    }

    /**
     * Get the details of a paypal order by order id
     * 
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_get
     *
     * @param string $order_id
     * @return void
     */
    public function showOrderDetails($order_id) {
        log_message('debug', "Paypal library show order started");
        try {
            $response = $this->client->request('GET','v2/checkout/orders/'.$order_id,[
                'headers'   => [
                    'Content-Type'  =>  'application/json',
                    'Authorization' =>  'Bearer '.$this->bearer_token
                ]
                ]);
                log_message('debug', "Paypal library show order completed");
                return ['status' => true, 'response' => $response];
            }
            catch(ClientException $e) {
                log_message('debug', "Paypal library show order failed");
                return ['status' => false, 'error' => $e];
            }
    }
}