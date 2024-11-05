<?php
/**
 * Ödeme ağ geçitleri için dil çevirilerini içerir
 */
$lang = array(
    // Genel ifadeler
    'online_payment'                     => 'Online Ödeme',
    'online_payments'                    => 'Online Ödemeler',
    'online_payment_for'                 => 'İçin Online Ödeme',
    'online_payment_for_invoice'         => 'Fatura İçin Online Ödeme',
    'online_payment_method'              => 'Online Ödeme Yöntemi',
    'online_payment_creditcard_hint'     => 'Kredi kartı ile ödeme yapmak istiyorsanız, lütfen aşağıdaki bilgileri girin.<br/>Kredi kartı bilgileri sunucularımızda saklanmaz ve güvenli bir bağlantı kullanılarak online ödeme ağ geçidine aktarılır.',
    'enable_online_payments'             => 'Online Ödemeleri Etkinleştir',
    'payment_provider'                   => 'Ödeme Sağlayıcısı',
    'provider_response'                  => 'Sağlayıcı Yanıtı',
    'add_payment_provider'               => 'Ödeme Sağlayıcısı Ekle',
    'transaction_reference'              => 'İşlem Referansı',
    'transaction_successful'             => 'İşlem başarılı',
    'payment_description'                => 'Fatura %s için Ödeme',

    // Kredi kartı ifadeleri
    'creditcard_cvv'                     => 'CVV / CSC',
    'creditcard_details'                 => 'Kredi Kartı Bilgileri',
    'creditcard_expiry_month'            => 'Son Kullanma Ayı',
    'creditcard_expiry_year'             => 'Son Kullanma Yılı',
    'creditcard_number'                  => 'Kredi Kartı Numarası',
    'online_payment_card_invalid'        => 'Bu kredi kartı geçersiz. Lütfen sağlanan bilgileri kontrol edin.',

    // Ödeme Ağ Geçidi Alanları
    'online_payment_apiLoginId'          => 'API Giriş Id', // AuthorizeNet_AIM alanı
    'online_payment_transactionKey'      => 'İşlem Anahtarı', // AuthorizeNet_AIM alanı
    'online_payment_testMode'            => 'Test Modu', // AuthorizeNet_AIM alanı
    'online_payment_developerMode'       => 'Geliştirici Modu', // AuthorizeNet_AIM alanı
    'online_payment_websiteKey'          => 'Web Sitesi Anahtarı', // Buckaroo_Ideal alanı
    'online_payment_secretKey'           => 'Gizli Anahtar', // Buckaroo_Ideal alanı
    'online_payment_merchantId'          => 'Tüccar Id', // CardSave alanı
    'online_payment_password'            => 'Şifre', // CardSave alanı
    'online_payment_apiKey'              => 'API Anahtarı', // Coinbase alanı
    'online_payment_secret'              => 'Gizli', // Coinbase alanı
    'online_payment_accountId'           => 'Hesap Id', // Coinbase alanı
    'online_payment_storeId'             => 'Mağaza Id', // FirstData_Connect alanı
    'online_payment_sharedSecret'        => 'Paylaşılan Gizli Anahtar', // FirstData_Connect alanı
    'online_payment_appId'               => 'Uygulama Id', // GoCardless alanı
    'online_payment_appSecret'           => 'Uygulama Gizli Anahtarı', // GoCardless alanı
    'online_payment_accessToken'         => 'Erişim Tokeni', // GoCardless alanı
    'online_payment_merchantAccessCode'  => 'Tüccar Erişim Kodu', // Migs_ThreeParty alanı
    'online_payment_secureHash'          => 'Güvenli Hash', // Migs_ThreeParty alanı
    'online_payment_siteId'              => 'Site Id', // MultiSafepay alanı
    'online_payment_siteCode'            => 'Site Kodu', // MultiSafepay alanı
    'online_payment_accountNumber'       => 'Hesap Numarası', // NetBanx alanı
    'online_payment_storePassword'       => 'Mağaza Şifresi', // NetBanx alanı
    'online_payment_merchantKey'         => 'Tüccar Anahtarı', // PayFast alanı
    'online_payment_pdtKey'              => 'Pdt Anahtarı', // PayFast alanı
    'online_payment_username'            => 'Kullanıcı Adı', // Payflow_Pro alanı
    'online_payment_vendor'              => 'Satıcı', // Payflow_Pro alanı
    'online_payment_partner'             => 'Ortak', // Payflow_Pro alanı
    'online_payment_pxPostUsername'      => 'Px Post Kullanıcı Adı', // PaymentExpress_PxPay alanı
    'online_payment_pxPostPassword'      => 'Px Post Şifresi', // PaymentExpress_PxPay alanı
    'online_payment_signature'           => 'İmza', // PayPal_Express alanı
    'online_payment_referrerId'          => 'Referans Id', // SagePay_Direct alanı
    'online_payment_transactionPassword' => 'İşlem Şifresi', // SecurePay_DirectPost alanı
    'online_payment_subAccountId'        => 'Alt Hesap Id', // TargetPay_Directebanking alanı
    'online_payment_secretWord'          => 'Gizli Kelime', // TwoCheckout alanı
    'online_payment_installationId'      => 'Kurulum Id', // WorldPay alanı
    'online_payment_callbackPassword'    => 'Geri Çağırma Şifresi', // WorldPay alanı

    // Durum / Hata Mesajları
    'online_payment_payment_cancelled'   => 'Ödeme iptal edildi.',
    'online_payment_payment_failed'      => 'Ödeme başarısız oldu. Lütfen tekrar deneyin.',
    'online_payment_payment_successful'  => 'Fatura %s için ödeme başarılı!',
    'online_payment_payment_redirect'    => 'Ödeme sayfasına yönlendirilirken lütfen bekleyin...',
    'online_payment_3dauth_redirect'     => 'Kimlik doğrulama için kart sağlayıcınıza yönlendirilirken lütfen bekleyin...'
);
