<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KenyanPaymentService
{
    private MpesaService $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Process payment based on Kenyan payment method
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        $paymentMethod = $paymentData['method'];
        
        return match($paymentMethod) {
            'mpesa' => $this->processMpesaPayment($order, $paymentData),
            'airtel_money' => $this->processAirtelMoneyPayment($order, $paymentData),
            'tkash' => $this->processTkashPayment($order, $paymentData),
            'equitel' => $this->processEquitelPayment($order, $paymentData),
            'bank_transfer' => $this->processBankTransferPayment($order, $paymentData),
            'cash' => $this->processCashPayment($order, $paymentData),
            'card' => $this->processCardPayment($order, $paymentData),
            default => [
                'success' => false,
                'message' => 'Unsupported payment method'
            ]
        };
    }

    /**
     * M-Pesa Payment (Safaricom)
     */
    private function processMpesaPayment(Order $order, array $paymentData): array
    {
        $phoneNumber = $this->formatKenyanPhoneNumber($paymentData['phone_number']);
        
        if (!$this->isValidSafaricomNumber($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Please use a valid Safaricom number (07xx, 01xx, or 254xxx)'
            ];
        }

        return $this->mpesaService->stkPush($order, $phoneNumber);
    }

    /**
     * Airtel Money Payment
     */
    private function processAirtelMoneyPayment(Order $order, array $paymentData): array
    {
        $phoneNumber = $this->formatKenyanPhoneNumber($paymentData['phone_number']);
        
        if (!$this->isValidAirtelNumber($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Please use a valid Airtel number (073x, 078x, or 254xxx)'
            ];
        }

        // Airtel Money integration would go here
        // For now, we'll simulate the process
        Log::info('Airtel Money payment initiated', [
            'order_code' => $order->order_code,
            'phone' => $phoneNumber,
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'Airtel Money payment request sent. Please check your phone.',
            'payment_method' => 'Airtel Money',
            'reference' => 'AM-' . time(),
            'instructions' => 'Dial *185# or check your Airtel Money app to complete payment'
        ];
    }

    /**
     * T-Kash Payment (Telkom)
     */
    private function processTkashPayment(Order $order, array $paymentData): array
    {
        $phoneNumber = $this->formatKenyanPhoneNumber($paymentData['phone_number']);
        
        if (!$this->isValidTelkomNumber($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Please use a valid Telkom number (077x or 254xxx)'
            ];
        }

        Log::info('T-Kash payment initiated', [
            'order_code' => $order->order_code,
            'phone' => $phoneNumber,
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'T-Kash payment request sent. Please check your phone.',
            'payment_method' => 'T-Kash',
            'reference' => 'TK-' . time(),
            'instructions' => 'Dial *460# to complete your T-Kash payment'
        ];
    }

    /**
     * Equitel Payment
     */
    private function processEquitelPayment(Order $order, array $paymentData): array
    {
        $phoneNumber = $this->formatKenyanPhoneNumber($paymentData['phone_number']);
        
        if (!$this->isValidEquitelNumber($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Please use a valid Equitel number (076x or 254xxx)'
            ];
        }

        Log::info('Equitel payment initiated', [
            'order_code' => $order->order_code,
            'phone' => $phoneNumber,
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'Equitel payment request sent. Please check your phone.',
            'payment_method' => 'Equitel',
            'reference' => 'EQ-' . time(),
            'instructions' => 'Check your Equitel SIM toolkit or dial *247# to complete payment'
        ];
    }

    /**
     * Bank Transfer Payment
     */
    private function processBankTransferPayment(Order $order, array $paymentData): array
    {
        $bankDetails = $this->getKenyanBankDetails($paymentData['bank_code'] ?? 'default');
        
        Log::info('Bank transfer payment initiated', [
            'order_code' => $order->order_code,
            'bank' => $paymentData['bank_code'] ?? 'default',
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'Bank transfer details provided. Please complete payment.',
            'payment_method' => 'Bank Transfer',
            'reference' => 'BT-' . $order->order_code,
            'bank_details' => $bankDetails,
            'instructions' => 'Transfer the amount to the provided account and upload payment slip'
        ];
    }

    /**
     * Cash Payment
     */
    private function processCashPayment(Order $order, array $paymentData): array
    {
        Log::info('Cash payment initiated', [
            'order_code' => $order->order_code,
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'Cash payment selected. Please pay at collection.',
            'payment_method' => 'Cash',
            'reference' => 'CASH-' . $order->order_code,
            'instructions' => 'Pay cash when collecting your order'
        ];
    }

    /**
     * Card Payment (Visa/Mastercard)
     */
    private function processCardPayment(Order $order, array $paymentData): array
    {
        // This would integrate with Kenyan card processors like:
        // - Pesapal, Flutterwave, DPO Group, etc.
        
        Log::info('Card payment initiated', [
            'order_code' => $order->order_code,
            'amount' => $order->total_with_tax
        ]);

        return [
            'success' => true,
            'message' => 'Card payment gateway will open shortly.',
            'payment_method' => 'Card Payment',
            'reference' => 'CARD-' . time(),
            'instructions' => 'You will be redirected to secure payment gateway'
        ];
    }

    /**
     * Format Kenyan phone numbers
     */
    private function formatKenyanPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Handle different Kenyan formats
        if (str_starts_with($phone, '254')) {
            return $phone; // Already in international format
        } elseif (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1); // Remove 0 and add 254
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '254' . $phone; // Add 254 prefix
        }
        
        return $phone;
    }

    /**
     * Validate Safaricom numbers (M-Pesa)
     */
    private function isValidSafaricomNumber(string $phoneNumber): bool
    {
        $phone = $this->formatKenyanPhoneNumber($phoneNumber);
        
        // Safaricom prefixes: 254700-254799, 254701-254709, 254110-254115
        $safaricomPrefixes = [
            '25470', '25471', '25472', '25473', '25474', '25475', 
            '25476', '25477', '25478', '25479', '254110', '254111', 
            '254112', '254113', '254114', '254115'
        ];
        
        foreach ($safaricomPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate Airtel numbers
     */
    private function isValidAirtelNumber(string $phoneNumber): bool
    {
        $phone = $this->formatKenyanPhoneNumber($phoneNumber);
        
        // Airtel prefixes: 254730-254739, 254750-254759, 254780-254789
        $airtelPrefixes = [
            '25473', '25475', '25478', '254100', '254101', '254102'
        ];
        
        foreach ($airtelPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate Telkom numbers (T-Kash)
     */
    private function isValidTelkomNumber(string $phoneNumber): bool
    {
        $phone = $this->formatKenyanPhoneNumber($phoneNumber);
        
        // Telkom prefixes: 254770-254779
        return str_starts_with($phone, '25477');
    }

    /**
     * Validate Equitel numbers
     */
    private function isValidEquitelNumber(string $phoneNumber): bool
    {
        $phone = $this->formatKenyanPhoneNumber($phoneNumber);
        
        // Equitel prefixes: 254760-254769
        return str_starts_with($phone, '25476');
    }

    /**
     * Get Kenyan bank details for transfers
     */
    private function getKenyanBankDetails(string $bankCode): array
    {
        $bankDetails = [
            'kcb' => [
                'bank_name' => 'Kenya Commercial Bank (KCB)',
                'account_name' => 'Palsa POS Business Account',
                'account_number' => '1234567890',
                'branch_code' => '01001',
                'swift_code' => 'KCBLKENX',
                'paybill' => '522522'
            ],
            'equity' => [
                'bank_name' => 'Equity Bank Kenya',
                'account_name' => 'Palsa POS Business Account',
                'account_number' => '0987654321',
                'branch_code' => '68000',
                'swift_code' => 'EQBLKENA',
                'paybill' => '247247'
            ],
            'coop' => [
                'bank_name' => 'Co-operative Bank of Kenya',
                'account_name' => 'Palsa POS Business Account',
                'account_number' => '01129123456789',
                'branch_code' => '01129',
                'swift_code' => 'COOPKENX',
                'paybill' => '400200'
            ],
            'default' => [
                'bank_name' => 'Kenya Commercial Bank (KCB)',
                'account_name' => 'Palsa POS Business Account',
                'account_number' => '1234567890',
                'branch_code' => '01001',
                'swift_code' => 'KCBLKENX',
                'paybill' => '522522'
            ]
        ];

        return $bankDetails[$bankCode] ?? $bankDetails['default'];
    }

    /**
     * Get all available Kenyan payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            [
                'code' => 'mpesa',
                'name' => 'M-Pesa',
                'description' => 'Pay using Safaricom M-Pesa',
                'logo' => 'mpesa-logo.png',
                'network' => 'Safaricom',
                'ussd' => '*334#',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 300000
            ],
            [
                'code' => 'airtel_money',
                'name' => 'Airtel Money',
                'description' => 'Pay using Airtel Money',
                'logo' => 'airtel-money-logo.png',
                'network' => 'Airtel',
                'ussd' => '*185#',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 150000
            ],
            [
                'code' => 'tkash',
                'name' => 'T-Kash',
                'description' => 'Pay using Telkom T-Kash',
                'logo' => 'tkash-logo.png',
                'network' => 'Telkom',
                'ussd' => '*460#',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 100000
            ],
            [
                'code' => 'equitel',
                'name' => 'Equitel',
                'description' => 'Pay using Equity Bank Equitel',
                'logo' => 'equitel-logo.png',
                'network' => 'Equity Bank',
                'ussd' => '*247#',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 200000
            ],
            [
                'code' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer or mobile banking',
                'logo' => 'bank-transfer-logo.png',
                'network' => 'All Banks',
                'ussd' => 'Various',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 10000000
            ],
            [
                'code' => 'cash',
                'name' => 'Cash Payment',
                'description' => 'Pay cash on delivery/collection',
                'logo' => 'cash-logo.png',
                'network' => 'Physical',
                'ussd' => 'N/A',
                'active' => true,
                'fee_percentage' => 0,
                'min_amount' => 1,
                'max_amount' => 1000000
            ],
            [
                'code' => 'card',
                'name' => 'Debit/Credit Card',
                'description' => 'Pay using Visa or Mastercard',
                'logo' => 'card-logo.png',
                'network' => 'Visa/Mastercard',
                'ussd' => 'N/A',
                'active' => true,
                'fee_percentage' => 2.5,
                'min_amount' => 1,
                'max_amount' => 5000000
            ]
        ];
    }

    /**
     * Get payment method by code
     */
    public function getPaymentMethod(string $code): ?array
    {
        $methods = $this->getAvailablePaymentMethods();
        
        foreach ($methods as $method) {
            if ($method['code'] === $code) {
                return $method;
            }
        }
        
        return null;
    }

    /**
     * Calculate payment fees for Kenyan methods
     */
    public function calculatePaymentFee(string $paymentMethod, float $amount): float
    {
        $method = $this->getPaymentMethod($paymentMethod);
        
        if (!$method) {
            return 0;
        }

        // Most mobile money services in Kenya don't charge merchants
        // But cards typically have processing fees
        if ($method['code'] === 'card') {
            return $amount * ($method['fee_percentage'] / 100);
        }

        return 0; // No fees for mobile money
    }
}