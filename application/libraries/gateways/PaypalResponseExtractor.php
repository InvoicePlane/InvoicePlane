<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PaypalResponseExtractor
{
    /**
     * Extract and validate capture data from PayPal response.
     *
     * @throws Exception on invalid response structure
     *
     * @return object|null Capture data object or null if not found
     */
    public static function extractCaptureData(object $paypal_object): ?object
    {
        try {
            return $paypal_object->purchase_units[0]->payments->captures[0] ?? null;
        } catch (Throwable $e) {
            throw new Exception('Invalid PayPal response structure: ' . $e->getMessage());
        }
    }

    /**
     * Extract capture status from PayPal response.
     *
     * @return string|null Uppercase status string or null if not found
     */
    public static function extractCaptureStatus(object $paypal_object): ?string
    {
        try {
            $status = $paypal_object->purchase_units[0]->payments->captures[0]->status ?? null;

            return $status ? mb_strtoupper($status) : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Extract invoice ID from PayPal response with fallback.
     *
     * @return string|null Invoice ID or null if not found
     */
    public static function extractInvoiceId(object $paypal_object, ?object $capture_data = null): ?string
    {
        if ($capture_data) {
            return $capture_data->invoice_id ?? null;
        }

        try {
            return $paypal_object->purchase_units[0]->payments->captures[0]->invoice_id ?? null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Extract payment amount and currency from capture data.
     *
     * @return array{amount: string|null, currency: string|null}
     */
    public static function extractAmountAndCurrency(object $capture_data): array
    {
        $amount   = $capture_data->amount->value ?? null;
        $currency = mb_strtoupper($capture_data->amount->currency_code ?? '');

        return [
            'amount'   => $amount,
            'currency' => $currency,
        ];
    }

    /**
     * Extract processor response code from capture data.
     *
     * @return string Processor response code or 'Unknown error'
     */
    public static function extractProcessorResponseCode(object $capture_data): string
    {
        return $capture_data->processor_response->response_code ?? 'Unknown error';
    }
}
