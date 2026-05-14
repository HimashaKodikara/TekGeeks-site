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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'ciaboc_backend_api' => [
        'url' => env('CIABOC_BACKEND_API_URL', 'http://eadp.local/api/'),
        'key' =>env('API_KEY', ''),
        'username' => env('CIABOC_BACKEND_API_USER', ''),
        'password' => env('CIABOC_BACKEND_API_PASSWORD', ''),
    ],

    'sms' => [
        'url' => env('SMS_GATEWAY_URL', 'https://api.smsprovider.com/send'),
        'sidcode' => env('SMS_SIDCODE', ''),
        'username' => env('SMS_USERNAME', ''),
        'password' => env('SMS_GATEWAY_PASSWORD', ''),
    ],

    'backend_api' => [
        'url'    => env('BACKEND_API_URL'),
        'secret' => env('BACKEND_JWT_SECRET'),
    ],

    'backend_support_api' => [
        'api_key' => env('BACKEND_SUPPORT_API_KEY', ''),
        'api_secret' => env('API_SECRET', ''),
    ],

    'email' => [
        'email_key' => env('EMAIL_API_KEY', 'https://asset.tekgeeks.net/api/'),
        'email_secret' => env('EMAIL_API_SECRET', ''),
    ],

    'support_api' => [
        'url' => env('SUPPORT_API_URL', 'http://ciaboc-backend-support.test/api/'),
        'username' => env('SUPPORT_API_USER', ''),
        'password' => env('SUPPORT_API_PASSWORD', ''),
    ],

];
