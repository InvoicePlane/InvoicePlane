<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

#[AllowDynamicProperties]
class PaypalLib
{
    protected Client $client;

    protected string $endpoint = 'https://api-m.paypal.com';

    protected string $client_id;

    protected string $client_secret;

    protected string $bearer_token;

    protected string $partner_attribution_id = 'ANGELLFREEInc_SP'; // Partner attribution.

    public function __construct(array $params)
    {
        $params['demo'] && $this->endpoint = 'https://api-m.sandbox.paypal.com';
        $this->client_id                   = $params['client_id'];
        $this->client_secret               = $params['client_secret'];

        log_message('debug', 'Paypal library initialization started');

        $this->client = new Client([
            'base_uri' => $this->endpoint,
        ]);

        log_message('debug', 'Paypal library client created');

        $this->authorize();
    }

    /**
     * Create an order on paypal.
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     *
     * @return string
     */
    public function createOrder(array $order_information): string|array
    {
        log_message('debug', 'Paypal library order creation started');
        try {
            $response = $this->client->request('POST', 'v2/checkout/orders', [
                'headers' => $this->buildHeaders([
                    'request_id'   => $this->generateRequestId('create'),
                    'content_type' => 'application/json',
                ]),
                'body' => json_encode([
                    'purchase_units' => [[
                        'invoice_id' => $order_information['invoice_id'],
                        'amount'     => [
                            'currency_code' => $order_information['currency_code'],
                            'value'         => $order_information['value'],
                        ],
                        'custom_id' => $order_information['custom_id'],
                    ]],
                    'intent' => 'CAPTURE',
                ]),
            ]);
            log_message('debug', 'Paypal library order creation completed');

            return $response->getBody()->getContents();
        } catch (ClientException $clientException) {
            log_message('debug', 'Paypal library order creation failed');

            return ['status' => false, 'error' => $clientException];
        }
    }

    /**
     * Capture the payment of the order.
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public function captureOrder(string $order_id): array
    {
        log_message('debug', 'Paypal library order capturing started');
        try {
            $response = $this->client->request('POST', 'v2/checkout/orders/' . $order_id . '/capture', [
                'headers' => $this->buildHeaders([
                    'request_id'   => $this->generateRequestId('capture'),
                    'content_type' => 'application/json',
                ]),
            ]);
            log_message('debug', 'Paypal library order capturing completed');

            return ['status' => true, 'response' => $response];
        } catch (ClientException $clientException) {
            log_message('debug', 'Paypal library order capturing failed');

            return ['status' => false, 'error' => $clientException];
        }
    }

    /**
     * Get the details of a paypal order by order id.
     *
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_get
     */
    public function showOrderDetails(string $order_id): array
    {
        log_message('debug', 'Paypal library show order started');
        try {
            $response = $this->client->request('GET', 'v2/checkout/orders/' . $order_id, [
                'headers' => $this->buildHeaders([
                    'content_type' => 'application/json',
                    'prefer'       => 'return=representation', // returns more detailed response object.
                ]),
            ]);
            log_message('debug', 'Paypal library show order completed');

            return ['status' => true, 'response' => $response];
        } catch (ClientException $clientException) {
            log_message('debug', 'Paypal library show order failed');

            return ['status' => false, 'error' => $clientException];
        }
    }

    /** Centralized headers for PayPal REST calls. */
    protected function buildHeaders(array $options = []): array
    {
        // $options: ['request_id' => string, 'content_type' => string, 'prefer' => string]
        $headers = [
            'Content-Type'                  => $options['content_type'] ?? 'application/json',
            'Authorization'                 => 'Bearer ' . $this->bearer_token,
            'PayPal-Partner-Attribution-Id' => $this->partner_attribution_id,
        ];

        if ( ! empty($options['request_id'])) {
            // Unique idempotency key per API attempt (reuse only for retries of the same call)
            $headers['PayPal-Request-Id'] = $options['request_id'];
        }

        if ( ! empty($options['prefer'])) {
            // e.g., 'return=representation' - provides more detailed response object.
            $headers['Prefer'] = $options['prefer'];
        }

        return $headers;
    }

    /** UUID v4 id generator for PayPal-Request-Id. */
    protected function generateRequestId(string $context = ''): string
    {
        $data = random_bytes(16);
        // version 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // variant RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', mb_str_split(bin2hex($data), 4));

        return 'ip' . ($context ? "-{$context}" : '') . '-' . $uuid;
    }

    /**
     * Generate the authentication token.
     *
     * @return void
     */
    protected function authorize()
    {
        log_message('debug', 'Paypal library authorization started');
        try {
            $response = $this->client->request('post', 'v1/oauth2/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'auth'        => [$this->client_id, $this->client_secret],
                'form_params' => ['grant_type' => 'client_credentials'],
            ]);

            $this->bearer_token = json_decode($response->getBody())->access_token;
            log_message('debug', 'Paypal library authorization obtained');
        } catch (ClientException $clientException) {
            log_message('error', 'Paypal library authorization failed');

            return $clientException->getResponse()->getBody();
        }
    }
}
