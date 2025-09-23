<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MpesaService
{
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortcode;
    private string $passkey;
    private string $baseUrl;
    private string $callbackUrl;

    public function __construct()
    {
        $this->consumerKey = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode = config('mpesa.shortcode');
        $this->passkey = config('mpesa.passkey');
        $this->baseUrl = config('mpesa.base_url');
        $this->callbackUrl = config('mpesa.callback_url');
    }

    /**
     * Generate M-Pesa access token
     */
    public function generateAccessToken(): ?string
    {
        try {
            $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('M-Pesa token generation failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa token generation exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Initiate STK Push (Lipa na M-Pesa Online)
     */
    public function stkPush(Order $order, string $phoneNumber): array
    {
        $accessToken = $this->generateAccessToken();
        
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to generate M-Pesa access token'
            ];
        }

        try {
            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $order->total_with_tax,
                'PartyA' => $this->formatPhoneNumber($phoneNumber),
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $this->formatPhoneNumber($phoneNumber),
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $order->order_code,
                'TransactionDesc' => "Payment for Order {$order->order_code}"
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['ResponseCode'] === '0') {
                    // Store the checkout request ID for tracking
                    $this->storeMpesaTransaction($order, $responseData);
                    
                    return [
                        'success' => true,
                        'message' => 'STK Push sent successfully',
                        'checkout_request_id' => $responseData['CheckoutRequestID'],
                        'merchant_request_id' => $responseData['MerchantRequestID']
                    ];
                }
            }

            Log::error('M-Pesa STK Push failed', [
                'order_id' => $order->id,
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to initiate M-Pesa payment'
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'M-Pesa service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query STK Push status
     */
    public function queryStkStatus(string $checkoutRequestId): array
    {
        $accessToken = $this->generateAccessToken();
        
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to generate access token'
            ];
        }

        try {
            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to query payment status'
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa status query exception', [
                'checkout_request_id' => $checkoutRequestId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Query service error'
            ];
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function handleCallback(array $callbackData): bool
    {
        try {
            Log::info('M-Pesa callback received', $callbackData);

            $resultCode = $callbackData['Body']['stkCallback']['ResultCode'] ?? null;
            $checkoutRequestId = $callbackData['Body']['stkCallback']['CheckoutRequestID'] ?? null;

            if ($resultCode === 0) {
                // Payment successful
                $callbackMetadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
                
                $transactionData = $this->parseCallbackMetadata($callbackMetadata);
                $this->processSuccessfulPayment($checkoutRequestId, $transactionData);
                
                return true;
            } else {
                // Payment failed or cancelled
                $this->processFailedPayment($checkoutRequestId, $resultCode);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('M-Pesa callback processing error', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);
            return false;
        }
    }

    /**
     * Format phone number for M-Pesa (254XXXXXXXXX)
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Handle different formats
        if (str_starts_with($phone, '254')) {
            return $phone;
        } elseif (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '254' . $phone;
        }
        
        return $phone;
    }

    /**
     * Store M-Pesa transaction for tracking
     */
    private function storeMpesaTransaction(Order $order, array $responseData): void
    {
        // You can create a separate MpesaTransaction model to track these
        Log::info('M-Pesa transaction initiated', [
            'order_code' => $order->order_code,
            'checkout_request_id' => $responseData['CheckoutRequestID'],
            'merchant_request_id' => $responseData['MerchantRequestID']
        ]);
    }

    /**
     * Parse callback metadata
     */
    private function parseCallbackMetadata(array $metadata): array
    {
        $data = [];
        
        foreach ($metadata as $item) {
            $name = $item['Name'] ?? '';
            $value = $item['Value'] ?? '';
            
            switch ($name) {
                case 'Amount':
                    $data['amount'] = $value;
                    break;
                case 'MpesaReceiptNumber':
                    $data['receipt_number'] = $value;
                    break;
                case 'TransactionDate':
                    $data['transaction_date'] = $value;
                    break;
                case 'PhoneNumber':
                    $data['phone_number'] = $value;
                    break;
            }
        }
        
        return $data;
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment(string $checkoutRequestId, array $transactionData): void
    {
        // Find the order and update payment status
        // You'll need to implement order lookup by checkout_request_id
        Log::info('M-Pesa payment successful', [
            'checkout_request_id' => $checkoutRequestId,
            'transaction_data' => $transactionData
        ]);
        
        // Update order status to confirmed
        // Send confirmation notifications
        // Generate receipt
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment(string $checkoutRequestId, int $resultCode): void
    {
        Log::warning('M-Pesa payment failed', [
            'checkout_request_id' => $checkoutRequestId,
            'result_code' => $resultCode
        ]);
        
        // Update order status accordingly
        // Notify customer of failed payment
    }

    /**
     * Validate M-Pesa configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        
        if (empty($this->consumerKey)) {
            $errors[] = 'M-Pesa Consumer Key is not configured';
        }
        
        if (empty($this->consumerSecret)) {
            $errors[] = 'M-Pesa Consumer Secret is not configured';
        }
        
        if (empty($this->shortcode)) {
            $errors[] = 'M-Pesa Shortcode is not configured';
        }
        
        if (empty($this->passkey)) {
            $errors[] = 'M-Pesa Passkey is not configured';
        }
        
        return $errors;
    }
}