<?php
/**
 * Contains the language translations for the payment gateways
 */
$lang = array(
    // General strings
    'online_payment'                     => 'Pago en linea',
    'online_payments'                    => 'Pagos en linea',
    'online_payment_for'                 => 'Pago en linea para',
    'online_payment_for_invoice'         => 'Pago en línea de factura',
    'online_payment_method'              => 'Método de pago en línea',
    'online_payment_creditcard_hint'     => 'Si desea pagar con tarjeta de crédito, introduzca la siguiente información.<br/>La información de la tarjeta de crédito no se almacenará en nuestros servidores y será trasladada a la pasarela de pago en linea mediante una conexión segura.',
    'enable_online_payments'             => 'Habilitar Pagos en línea',
    'payment_provider'                   => 'Proveedor de pagos',
    'add_payment_provider'               => 'Agregar un Proveedor de Pagos',
    'transaction_reference'              => 'Referencia de la transacción',
    'payment_description'                => 'Pago de factura %s',

    // Credit card strings
    'creditcard_cvv'                     => 'CVV/CSC',
    'creditcard_details'                 => 'Detalles de la tarjeta de crédito',
    'creditcard_expiry_month'            => 'Mes de vencimiento',
    'creditcard_expiry_year'             => 'Año de vencimiento',
    'creditcard_number'                  => 'Numero de tarjeta de crédito',
    'online_payment_card_invalid'        => 'Esta tarjeta de crédito no es válida. Por favor verifique la información proporcionada.',

    // Payment Gateway Fields
    'online_payment_apiLoginId'          => 'ID de acceso de la API', // Field for AuthorizeNet_AIM
    'online_payment_transactionKey'      => 'Clave de transacción', // Field for AuthorizeNet_AIM
    'online_payment_testMode'            => 'Modo de prueba', // Field for AuthorizeNet_AIM
    'online_payment_developerMode'       => 'Modo desarrollador', // Field for AuthorizeNet_AIM
    'online_payment_websiteKey'          => 'Llave de Sitio web', // Field for Buckaroo_Ideal
    'online_payment_secretKey'           => 'Llave secreta', // Field for Buckaroo_Ideal
    'online_payment_merchantId'          => 'ID del comercio', // Field for CardSave
    'online_payment_password'            => 'Contraseña', // Field for CardSave
    'online_payment_apiKey'              => 'Clave API', // Field for Coinbase
    'online_payment_secret'              => 'Secreto', // Field for Coinbase
    'online_payment_accountId'           => 'ID de la cuenta', // Field for Coinbase
    'online_payment_storeId'             => 'Identificación de la tienda', // Field for FirstData_Connect
    'online_payment_sharedSecret'        => 'Secreto compartido', // Field for FirstData_Connect
    'online_payment_appId'               => 'ID de la aplicación', // Field for GoCardless
    'online_payment_appSecret'           => 'App secreta', // Field for GoCardless
    'online_payment_accessToken'         => 'Token de acceso', // Field for GoCardless
    'online_payment_merchantAccessCode'  => 'Código de Acceso Comercial', // Field for Migs_ThreeParty
    'online_payment_secureHash'          => 'Secure Hash', // Field for Migs_ThreeParty
    'online_payment_siteId'              => 'ID del sitio', // Field for MultiSafepay
    'online_payment_siteCode'            => 'Código del sitio', // Field for MultiSafepay
    'online_payment_accountNumber'       => 'Número de cuenta', // Field for NetBanx
    'online_payment_storePassword'       => 'Guardar contraseña', // Field for NetBanx
    'online_payment_merchantKey'         => 'ID del comercio', // Field for PayFast
    'online_payment_pdtKey'              => 'Clave de PDT', // Field for PayFast
    'online_payment_username'            => 'Nombre de Usuario', // Field for Payflow_Pro
    'online_payment_vendor'              => 'Vendedor', // Field for Payflow_Pro
    'online_payment_partner'             => 'Socio', // Field for Payflow_Pro
    'online_payment_pxPostUsername'      => 'Nombre de usuario de correo PX', // Field for PaymentExpress_PxPay
    'online_payment_pxPostPassword'      => 'Contraseña PX Post', // Field for PaymentExpress_PxPay
    'online_payment_signature'           => 'Firma', // Field for PayPal_Express
    'online_payment_referrerId'          => 'Programa de afiliados', // Field for SagePay_Direct
    'online_payment_transactionPassword' => 'Contraseña de la transacción', // Field for SecurePay_DirectPost
    'online_payment_subAccountId'        => 'ID de la subcuenta', // Field for TargetPay_Directebanking
    'online_payment_secretWord'          => 'Palabra secreta', // Field for TwoCheckout
    'online_payment_installationId'      => 'ID de instalación', // Field for WorldPay
    'online_payment_callbackPassword'    => 'Contraseña de devolución de llamada', // Field for WorldPay

    // Status / Error Messages
    'online_payment_payment_cancelled'   => 'Pago cancelado.',
    'online_payment_payment_failed'      => 'El pago falló. Por favor inténtelo nuevamente.',
    'online_payment_payment_successful'  => 'Pago de factura %s se realizó con éxito!',
    'online_payment_payment_redirect'    => 'Favor esperar mientras redirigimos a la página de pago...',
    'online_payment_3dauth_redirect'     => 'Favor esperar mientras redirigimos al emisor de la tarjeta para la autenticación...'
);