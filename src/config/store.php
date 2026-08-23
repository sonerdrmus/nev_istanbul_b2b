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
        'swift' => env('STORE_BANK_SWIFT', 'KTEFTRISXXX'),
        'description' => env('STORE_BANK_DESCRIPTION', 'Sipariş numarası açıklama kısmına yazılmalıdır.'),
    ],

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
