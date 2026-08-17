<?php

return [

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
        'description' => env('STORE_BANK_DESCRIPTION', 'Sipariş numarası açıklama kısmına yazılmalıdır.'),
    ],

];
