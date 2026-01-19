<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Mollie\Gateway;
use Omnipay\Omnipay;

#[AllowDynamicProperties]
class MollieLib
{
    protected Gateway $gateway;

    protected string $api_key;

    public function __construct(array $params)
    {
        if (empty($params['api_key'])) {
            throw new InvalidArgumentException('Mollie API key is required');
        }
        $this->api_key = $params['api_key'];

        log_message('debug', 'Mollie library initialization started');

        $this->gateway = Omnipay::create('Mollie');
        $this->gateway->setApiKey($this->api_key);

        log_message('debug', 'Mollie library initialized');
    }

    /**
     * Create a payment on Mollie.
     *
     * @see https://docs.mollie.com/reference/v2/payments-api/create-payment
     *
     * @return array
     */
    public function createPayment(array $payment_information): array
    {
        log_message('debug', 'Mollie library payment creation started');
        try {
            $response = $this->gateway->purchase([
                'amount'      => $payment_information['amount'],
                'currency'    => $payment_information['currency'],
                'description' => $payment_information['description'],
                'returnUrl'   => $payment_information['return_url'],
                'metadata'    => [
                    'invoice_id'  => $payment_information['invoice_id'],
                    'invoice_key' => $payment_information['invoice_key'],
                ],
            ])->send();

            if ($response->isRedirect()) {
                log_message('debug', 'Mollie library payment creation completed');

                return [
                    'status'       => true,
                    'redirect_url' => $response->getRedirectUrl(),
                    'reference'    => $response->getTransactionReference(),
                ];
            } else {
                log_message('error', 'Mollie library payment creation failed: ' . $response->getMessage());

                return [
                    'status'  => false,
                    'message' => $response->getMessage(),
                ];
            }
        } catch (InvalidRequestException $e) {
            log_message('error', 'Mollie library payment creation - invalid request: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Invalid payment request: ' . $e->getMessage(),
            ];
        } catch (InvalidResponseException $e) {
            log_message('error', 'Mollie library payment creation - invalid response: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Invalid response from Mollie: ' . $e->getMessage(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Mollie library payment creation exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the details of a Mollie payment by transaction reference.
     *
     * @see https://docs.mollie.com/reference/v2/payments-api/get-payment
     */
    public function getPayment(string $transaction_reference): array
    {
        log_message('debug', 'Mollie library get payment started');
        try {
            $response = $this->gateway->fetchTransaction([
                'transactionReference' => $transaction_reference,
            ])->send();

            log_message('debug', 'Mollie library get payment completed');

            return [
                'status'   => true,
                'response' => $response,
            ];
        } catch (InvalidRequestException $e) {
            log_message('error', 'Mollie library get payment - invalid request: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Invalid payment fetch request: ' . $e->getMessage(),
            ];
        } catch (InvalidResponseException $e) {
            log_message('error', 'Mollie library get payment - invalid response: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Invalid response from Mollie: ' . $e->getMessage(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Mollie library get payment failed: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
