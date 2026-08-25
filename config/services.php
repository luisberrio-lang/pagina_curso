<?php

return [

    'izipay' => [
        'payments_enabled' => filter_var(env('IZIPAY_PAYMENTS_ENABLED', false), FILTER_VALIDATE_BOOL),
        'environment' => env('IZIPAY_ENVIRONMENT', 'sandbox'),
        'merchant_code' => env('IZIPAY_MERCHANT_CODE'),
        'api_key' => env('IZIPAY_API_KEY'),
        'hash_key' => env('IZIPAY_HASH_KEY'),
        'public_key' => env('IZIPAY_PUBLIC_KEY'),
        'sdk_url' => env('IZIPAY_ENVIRONMENT', 'sandbox') === 'production'
            ? 'https://checkout.izipay.pe/payments/v1/js/index.js'
            : 'https://sandbox-checkout.izipay.pe/payments/v1/js/index.js',
    ],

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

];
