<?php
/**
 * Contains the language translations for the payment gateways
 */
$lang = array(
    // General strings
    'online_payment'                     => 'Pagamenti online',
    'online_payments'                    => 'Pagamenti on-line',
    'online_payment_for'                 => 'Pagamento online per',
    'online_payment_for_invoice'         => 'Pagamento on-line della fattura',
    'online_payment_method'              => 'Metodo di pagamento online',
    'online_payment_creditcard_hint'     => 'Se si desidera pagare tramite carta di credito si prega di inserire le seguenti informazioni. <br/> Le informazioni della carta di credito non vengono memorizzate sui nostri server e saranno trasferite verso il fornitore del pagamento online utilizzando una connessione sicura.',
    'enable_online_payments'             => 'Attiva Pagamenti Online',
    'payment_provider'                   => 'Provider di pagamento',
    'add_payment_provider'               => 'Aggiungere un provider di pagamento',
    'transaction_reference'              => 'Riferimento',
    'payment_description'                => 'Pagamento della fattura %s',

    // Credit card strings
    'creditcard_cvv'                     => 'CVV / CSC',
    'creditcard_details'                 => 'Dettagli della carta di credito',
    'creditcard_expiry_month'            => 'Mese di scadenza',
    'creditcard_expiry_year'             => 'Anno di scadenza',
    'creditcard_number'                  => 'Numero carta di credito',
    'online_payment_card_invalid'        => 'Questa carta di credito non è valida. Si prega di controllare le informazioni fornite.',

    // Payment Gateway Fields
    'online_payment_apiLoginId'          => 'Id di accesso API', // Field for AuthorizeNet_AIM
    'online_payment_transactionKey'      => 'Chiave transazione', // Field for AuthorizeNet_AIM
    'online_payment_testMode'            => 'Modalità Test', // Field for AuthorizeNet_AIM
    'online_payment_developerMode'       => 'Modalità sviluppatore', // Field for AuthorizeNet_AIM
    'online_payment_websiteKey'          => 'Chiave del sito', // Field for Buckaroo_Ideal
    'online_payment_secretKey'           => 'Chiave segreta', // Field for Buckaroo_Ideal
    'online_payment_merchantId'          => 'Merchant Id', // Field for CardSave
    'online_payment_password'            => 'Password', // Field for CardSave
    'online_payment_apiKey'              => 'API Key', // Field for Coinbase
    'online_payment_secret'              => 'Secret', // Field for Coinbase
    'online_payment_accountId'           => 'Account ID', // Field for Coinbase
    'online_payment_storeId'             => 'Store ID', // Field for FirstData_Connect
    'online_payment_sharedSecret'        => 'Shared Secret', // Field for FirstData_Connect
    'online_payment_appId'               => 'App ID', // Field for GoCardless
    'online_payment_appSecret'           => 'App Secret', // Field for GoCardless
    'online_payment_accessToken'         => 'Access Token', // Field for GoCardless
    'online_payment_merchantAccessCode'  => 'Codice Accesso Merchant', // Field for Migs_ThreeParty
    'online_payment_secureHash'          => 'Secure Hash', // Field for Migs_ThreeParty
    'online_payment_siteId'              => 'ID Sito', // Field for MultiSafepay
    'online_payment_siteCode'            => 'Codice Sito', // Field for MultiSafepay
    'online_payment_accountNumber'       => 'Numero Account', // Field for NetBanx
    'online_payment_storePassword'       => 'Salva Password', // Field for NetBanx
    'online_payment_merchantKey'         => 'Merchant Key', // Field for PayFast
    'online_payment_pdtKey'              => 'Pdt Key', // Field for PayFast
    'online_payment_username'            => 'Nome utente', // Field for Payflow_Pro
    'online_payment_vendor'              => 'Fornitore', // Field for Payflow_Pro
    'online_payment_partner'             => 'Partner', // Field for Payflow_Pro
    'online_payment_pxPostUsername'      => 'Px Post Username', // Field for PaymentExpress_PxPay
    'online_payment_pxPostPassword'      => 'Px Post Password', // Field for PaymentExpress_PxPay
    'online_payment_signature'           => 'Firma', // Field for PayPal_Express
    'online_payment_referrerId'          => 'Referrer Id', // Field for SagePay_Direct
    'online_payment_transactionPassword' => 'Password per la transazione', // Field for SecurePay_DirectPost
    'online_payment_subAccountId'        => 'ID SubAccount', // Field for TargetPay_Directebanking
    'online_payment_secretWord'          => 'Parola segreta', // Field for TwoCheckout
    'online_payment_installationId'      => 'ID installazione', // Field for WorldPay
    'online_payment_callbackPassword'    => 'Callback Password', // Field for WorldPay

    // Status / Error Messages
    'online_payment_payment_cancelled'   => 'Pagamento annullato.',
    'online_payment_payment_failed'      => 'Pagamento non riuscito. Per favore riprova.',
    'online_payment_payment_successful'  => 'Pagamento della fattura %s effettuato con successo!',
    'online_payment_payment_redirect'    => 'Attendi mentre ti reindirizziamo alla pagina di pagamento...',
    'online_payment_3dauth_redirect'     => 'Attendi mentre ti reinditizziamo verso l\'emittente della tua carta per l\'autenticazione...'
);