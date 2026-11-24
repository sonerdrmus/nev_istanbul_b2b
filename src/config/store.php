<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mağaza dilleri (URL öneki)
    |--------------------------------------------------------------------------
    */
    'locales' => ['tr', 'en', 'it'],
    'default_locale' => env('STORE_DEFAULT_LOCALE', 'tr'),

    /*
    |--------------------------------------------------------------------------
    | Admin paneli güvenlik
    |--------------------------------------------------------------------------
    | ADMIN_ALLOWED_IPS boşsa IP kısıtı uygulanmaz.
    | Virgülle ayrılmış liste: 1.2.3.4,10.0.0.0/8
    */
    'admin_allowed_ips' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ADMIN_ALLOWED_IPS', ''))
    ))),
    'admin_force_2fa' => (bool) env('ADMIN_FORCE_2FA', false),

    /*
    |--------------------------------------------------------------------------
    | İletişim formu
    |--------------------------------------------------------------------------
    */
    'contact' => [
        'to' => env('CONTACT_MAIL_TO', 'info@nevistanbul.com.tr'),
        'max_attachments' => 5,
        'max_kilobytes' => 8192,
        'map_query' => env(
            'CONTACT_MAP_QUERY',
            '15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar, İstanbul, Türkiye'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp destek
    |--------------------------------------------------------------------------
    | Mağaza sağ alt köşe butonu. Boşsa buton gizlenir.
    | Görünen numara ve wa.me için E.164 (ülke kodu + numara, + olmadan da olur).
    */
    'whatsapp' => [
        'number' => env('WHATSAPP_SUPPORT_NUMBER', '+90 532 690 59 19'),
        'enabled' => filter_var(env('WHATSAPP_SUPPORT_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sosyal medya (footer)
    |--------------------------------------------------------------------------
    | Boş bırakılanlar footer'da gizlenir.
    */
    'social' => [
        'instagram' => env('STORE_SOCIAL_INSTAGRAM', ''),
        'linkedin' => env('STORE_SOCIAL_LINKEDIN', ''),
        'facebook' => env('STORE_SOCIAL_FACEBOOK', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ödeme: Havale / EFT
    |--------------------------------------------------------------------------
    | Sipariş onay sayfasında gösterilecek banka hesap bilgileri.
    | Boş bırakılırsa "e-posta ile iletilecektir" mesajı kullanılır.
    */
    'bank_transfer' => [
        'enabled' => env('STORE_BANK_TRANSFER_ENABLED', false),
        'bank_name' => env('STORE_BANK_NAME', ''),
        'iban' => env('STORE_BANK_IBAN', ''),
        'account_holder' => env('STORE_BANK_ACCOUNT_HOLDER', ''),
        'branch' => env('STORE_BANK_BRANCH', ''),
        'swift' => env('STORE_BANK_SWIFT', 'KTEFTRISXXX'),
        'description' => env('STORE_BANK_DESCRIPTION', 'Sipariş numarası açıklama kısmına yazılmalıdır.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Medya CDN (opsiyonel preconnect)
    |--------------------------------------------------------------------------
    | S3/CDN host APP_URL’den farklıysa layout’ta preconnect eklenir.
    | Öncelik: admin panel (store_settings.media_cdn_url) → bu env değeri.
    */
    'media_cdn_url' => env('MEDIA_CDN_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Proforma fatura
    |--------------------------------------------------------------------------
    */
    'proforma' => [
        'address_line_1' => env('STORE_PROFORMA_ADDRESS_1', '15 Temmuz mahallesi 1432 sokak no:26-30 34212'),
        'address_line_2' => env('STORE_PROFORMA_ADDRESS_2', '34212 - Bağcılar - İstanbul'),
        'accounting_email' => env('STORE_PROFORMA_EMAIL', 'accounting@nevistanbul.com.tr'),
        'swift' => env('STORE_BANK_SWIFT', 'KTEFTRISXXX'),
    ],

];
