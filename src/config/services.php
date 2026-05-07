<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | TCMB günlük döviz tablosu (today.xml); USD/EUR canlı güncelleme için kullanılır.
    | Hafta sonu / tatilde son iş gününün tablosu servis edilir.
    */
    'tcmb' => [
        'kurlar_url' => env('TCMB_KURLAR_URL', 'https://www.tcmb.gov.tr/kurlar/today.xml'),
        'cache_ttl_seconds' => (int) env('TCMB_CACHE_TTL', 45),
        'timeout' => (int) env('TCMB_HTTP_TIMEOUT', 12),
    ],

];
