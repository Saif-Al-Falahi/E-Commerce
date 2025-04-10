<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-Commerce Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'symbol' => 'AED',        // Currency symbol
        'position' => 'before',   // Symbol position (before or after)
        'decimal_point' => '.',   // Decimal point character
        'thousands_separator' => '', // Thousands separator character
        'decimals' => 2,          // Number of decimal places
    ],

    'timezone' => 'Asia/Dubai',

    'pagination' => [
        'per_page' => 12,
    ],

    'cart' => [
        'session_key' => 'cart_id',
        'expiration' => 60 * 24, // 24 hours in minutes
    ],

    'order' => [
        'status' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ],
    ],

    'product' => [
        'image' => [
            'max_size' => 2048, // in KB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        ],
    ],
]; 