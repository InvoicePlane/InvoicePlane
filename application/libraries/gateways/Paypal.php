<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Paypal {

    protected Client $client;
    protected string $endpoint = 'https://api-m.paypal.com';
    protected string $client_id;
    protected string $client_secret;
    protected string $bearer_token;

    public function __construct($params) {
        $params['demo'] && $this->endpoint = 'https://api-m.sandbox.paypal.com';
        $this->client_id = $params['client_id'];
        $this->client_secret = $params['client_secret'];

        $this->client = new Client([
            'base_uri' => $this->endpoint
        ]);

        $this->authorize();
    }

    protected function authorize() {
        try {
        $response = $this->client->request('post','v1/oauth2/token',[
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'auth' => [$this->client_id, $this->client_secret],
            'form_params' => ['grant_type' => 'client_credentials'],
            ]);

        $this->bearer_token = json_decode($response->getBody())->access_token;
        }
        catch(ClientException $e) {
            return $e->getResponse()->getBody();
        }
    }

    public function createOrder($order_information) : String {
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

        return $response->getBody()->getContents(); //TODO: handle errors
    }

    public function captureOrder($order_id) : array {
        try {
        $response = $this->client->request('POST','v2/checkout/orders/'.$order_id.'/capture',[
            'headers'   => [
                'Content-Type'  =>  'application/json',
                'Authorization' =>  'Bearer '.$this->bearer_token
            ]
            ]);
            return ['status' => true, 'response' => $response];
        }
        catch(ClientException $e) {
            return ['status' => false, 'error' => $e];
        }
    }
}