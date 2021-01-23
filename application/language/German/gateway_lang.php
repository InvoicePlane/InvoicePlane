<?php
/**
 * Contains the language translations for the payment gateways
 */
$lang = array(
    // General strings
    'online_payment'                     => 'Online-Zahlung',
    'online_payments'                    => 'Online-Zahlungen',
    'online_payment_for'                 => 'Online-Bezahlung für',
    'online_payment_for_invoice'         => 'Online-Zahlung für Rechnung',
    'online_payment_method'              => 'Online-Zahlungsmethode',
    'online_payment_creditcard_hint'     => 'Falls Sie mit der Kreditkarte bezahlen möchten, geben Sie bitte die untenstehende Daten ein.<br/>Die Kreditkarten-Informationen werden nicht auf unseren Servern gespeichert und werden über eine sichere Verbindung zum Online-Bezahlungsdienst übertragen.',
    'enable_online_payments'             => 'Online-Zahlungen aktivieren',
    'payment_provider'                   => 'Zahlungsanbieter',
    'add_payment_provider'               => 'Zahlungsanbieter hinzufügen',
    'transaction_reference'              => 'Transaktions-Referenz',
    'payment_description'                => 'Zahlung für Rechnung %s',

    // Credit card strings
    'creditcard_cvv'                     => 'Sicherheitsnummer (CVC / CSC)',
    'creditcard_details'                 => 'Kreditkarteninformationen',
    'creditcard_expiry_month'            => 'Ablaufmonat',
    'creditcard_expiry_year'             => 'Ablaufjahr',
    'creditcard_number'                  => 'Kreditkartennummer',
    'online_payment_card_invalid'        => 'Diese Kreditkarte ist ungültig. Bitte überprüfen Sie die angegebenen Informationen.',

    // Payment Gateway Fields
    'online_payment_apiLoginId'          => 'API Login ID', // Field for AuthorizeNet_AIM
    'online_payment_transactionKey'      => 'Transaktionsschlüssel', // Field for AuthorizeNet_AIM
    'online_payment_testMode'            => 'Test-Modus', // Field for AuthorizeNet_AIM
    'online_payment_developerMode'       => 'Entwicklermodus', // Field for AuthorizeNet_AIM
    'online_payment_websiteKey'          => 'Website-Schlüssel', // Field for Buckaroo_Ideal
    'online_payment_secretKey'           => 'Geheimschlüssel', // Field for Buckaroo_Ideal
    'online_payment_merchantId'          => 'Händler-ID', // Field for CardSave
    'online_payment_password'            => 'Passwort', // Field for CardSave
    'online_payment_apiKey'              => 'API-Schlüssel', // Field for Coinbase
    'online_payment_secret'              => 'Passwort (Secret)', // Field for Coinbase
    'online_payment_accountId'           => 'Konto-ID', // Field for Coinbase
    'online_payment_storeId'             => 'Shop-ID', // Field for FirstData_Connect
    'online_payment_sharedSecret'        => 'Gemeinsamer Schlüssel', // Field for FirstData_Connect
    'online_payment_appId'               => 'App-ID', // Field for GoCardless
    'online_payment_appSecret'           => 'App-Schlüssel', // Field for GoCardless
    'online_payment_accessToken'         => 'Zugangs-Token', // Field for GoCardless
    'online_payment_merchantAccessCode'  => 'Händler Zugangscode', // Field for Migs_ThreeParty
    'online_payment_secureHash'          => 'Sicherer Hash', // Field for Migs_ThreeParty
    'online_payment_siteId'              => 'Seiten ID', // Field for MultiSafepay
    'online_payment_siteCode'            => 'Seiten Code', // Field for MultiSafepay
    'online_payment_accountNumber'       => 'Kontonummer', // Field for NetBanx
    'online_payment_storePassword'       => 'Passwort speichern', // Field for NetBanx
    'online_payment_merchantKey'         => 'Händler-Schlüssel', // Field for PayFast
    'online_payment_pdtKey'              => 'PDT (Payment Data Transfer) Schlüssel', // Field for PayFast
    'online_payment_username'            => 'Benutzername', // Field for Payflow_Pro
    'online_payment_vendor'              => 'Hersteller', // Field for Payflow_Pro
    'online_payment_partner'             => 'Partner', // Field for Payflow_Pro
    'online_payment_pxPostUsername'      => 'PX-Post Benutzername', // Field for PaymentExpress_PxPay
    'online_payment_pxPostPassword'      => 'PX-Post Passwort', // Field for PaymentExpress_PxPay
    'online_payment_signature'           => 'Unterschrift', // Field for PayPal_Express
    'online_payment_referrerId'          => 'Referrer ID', // Field for SagePay_Direct
    'online_payment_transactionPassword' => 'Transaktions-Passwort', // Field for SecurePay_DirectPost
    'online_payment_subAccountId'        => 'Unterkonto-ID', // Field for TargetPay_Directebanking
    'online_payment_secretWord'          => 'Geheimwort', // Field for TwoCheckout
    'online_payment_installationId'      => 'Installations-ID', // Field for WorldPay
    'online_payment_callbackPassword'    => 'Passwort für Rückruf', // Field for WorldPay

    // Status / Error Messages
    'online_payment_payment_cancelled'   => 'Zahlung storniert.',
    'online_payment_payment_failed'      => 'Zahlung fehlgeschlagen. Bitte versuchen Sie es erneut.',
    'online_payment_payment_successful'  => 'Zahlung für Rechnung %s erfolgreich!',
    'online_payment_payment_redirect'    => 'Bitte warten Sie, während wir Sie zur Zahlungsseite weiterleiten...',
    'online_payment_3dauth_redirect'     => 'Bitte warten Sie, während wir Sie auf die Seite des Kreditkarteninstituts zur Authentifizierung weiterleiten...'
);