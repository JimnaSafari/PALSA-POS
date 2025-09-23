<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kenyan Localization Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'code' => 'KES',
        'symbol' => 'KSh',
        'name' => 'Kenya Shilling',
        'decimal_places' => 2,
    ],

    'tax' => [
        'vat_rate' => 0.16, // 16% VAT in Kenya
        'vat_name' => 'VAT',
        'include_in_price' => false,
    ],

    'phone' => [
        'country_code' => '+254',
        'formats' => [
            'display' => '+254 XXX XXX XXX',
            'input' => '07XXXXXXXX',
        ],
        'networks' => [
            'safaricom' => ['070', '071', '072', '073', '074', '075', '076', '077', '078', '079', '0110', '0111', '0112', '0113', '0114', '0115'],
            'airtel' => ['073', '075', '078', '0100', '0101', '0102'],
            'telkom' => ['077'],
            'equitel' => ['076'],
        ],
    ],

    'business' => [
        'timezone' => 'Africa/Nairobi',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'datetime_format' => 'd/m/Y H:i',
    ],

    'payment_methods' => [
        'mpesa' => [
            'name' => 'M-Pesa',
            'description' => 'Pay with M-Pesa mobile money',
            'icon' => 'mpesa-icon.png',
            'enabled' => true,
        ],
        'airtel_money' => [
            'name' => 'Airtel Money',
            'description' => 'Pay with Airtel Money',
            'icon' => 'airtel-icon.png',
            'enabled' => false,
        ],
        'tkash' => [
            'name' => 'T-Kash',
            'description' => 'Pay with T-Kash (Telkom)',
            'icon' => 'tkash-icon.png',
            'enabled' => false,
        ],
        'equitel' => [
            'name' => 'Equitel',
            'description' => 'Pay with Equitel (Equity Bank)',
            'icon' => 'equitel-icon.png',
            'enabled' => false,
        ],
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'description' => 'Pay via bank transfer',
            'icon' => 'bank-icon.png',
            'enabled' => true,
        ],
        'cash' => [
            'name' => 'Cash',
            'description' => 'Pay with cash',
            'icon' => 'cash-icon.png',
            'enabled' => true,
        ],
    ],

    'banks' => [
        'kcb' => [
            'name' => 'Kenya Commercial Bank',
            'code' => 'KCB',
            'paybill' => '522522',
        ],
        'equity' => [
            'name' => 'Equity Bank',
            'code' => 'EQUITY',
            'paybill' => '247247',
        ],
        'coop' => [
            'name' => 'Co-operative Bank',
            'code' => 'COOP',
            'paybill' => '400200',
        ],
        'standard_chartered' => [
            'name' => 'Standard Chartered Bank',
            'code' => 'SCB',
            'paybill' => '329329',
        ],
    ],
];