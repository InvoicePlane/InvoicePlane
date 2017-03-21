<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Online Payment Providers
| -------------------------------------------------------------------
| This is a list of available online payment providers that are
| used by the settings.
|
*/

$config['payment_gateways'] = array(
    'AuthorizeNet_AIM' => array(
        'apiLoginId' => array(
            'type' => 'text',
            'label' => 'Api Login Id',
        ),
        'transactionKey' => array(
            'type' => 'text',
            'label' => 'Transaction Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
        'developerMode' => array(
            'type' => 'checkbox',
            'label' => 'Developer Mode',
        ),
        //'liveEndpoint' => array(
        //    'type' => 'text',
        //    'label' => 'Live Endpoint',
        //),
        //'developerEndpoint' => array(
        //    'type' => 'text',
        //    'label' => 'Developer Endpoint',
        //),
    ),
    'AuthorizeNet_SIM' => array(
        'apiLoginId' => array(
            'type' => 'text',
            'label' => 'Api Login Id',
        ),
        'transactionKey' => array(
            'type' => 'text',
            'label' => 'Transaction Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
        'developerMode' => array(
            'type' => 'checkbox',
            'label' => 'Developer Mode',
        ),
        //'liveEndpoint' => array(
        //    'type' => 'text',
        //    'label' => 'Live Endpoint',
        //),
        //'developerEndpoint' => array(
        //    'type' => 'text',
        //    'label' => 'Developer Endpoint',
        //),
        //'hashSecret' => array(
        //    'type' => 'text',
        //    'label' => 'Hash Secret',
        //),
    ),
    'Buckaroo_Ideal' => array(
        'websiteKey' => array(
            'type' => 'text',
            'label' => 'Website Key',
        ),
        'secretKey' => array(
            'type' => 'password',
            'label' => 'Secret Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Buckaroo_PayPal' => array(
        'websiteKey' => array(
            'type' => 'text',
            'label' => 'Website Key',
        ),
        'secretKey' => array(
            'type' => 'password',
            'label' => 'Secret Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'CardSave' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
    ),
    'Coinbase' => array(
        'apiKey' => array(
            'type' => 'text',
            'label' => 'Api Key',
        ),
        'secret' => array(
            'type' => 'password',
            'label' => 'Secret',
        ),
        'accountId' => array(
            'type' => 'text',
            'label' => 'Account Id',
        ),
    ),
    'Eway_Rapid' => array(
        'apiKey' => array(
            'type' => 'text',
            'label' => 'Api Key',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'FirstData_Connect' => array(
        'storeId' => array(
            'type' => 'text',
            'label' => 'Store Id',
        ),
        'sharedSecret' => array(
            'type' => 'password',
            'label' => 'Shared Secret',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'GoCardless' => array(
        'appId' => array(
            'type' => 'text',
            'label' => 'App Id',
        ),
        'appSecret' => array(
            'type' => 'password',
            'label' => 'App Secret',
        ),
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'accessToken' => array(
            'type' => 'text',
            'label' => 'Access Token',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Migs_ThreeParty' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'merchantAccessCode' => array(
            'type' => 'text',
            'label' => 'Merchant Access Code',
        ),
        'secureHash' => array(
            'type' => 'text',
            'label' => 'Secure Hash',
        ),
    ),
    'Migs_TwoParty' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'merchantAccessCode' => array(
            'type' => 'text',
            'label' => 'Merchant Access Code',
        ),
        'secureHash' => array(
            'type' => 'text',
            'label' => 'Secure Hash',
        ),
    ),
    'Mollie' => array(
        'apiKey' => array(
            'type' => 'text',
            'label' => 'Api Key',
        ),
    ),
    'MultiSafepay' => array(
        'accountId' => array(
            'type' => 'text',
            'label' => 'Account Id',
        ),
        'siteId' => array(
            'type' => 'text',
            'label' => 'Site Id',
        ),
        'siteCode' => array(
            'type' => 'text',
            'label' => 'Site Code',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Netaxept' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'NetBanx' => array(
        'accountNumber' => array(
            'type' => 'text',
            'label' => 'Account Number',
        ),
        'storeId' => array(
            'type' => 'text',
            'label' => 'Store Id',
        ),
        'storePassword' => array(
            'type' => 'password',
            'label' => 'Store Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'PayFast' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'merchantKey' => array(
            'type' => 'text',
            'label' => 'Merchant Key',
        ),
        'pdtKey' => array(
            'type' => 'text',
            'label' => 'Pdt Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Payflow_Pro' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'vendor' => array(
            'type' => 'text',
            'label' => 'Vendor',
        ),
        'partner' => array(
            'type' => 'text',
            'label' => 'Partner',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'PaymentExpress_PxPay' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'pxPostUsername' => array(
            'type' => 'text',
            'label' => 'Px Post Username',
        ),
        'pxPostPassword' => array(
            'type' => 'password',
            'label' => 'Px Post Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'PaymentExpress_PxPost' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'PayPal_Express' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'signature' => array(
            'type' => 'password',
            'label' => 'Signature',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
        //'solutionType' => array(
        //    'type' => 'text',
        //    'label' => 'Solution Type',
        //),
        //'landingPage' => array(
        //    'type' => 'text',
        //    'label' => 'Landing Page',
        //),
        //'brandName' => array(
        //    'type' => 'text',
        //    'label' => 'Brand Name',
        //),
        //'headerImageUrl' => array(
        //    'type' => 'text',
        //    'label' => 'Header Image Url',
        //),
        //'logoImageUrl' => array(
        //    'type' => 'text',
        //    'label' => 'Logo Image Url',
        //),
        //'borderColor' => array(
        //    'type' => 'text',
        //    'label' => 'Border Color',
        //),
    ),
    'PayPal_Pro' => array(
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password',
        ),
        'signature' => array(
            'type' => 'text',
            'label' => 'Signature',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Pin' => array(
        'secretKey' => array(
            'type' => 'password',
            'label' => 'Secret Key',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'SagePay_Direct' => array(
        'vendor' => array(
            'type' => 'text',
            'label' => 'Vendor',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
        'referrerId' => array(
            'type' => 'text',
            'label' => 'Referrer Id',
        ),
    ),
    'SagePay_Server' => array(
        'vendor' => array(
            'type' => 'text',
            'label' => 'Vendor',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
        'referrerId' => array(
            'type' => 'text',
            'label' => 'Referrer Id',
        ),
    ),
    'SecurePay_DirectPost' => array(
        'merchantId' => array(
            'type' => 'text',
            'label' => 'Merchant Id',
        ),
        'transactionPassword' => array(
            'type' => 'password',
            'label' => 'Transaction Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'Stripe' => array(
        'apiKey' => array(
            'type' => 'password',
            'label' => 'Api Key',
        ),
    ),
    'TargetPay_Directebanking' => array(
        'subAccountId' => array(
            'type' => 'text',
            'label' => 'Sub Account Id',
        ),
    ),
    'TargetPay_Ideal' => array(
        'subAccountId' => array(
            'type' => 'text',
            'label' => 'Sub Account Id',
        ),
    ),
    'TargetPay_Mrcash' => array(
        'subAccountId' => array(
            'type' => 'text',
            'label' => 'Sub Account Id',
        ),
    ),
    'TwoCheckout' => array(
        'accountNumber' => array(
            'type' => 'text',
            'label' => 'Account Number',
        ),
        'secretWord' => array(
            'type' => 'password',
            'label' => 'Secret Word',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
    'WorldPay' => array(
        'installationId' => array(
            'type' => 'text',
            'label' => 'Installation Id',
        ),
        'accountId' => array(
            'type' => 'text',
            'label' => 'Account Id',
        ),
        'secretWord' => array(
            'type' => 'password',
            'label' => 'Secret Word',
        ),
        'callbackPassword' => array(
            'type' => 'password',
            'label' => 'Callback Password',
        ),
        'testMode' => array(
            'type' => 'checkbox',
            'label' => 'Test Mode',
        ),
    ),
);