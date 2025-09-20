<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KenyanPaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KenyanPaymentController extends Controller
{
    public function __construct(
        private KenyanPaymentService $kenyanPaymentService,
        private NotificationService $notificationService
    ) {}

    /**
     * Get available Kenyan payment methods
     */
    public function getPaymentMethods()
    {
        try {
            $methods = $this->kenyanPaymentService->getAvailablePaymentMethods();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'payment_methods' => $methods,
                    'currency' => 'KES',
                    'country' => 'Kenya'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment methods', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch payment methods'
            ], 500);
        }
    }

    /**
     * Initiate payment using Kenyan payment systems
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string|exists:orders,order_code',
            'payment_method' => 'required|string|in:mpesa,airtel_money,tkash,equitel,bank_transfer,cash,card',
            'phone_number' => 'required_if:payment_method,mpesa,airtel_money,tkash,equitel|string',
            'bank_code' => 'required_if:payment_method,bank_transfer|string',
        ]);

        try {
            $order = Order::where('order_code', $request->order_code)
                ->where('status', Order::STATUS_PENDING)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or already processed'
                ], 404);
            }

            // Validate payment amount limits
            $paymentMethod = $this->kenyanPaymentService->getPaymentMethod($request->payment_method);
            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment method'
                ], 400);
            }

            $amount = $order->total_with_tax;
            if ($amount < $paymentMethod['min_amount'] || $amount > $paymentMethod['max_amount']) {
                return response()->json([
                    'success' => false,
                    'message' => "Amount must be between KES {$paymentMethod['min_amount']} and KES {$paymentMethod['max_amount']}"
                ], 400);
            }

            // Process payment
            $paymentData = [
                'method' => $request->payment_method,
                'phone_number' => $request->phone_number,
                'bank_code' => $request->bank_code,
                'amount' => $amount
            ];

            $result = $this->kenyanPaymentService->processPayment($order, $paymentData);

            if ($result['success']) {
                // Calculate fees
                $fee = $this->kenyanPaymentService->calculatePaymentFee($request->payment_method, $amount);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'order_code' => $order->order_code,
                        'payment_method' => $result['payment_method'],
                        'amount' => $amount,
                        'fee' => $fee,
                        'total_amount' => $amount + $fee,
                        'currency' => 'KES',
                        'reference' => $result['reference'] ?? null,
                        'instructions' => $result['instructions'] ?? null,
                        'bank_details' => $result['bank_details'] ?? null,
                        'checkout_request_id' => $result['checkout_request_id'] ?? null
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Kenyan payment initiation error', [
                'order_code' => $request->order_code,
                'payment_method' => $request->payment_method,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Validate Kenyan phone number for mobile money
     */
    public function validatePhoneNumber(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'payment_method' => 'required|string|in:mpesa,airtel_money,tkash,equitel'
        ]);

        try {
            $phoneNumber = $request->phone_number;
            $paymentMethod = $request->payment_method;
            
            // Format phone number
            $formattedPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (str_starts_with($formattedPhone, '0')) {
                $formattedPhone = '254' . substr($formattedPhone, 1);
            } elseif (!str_starts_with($formattedPhone, '254')) {
                $formattedPhone = '254' . $formattedPhone;
            }

            // Validate based on network
            $isValid = false;
            $network = '';
            $instructions = '';

            switch ($paymentMethod) {
                case 'mpesa':
                    $isValid = $this->isValidSafaricomNumber($formattedPhone);
                    $network = 'Safaricom';
                    $instructions = 'You will receive an M-Pesa prompt on your phone. Enter your M-Pesa PIN to complete payment.';
                    break;
                case 'airtel_money':
                    $isValid = $this->isValidAirtelNumber($formattedPhone);
                    $network = 'Airtel';
                    $instructions = 'Dial *185# or use your Airtel Money app to complete payment.';
                    break;
                case 'tkash':
                    $isValid = $this->isValidTelkomNumber($formattedPhone);
                    $network = 'Telkom';
                    $instructions = 'Dial *460# to access T-Kash and complete your payment.';
                    break;
                case 'equitel':
                    $isValid = $this->isValidEquitelNumber($formattedPhone);
                    $network = 'Equity Bank';
                    $instructions = 'Use your Equitel SIM toolkit or dial *247# to complete payment.';
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => $isValid,
                    'formatted_phone' => $formattedPhone,
                    'network' => $network,
                    'payment_method' => $paymentMethod,
                    'instructions' => $instructions,
                    'message' => $isValid 
                        ? "Valid {$network} number" 
                        : "Please use a valid {$network} number for {$paymentMethod}"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number validation failed'
            ], 500);
        }
    }

    /**
     * Get Kenyan bank details for transfers
     */
    public function getBankDetails(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string|in:kcb,equity,coop,default'
        ]);

        try {
            $bankDetails = [
                'kcb' => [
                    'bank_name' => 'Kenya Commercial Bank (KCB)',
                    'account_name' => 'Palsa POS Business Account',
                    'account_number' => '1234567890',
                    'branch_code' => '01001',
                    'branch_name' => 'KCB Nairobi Branch',
                    'swift_code' => 'KCBLKENX',
                    'paybill' => '522522',
                    'till_number' => '123456',
                    'mobile_banking' => [
                        'kcb_mpesa' => 'Paybill 522522, Account: Your Phone Number',
                        'mobi_banking' => 'Use KCB Mobile Banking App'
                    ]
                ],
                'equity' => [
                    'bank_name' => 'Equity Bank Kenya',
                    'account_name' => 'Palsa POS Business Account',
                    'account_number' => '0987654321',
                    'branch_code' => '68000',
                    'branch_name' => 'Equity Centre Branch',
                    'swift_code' => 'EQBLKENA',
                    'paybill' => '247247',
                    'till_number' => '654321',
                    'mobile_banking' => [
                        'equitel' => 'Dial *247# from Equitel line',
                        'eazzy_banking' => 'Use Eazzy Banking App'
                    ]
                ],
                'coop' => [
                    'bank_name' => 'Co-operative Bank of Kenya',
                    'account_name' => 'Palsa POS Business Account',
                    'account_number' => '01129123456789',
                    'branch_code' => '01129',
                    'branch_name' => 'Co-op House Branch',
                    'swift_code' => 'COOPKENX',
                    'paybill' => '400200',
                    'till_number' => '789012',
                    'mobile_banking' => [
                        'mcoop_cash' => 'Paybill 400200, Account: Your Phone Number',
                        'coop_mobile' => 'Use Co-op Mobile App'
                    ]
                ]
            ];

            $bankCode = $request->bank_code === 'default' ? 'kcb' : $request->bank_code;
            $details = $bankDetails[$bankCode] ?? $bankDetails['kcb'];

            return response()->json([
                'success' => true,
                'data' => [
                    'bank_details' => $details,
                    'payment_instructions' => [
                        'mobile_money' => 'Use the paybill number with your phone number as account',
                        'mobile_banking' => 'Transfer using your mobile banking app',
                        'internet_banking' => 'Use internet banking with the account details',
                        'branch_banking' => 'Visit any branch with the account details',
                        'atm' => 'Use ATM transfer with account number'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch bank details'
            ], 500);
        }
    }

    // Helper methods for phone number validation
    private function isValidSafaricomNumber(string $phone): bool
    {
        $safaricomPrefixes = ['25470', '25471', '25472', '25473', '25474', '25475', '25476', '25477', '25478', '25479', '254110', '254111', '254112', '254113', '254114', '254115'];
        foreach ($safaricomPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) return true;
        }
        return false;
    }

    private function isValidAirtelNumber(string $phone): bool
    {
        $airtelPrefixes = ['25473', '25475', '25478', '254100', '254101', '254102'];
        foreach ($airtelPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) return true;
        }
        return false;
    }

    private function isValidTelkomNumber(string $phone): bool
    {
        return str_starts_with($phone, '25477');
    }

    private function isValidEquitelNumber(string $phone): bool
    {
        return str_starts_with($phone, '25476');
    }
}