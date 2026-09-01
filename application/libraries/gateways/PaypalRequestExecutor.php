<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PaypalRequestExecutor
{
    public function __construct(private Client $client) {}

    /**
     * Execute a PayPal API request with unified error handling and logging.
     *
     * @param callable $request Function that performs the request
     * @param string   $action  Description of the action for logging
     *
     * @return array ['status' => true/false, 'response' => Response|Exception]
     */
    public function execute(callable $request, string $action): array
    {
        log_message('debug', "Paypal library {$action} started");
        try {
            $response = $request();
            log_message('debug', "Paypal library {$action} completed");

            return ['status' => true, 'response' => $response];
        } catch (ClientException|InvalidArgumentException $exception) {
            log_message('debug', "Paypal library {$action} failed");

            return ['status' => false, 'error' => $exception];
        }
    }
}
