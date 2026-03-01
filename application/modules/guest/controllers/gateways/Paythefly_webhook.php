<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane - PayTheFly Pro Webhook Controller
 *
 * Receives and processes payment confirmation webhooks from PayTheFly Pro.
 * This controller does NOT require authentication (public endpoint).
 *
 * Webhook payload format:
 * {
 *   "data": "<json string>",
 *   "sign": "<hmac hex>",
 *   "timestamp": <unix int>
 * }
 *
 * Data fields:
 * - project_id, chain_symbol, tx_hash, wallet, value, fee
 * - serial_no, tx_type (1=pay, 2=withdraw), confirmed, create_at
 *
 * Response MUST contain "success" string.
 *
 * @author      PayTheFly Integration
 * @link        https://pro.paythefly.com
 */

#[AllowDynamicProperties]
class Paythefly_webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable CSRF for webhook endpoint
        // Note: CodeIgniter's CSRF protection must be configured to exclude
        // this endpoint, or it needs to be handled in config/config.php
        // via $config['csrf_exclude_uris'] = ['guest/gateways/paythefly_webhook/notify'];
    }

    /**
     * Main webhook notification endpoint.
     *
     * Receives POST requests from PayTheFly Pro when a payment
     * is confirmed on the blockchain.
     *
     * URL: /guest/gateways/paythefly_webhook/notify
     *
     * @return void
     */
    public function notify()
    {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->_respond(405, 'Method not allowed');
            return;
        }

        // Read raw POST body
        $rawBody = file_get_contents('php://input');

        if (empty($rawBody)) {
            log_message('error', 'PayTheFly webhook: Empty request body');
            $this->_respond(400, 'Empty body');
            return;
        }

        // Parse the webhook payload
        $payload = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'PayTheFly webhook: Invalid JSON: ' . json_last_error_msg());
            $this->_respond(400, 'Invalid JSON');
            return;
        }

        // Validate required payload fields
        if ( ! isset($payload['data'], $payload['sign'], $payload['timestamp'])) {
            log_message('error', 'PayTheFly webhook: Missing required fields (data, sign, timestamp)');
            $this->_respond(400, 'Missing required fields');
            return;
        }

        // Initialize PayTheFly library for verification
        $this->load->library('crypt');
        $this->load->library('gateways/PaytheflyLib', [
            'project_id'       => get_setting('gateway_paythefly_projectId'),
            'private_key'      => '', // Not needed for webhook verification
            'project_key'      => $this->crypt->decode(get_setting('gateway_paythefly_projectKey')),
            'contract_address' => get_setting('gateway_paythefly_contractAddress'),
        ], 'lib_paythefly');

        // Verify HMAC signature
        if ( ! $this->lib_paythefly->verifyWebhookSignature(
            $payload['data'],
            $payload['sign'],
            (int) $payload['timestamp']
        )) {
            log_message('error', 'PayTheFly webhook: Invalid signature');
            $this->_respond(403, 'Invalid signature');
            return;
        }

        // Parse the data field
        $data = $this->lib_paythefly->parseWebhookData($payload['data']);

        if ($data === null) {
            $this->_respond(400, 'Invalid data payload');
            return;
        }

        // Verify project ID matches
        if ($data['project_id'] !== get_setting('gateway_paythefly_projectId')) {
            log_message('error', 'PayTheFly webhook: Project ID mismatch. Expected: '
                . get_setting('gateway_paythefly_projectId') . ', Got: ' . $data['project_id']);
            $this->_respond(403, 'Project ID mismatch');
            return;
        }

        // Process based on transaction type
        $txType = (int) $data['tx_type'];

        if ($txType === 1) {
            // Payment transaction
            $this->_processPayment($data);
        } elseif ($txType === 2) {
            // Withdrawal transaction (log only, no invoice action)
            $this->_processWithdrawal($data);
        } else {
            log_message('warning', 'PayTheFly webhook: Unknown tx_type: ' . $txType);
        }

        // PayTheFly requires response to contain "success"
        $this->_respond(200, 'success');
    }

    /**
     * Process a payment webhook notification.
     *
     * Looks up the invoice by serial_no (invoice_number),
     * records the payment, and updates the invoice status.
     *
     * @param array $data Parsed webhook data
     *
     * @return void
     */
    protected function _processPayment(array $data)
    {
        $serialNo  = $data['serial_no'];
        $txHash    = $data['tx_hash'];
        $wallet    = $data['wallet'] ?? '';
        $value     = $data['value'] ?? '0';
        $fee       = $data['fee'] ?? '0';
        $chain     = $data['chain_symbol'] ?? 'BSC';
        $confirmed = (bool) ($data['confirmed'] ?? false);

        log_message('info', sprintf(
            'PayTheFly webhook: Payment received. serial_no=%s, tx_hash=%s, value=%s, chain=%s, confirmed=%s',
            $serialNo, $txHash, $value, $chain, $confirmed ? 'true' : 'false'
        ));

        // Only process confirmed transactions
        if ( ! $confirmed) {
            log_message('info', 'PayTheFly webhook: Payment not yet confirmed, skipping. tx_hash=' . $txHash);
            return;
        }

        // Look up the invoice by invoice_number (serial_no)
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices
            ->where('ip_invoices.invoice_number', $serialNo)
            ->get()
            ->row();

        if ( ! $invoice) {
            log_message('error', 'PayTheFly webhook: Invoice not found for serial_no: ' . $serialNo);
            return;
        }

        // Check for duplicate payment (same tx_hash)
        $existing = $this->db
            ->where('merchant_response_reference', 'tx_hash:' . $txHash)
            ->where('merchant_response_driver', 'PayTheFly')
            ->where('merchant_response_successful', 1)
            ->get('ip_merchant_responses')
            ->row();

        if ($existing) {
            log_message('info', 'PayTheFly webhook: Duplicate payment notification ignored. tx_hash=' . $txHash);
            return;
        }

        // Determine the payment amount in the invoice's currency
        // The webhook value is the human-readable crypto amount
        // We treat it as equivalent to the invoice balance for recording
        $paymentAmount = (float) $invoice->invoice_balance;

        // If the crypto value is available and we should use it for the amount
        if ( ! empty($value) && (float) $value > 0) {
            // The value from webhook is already human-readable
            // Use the invoice balance as the payment amount (crypto amount may differ)
            $paymentAmount = min((float) $value, (float) $invoice->invoice_balance);
        }

        // Record the payment in InvoicePlane
        $this->load->model('payments/mdl_payments');

        $paymentNote = sprintf(
            'PayTheFly Crypto Payment — Chain: %s, TX: %s, Wallet: %s, Value: %s, Fee: %s',
            $chain,
            $txHash,
            $wallet,
            $value,
            $fee
        );

        try {
            $paymentMethodId = get_setting('gateway_paythefly_payment_method') ?: 0;

            $this->mdl_payments->save(null, [
                'invoice_id'        => $invoice->invoice_id,
                'payment_date'      => date('Y-m-d'),
                'payment_amount'    => $paymentAmount,
                'payment_method_id' => $paymentMethodId,
                'payment_note'      => $paymentNote,
            ]);

            log_message('info', sprintf(
                'PayTheFly webhook: Payment recorded successfully. Invoice #%s, Amount: %s',
                $invoice->invoice_number,
                $paymentAmount
            ));

            // Record successful merchant response
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => 1,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'PayTheFly',
                'merchant_response'            => sprintf(
                    'Payment confirmed. Chain: %s, Value: %s, Fee: %s, Wallet: %s',
                    $chain, $value, $fee, $wallet
                ),
                'merchant_response_reference'  => 'tx_hash:' . $txHash,
            ]);

        } catch (Exception $e) {
            log_message('error', 'PayTheFly webhook: Failed to record payment: ' . $e->getMessage());

            // Record failed merchant response
            $this->db->insert('ip_merchant_responses', [
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => 0,
                'merchant_response_date'       => date('Y-m-d'),
                'merchant_response_driver'     => 'PayTheFly',
                'merchant_response'            => 'Payment recording failed: ' . $e->getMessage(),
                'merchant_response_reference'  => 'tx_hash:' . $txHash,
            ]);
        }
    }

    /**
     * Process a withdrawal webhook notification.
     *
     * Withdrawals are admin-initiated fund movements out of PayTheFly.
     * We only log them for audit purposes.
     *
     * @param array $data Parsed webhook data
     *
     * @return void
     */
    protected function _processWithdrawal(array $data)
    {
        log_message('info', sprintf(
            'PayTheFly webhook: Withdrawal notification received. serial_no=%s, tx_hash=%s, value=%s, chain=%s',
            $data['serial_no'] ?? '',
            $data['tx_hash'] ?? '',
            $data['value'] ?? '0',
            $data['chain_symbol'] ?? ''
        ));

        // Optionally record in merchant responses for audit trail
        $this->db->insert('ip_merchant_responses', [
            'invoice_id'                   => 0,
            'merchant_response_successful' => 1,
            'merchant_response_date'       => date('Y-m-d'),
            'merchant_response_driver'     => 'PayTheFly',
            'merchant_response'            => sprintf(
                'Withdrawal confirmed. Chain: %s, Value: %s, TX: %s',
                $data['chain_symbol'] ?? '',
                $data['value'] ?? '0',
                $data['tx_hash'] ?? ''
            ),
            'merchant_response_reference'  => 'withdrawal:' . ($data['tx_hash'] ?? ''),
        ]);
    }

    /**
     * Send an HTTP response.
     *
     * @param int    $statusCode HTTP status code
     * @param string $message    Response body (PayTheFly requires "success" for 200)
     *
     * @return void
     */
    protected function _respond(int $statusCode, string $message)
    {
        $this->output
            ->set_status_header($statusCode)
            ->set_content_type('text/plain')
            ->set_output($message);
    }
}
