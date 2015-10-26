<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------
| Online Payment Providers
| -------------------------------------------------------------------
| This is a list of available online payment providers that are
| used by the settings.
|
*/

$config['payment_gateways'] = array(
    "AuthorizeNet_AIM",
    "AuthorizeNet_SIM",
    "Buckaroo_Ideal",
    "Buckaroo_PayPal",
    "CardSave",
    "Coinbase",
    "Eway_Rapid",
    "FirstData_Connect",
    "GoCardless",
    "Migs_ThreeParty",
    "Migs_TwoParty",
    "Mollie",
    "MultiSafepay",
    "Netaxept",
    "NetBanx",
    "PayFast",
    "Payflow_Pro",
    "PaymentExpress_PxPay",
    "PaymentExpress_PxPost",
    "PayPal_Express",
    "PayPal_Pro",
    "Pin",
    "SagePay_Direct",
    "SagePay_Server",
    "SecurePay_DirectPost",
    "Stripe",
    "TargetPay_Directebanking",
    "TargetPay_Ideal",
    "TargetPay_Mrcash",
    "TwoCheckout",
    "WorldPay"
);