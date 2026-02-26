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
    |--------------------------------------------------------------------------
    | LHDN E-Invoice
    |--------------------------------------------------------------------------
    */
    'lhdn' => [
        'mode' => env('LHDN_MODE', 'mock'),
        'api_url' => env('LHDN_API_URL'),
        'client_id' => env('LHDN_CLIENT_ID'),
        'client_secret' => env('LHDN_CLIENT_SECRET'),
        'tin' => env('LHDN_TIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | DuitNow QR Payment
    |--------------------------------------------------------------------------
    */
    'duitnow' => [
        'merchant_id' => env('DUITNOW_MERCHANT_ID'),
        'secret_key' => env('DUITNOW_SECRET_KEY'),
        'production' => env('DUITNOW_PRODUCTION', false),
        'expiry_minutes' => env('DUITNOW_EXPIRY_MINUTES', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSM Company Lookup
    |--------------------------------------------------------------------------
    */
    'ssm' => [
        'api_key' => env('SSM_API_KEY'),
        'production' => env('SSM_PRODUCTION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Loyalty Points
    |--------------------------------------------------------------------------
    */
    'loyalty' => [
        'points_per_ringgit' => env('LOYALTY_POINTS_PER_RINGGIT', 1),
        'min_spend_for_points' => env('LOYALTY_MIN_SPEND_FOR_POINTS', 1),
        'points_value' => [
            'bronze' => env('LOYALTY_POINTS_VALUE_BRONZE', 0.025),
            'silver' => env('LOYALTY_POINTS_VALUE_SILVER', 0.030),
            'gold' => env('LOYALTY_POINTS_VALUE_GOLD', 0.035),
            'platinum' => env('LOYALTY_POINTS_VALUE_PLATINUM', 0.040),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */
    'inventory' => [
        'low_stock_threshold_default' => env('LOW_STOCK_THRESHOLD_DEFAULT', 10),
        'enable_low_stock_alerts' => env('ENABLE_LOW_STOCK_ALERTS', true),
    ],

];
