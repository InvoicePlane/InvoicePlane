<?php
/**
 * Contains the language translations for the payment gateways
 */
$lang = array(
    // General strings
    'online_payment'                     => 'Online betaling',
    'online_payments'                    => 'Online betalingen',
    'online_payment_for'                 => 'Online betaling voor',
    'online_payment_for_invoice'         => 'Online betaling van de factuur',
    'online_payment_method'              => 'Online betaalmethode',
    'online_payment_creditcard_hint'     => 'Voer de onderstaande informatie in als u wilt betalen met creditcard. <br/> De creditcardgegevens worden niet op onze servers opgeslagen en zullen worden overgebracht naar de online betalingsgateway via een beveiligde verbinding.',
    'enable_online_payments'             => 'Online betaling activeren',
    'payment_provider'                   => 'Betalingsprovider',
    'add_payment_provider'               => 'Toevoegen van een betalingsprovider',
    'transaction_reference'              => 'Transactiereferentie',
    'payment_description'                => 'Betaling van factuur %s',

    // Credit card strings
    'creditcard_cvv'                     => 'CVV / CSC',
    'creditcard_details'                 => 'Creditcardgegevens',
    'creditcard_expiry_month'            => 'Vervalmaand',
    'creditcard_expiry_year'             => 'Vervaljaar',
    'creditcard_number'                  => 'Credicardnummer',
    'online_payment_card_invalid'        => 'Deze creditcard is ongeldig. Controleer de verstrekte informatie.',

    // Payment Gateway Fields
    'online_payment_apiLoginId'          => 'API aanmeldings-Id', // Field for AuthorizeNet_AIM
    'online_payment_transactionKey'      => 'Transactiesleutel', // Field for AuthorizeNet_AIM
    'online_payment_testMode'            => 'Test-modus', // Field for AuthorizeNet_AIM
    'online_payment_developerMode'       => 'Ontwikkelaarsmodus', // Field for AuthorizeNet_AIM
    'online_payment_websiteKey'          => 'Websitesleutel', // Field for Buckaroo_Ideal
    'online_payment_secretKey'           => 'Geheime sleutel', // Field for Buckaroo_Ideal
    'online_payment_merchantId'          => 'Handelaars-ID', // Field for CardSave
    'online_payment_password'            => 'Wachtwoord', // Field for CardSave
    'online_payment_apiKey'              => 'API-sleutel', // Field for Coinbase
    'online_payment_secret'              => 'Geheim', // Field for Coinbase
    'online_payment_accountId'           => 'Account-ID', // Field for Coinbase
    'online_payment_storeId'             => 'Winkel-ID', // Field for FirstData_Connect
    'online_payment_sharedSecret'        => 'Gedeeld geheim', // Field for FirstData_Connect
    'online_payment_appId'               => 'App-ID', // Field for GoCardless
    'online_payment_appSecret'           => 'App-geheim', // Field for GoCardless
    'online_payment_accessToken'         => 'Toegangstoken', // Field for GoCardless
    'online_payment_merchantAccessCode'  => 'Handelaar toegangscode', // Field for Migs_ThreeParty
    'online_payment_secureHash'          => 'Veilige Hash', // Field for Migs_ThreeParty
    'online_payment_siteId'              => 'Site-ID', // Field for MultiSafepay
    'online_payment_siteCode'            => 'Site-Code', // Field for MultiSafepay
    'online_payment_accountNumber'       => 'Rekeningnummer', // Field for NetBanx
    'online_payment_storePassword'       => 'Wachtwoord opslaan', // Field for NetBanx
    'online_payment_merchantKey'         => 'Handelaarssleutel', // Field for PayFast
    'online_payment_pdtKey'              => 'PDT-sleutel', // Field for PayFast
    'online_payment_username'            => 'Gebruikersnaam', // Field for Payflow_Pro
    'online_payment_vendor'              => 'Leverancier', // Field for Payflow_Pro
    'online_payment_partner'             => 'Partner', // Field for Payflow_Pro
    'online_payment_pxPostUsername'      => 'PX Post gebruikersnaam', // Field for PaymentExpress_PxPay
    'online_payment_pxPostPassword'      => 'PX Post wachtwoord', // Field for PaymentExpress_PxPay
    'online_payment_signature'           => 'Handtekening', // Field for PayPal_Express
    'online_payment_referrerId'          => 'Verwijzers-ID', // Field for SagePay_Direct
    'online_payment_transactionPassword' => 'Transactie wachtwoord', // Field for SecurePay_DirectPost
    'online_payment_subAccountId'        => 'Subaccount-ID', // Field for TargetPay_Directebanking
    'online_payment_secretWord'          => 'Geheim woord', // Field for TwoCheckout
    'online_payment_installationId'      => 'Installatie-ID', // Field for WorldPay
    'online_payment_callbackPassword'    => 'Callback wachtwoord', // Field for WorldPay

    // Status / Error Messages
    'online_payment_payment_cancelled'   => 'Betaling geannuleerd.',
    'online_payment_payment_failed'      => 'Betaling mislukt. Probeer het opnieuw.',
    'online_payment_payment_successful'  => 'Betaling van factuur %s is geslaagd!',
    'online_payment_payment_redirect'    => 'Een moment geduld a.u.b. , u wordt omgeleid naar de betalingspagina...',
    'online_payment_3dauth_redirect'     => 'Een moment geduld a.u.b., u wordt omgeleid naar uw kaartverstrekker voor verificatie...'
);