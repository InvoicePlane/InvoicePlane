<?php

/**
 * Contains the language translations for the PayTheFly Pro payment gateway.
 *
 * These entries should be merged into the gateway_lang.php file
 * or loaded separately via $this->lang->load('paythefly').
 */
$lang = [
    // Gateway name & labels
    'paythefly_pay_with_crypto'     => 'Pay with Crypto (PayTheFly)',
    'paythefly_select_chain'        => 'Select Blockchain Network',
    'paythefly_chain'               => 'Blockchain',
    'paythefly_payment_deadline'    => 'Payment Deadline',
    'paythefly_proceed_to_payment'  => 'Proceed to Crypto Payment',
    'paythefly_generating_payment'  => 'Generating payment details...',
    'paythefly_secure_notice'       => 'You will be redirected to PayTheFly Pro for a secure blockchain payment. No credit card information is required.',
    'paythefly_payment_info'        => 'After completing the payment on the blockchain, your invoice will be automatically marked as paid once the transaction is confirmed. This usually takes a few minutes depending on network congestion.',

    // Status messages
    'paythefly_payment_processing'  => 'Your crypto payment for Invoice #%s is being processed. It will be confirmed once the blockchain transaction is verified.',
    'paythefly_payment_cancelled'   => 'The crypto payment was cancelled. You can try again at any time.',
    'paythefly_payment_pending'     => 'Your payment status is pending. If you have completed the payment, please allow a few minutes for blockchain confirmation.',
    'paythefly_payment_error'       => 'An error occurred while setting up the crypto payment. Please try again or contact us.',
    'paythefly_payment_successful'  => 'Crypto payment for Invoice #%s has been confirmed! Thank you.',

    // Errors
    'paythefly_invalid_chain'       => 'The selected blockchain network is not supported.',
    'paythefly_config_error'        => 'PayTheFly is not properly configured. Please contact the administrator.',
    'paythefly_retry'               => 'Try Again',

    // Admin settings
    'paythefly_settings_info'       => 'Configure your PayTheFly Pro account to accept cryptocurrency payments. You can get your credentials from the PayTheFly Pro Dashboard.',
    'paythefly_webhook_url'         => 'Webhook URL (set this in PayTheFly dashboard):',

    // Gateway config labels (for payment_gateways.php settings page)
    'online_payment_projectId'              => 'Project ID',
    'online_payment_contractAddress'        => 'Contract Address',
    'online_payment_privateKey'             => 'EIP-712 Signing Private Key',
    'online_payment_projectKey'             => 'Webhook Project Key (HMAC)',
    'online_payment_defaultChain'           => 'Default Chain (BSC or TRON)',
    'online_payment_deadlineMinutes'        => 'Payment Deadline (minutes)',
];
