<?php

return [

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
