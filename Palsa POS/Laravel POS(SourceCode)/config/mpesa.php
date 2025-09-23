<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa integration
    |
    */

    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),
    
    // Environment URLs
    'base_url' => env('MPESA_ENV', 'sandbox') === 'production' 
        ? 'https://api.safaricom.co.ke' 
        : 'https://sandbox.safaricom.co.ke',
    
    'callback_url' => env('MPESA_CALLBACK_URL', env('APP_URL') . '/api/mpesa/callback'),
    
    // Transaction settings
    'timeout_url' => env('MPESA_TIMEOUT_URL', env('APP_URL') . '/api/mpesa/timeout'),
    'result_url' => env('MPESA_RESULT_URL', env('APP_URL') . '/api/mpesa/result'),
    
    // Environment (sandbox or production)
    'environment' => env('MPESA_ENV', 'sandbox'),
    
    // Default transaction type
    'transaction_type' => 'CustomerPayBillOnline',
    
    // Timeout in seconds for HTTP requests
    'timeout' => 30,
];