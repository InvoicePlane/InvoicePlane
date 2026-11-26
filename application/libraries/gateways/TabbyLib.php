<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2026 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Tabby Pay in 4, direct API integration (UAE).
 *
 * @see https://docs.tabby.ai/api-reference/overview
 */
#[AllowDynamicProperties]
class TabbyLib
{
    protected const BASE_URI = 'https://api.tabby.ai';

    protected Client $client;

    public function __construct(array $params)
    {
        $this->client = new Client([
            'base_uri'        => self::BASE_URI,
            'timeout'         => 15,
            'connect_timeout' => 5,
            'headers'         => [
                'Authorization' => 'Bearer ' . $params['secret_key'],
                'Accept'        => 'application/json',
            ],
        ]);
    }

    public static function is_payment_id(string $payment_id): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $payment_id) === 1;
    }

    /**
     * @see https://docs.tabby.ai/api-reference/checkout/create-a-session
     */
    public function create_checkout(array $payload): array
    {
        return $this->request('POST', 'api/v2/checkout', ['json' => $payload]);
    }

    /**
     * @see https://docs.tabby.ai/api-reference/payments/retrieve-a-payment
     */
    public function get_payment(string $payment_id): array
    {
        return $this->request('GET', $this->payment_uri($payment_id));
    }

    /**
     * The reference_id makes the capture idempotent, so a retry never captures twice.
     *
     * @see https://docs.tabby.ai/api-reference/payments/capture-a-payment
     */
    public function capture_payment(string $payment_id, string $amount, string $reference_id): array
    {
        return $this->request('POST', $this->payment_uri($payment_id) . '/captures', [
            'json' => [
                'amount'       => $amount,
                'reference_id' => $reference_id,
            ],
        ]);
    }

    /**
     * Closing an uncaptured payment cancels it and refunds the customer.
     *
     * @see https://docs.tabby.ai/api-reference/payments/close-a-payment
     */
    public function close_payment(string $payment_id): array
    {
        return $this->request('POST', $this->payment_uri($payment_id) . '/close');
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (BadResponseException $e) {
            $body  = json_decode((string) $e->getResponse()->getBody(), true);
            $error = is_array($body) ? implode(' ', array_filter([$body['errorType'] ?? null, $body['error'] ?? null], 'is_string')) : '';

            throw new RuntimeException(sprintf('Tabby %s %s failed with HTTP %d %s', $method, $uri, $e->getResponse()->getStatusCode(), $error), 0, $e);
        } catch (GuzzleException $e) {
            throw new RuntimeException(sprintf('Tabby %s %s failed: %s', $method, $uri, $e->getMessage()), 0, $e);
        }

        $data = json_decode((string) $response->getBody(), true);

        if ( ! is_array($data)) {
            throw new RuntimeException(sprintf('Tabby %s %s returned an unreadable response', $method, $uri));
        }

        return $data;
    }

    /**
     * The id is part of the request path, so anything but a UUID is refused before it reaches Tabby.
     */
    private function payment_uri(string $payment_id): string
    {
        if ( ! self::is_payment_id($payment_id)) {
            throw new InvalidArgumentException('Invalid Tabby payment id');
        }

        return 'api/v2/payments/' . $payment_id;
    }
}
