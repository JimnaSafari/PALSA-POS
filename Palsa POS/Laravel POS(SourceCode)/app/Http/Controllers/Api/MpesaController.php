<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MpesaService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function __construct(
        private MpesaService $mpesaService,
        private NotificationService $notificationService
    ) {}

    /**
     * Initiate M-Pesa STK Push payment
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string|exists:orders,order_code',
            'phone_number' => 'required|string|regex:/^[0-9+\-\s]+$/',
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

            // Validate M-Pesa configuration
            $configErrors = $this->mpesaService->validateConfiguration();
            if (!empty($configErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa service not properly configured',
                    'errors' => $configErrors
                ], 500);
            }

            $result = $this->mpesaService->stkPush($order, $request->phone_number);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment request sent to your phone. Please enter your M-Pesa PIN.',
                    'data' => [
                        'checkout_request_id' => $result['checkout_request_id'],
                        'order_code' => $order->order_code,
                        'amount' => $order->total_with_tax
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('M-Pesa payment initiation error', [
                'order_code' => $request->order_code,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string',
        ]);

        try {
            $result = $this->mpesaService->queryStkStatus($request->checkout_request_id);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('M-Pesa status check error', [
                'checkout_request_id' => $request->checkout_request_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to check payment status'
            ], 500);
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function handleCallback(Request $request)
    {
        try {
            Log::info('M-Pesa callback received', $request->all());

            $callbackData = $request->all();
            $success = $this->mpesaService->handleCallback($callbackData);

            if ($success) {
                return response()->json([
                    'ResultCode' => 0,
                    'ResultDesc' => 'Success'
                ]);
            }

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Failed'
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa callback error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Internal Server Error'
            ]);
        }
    }

    /**
     * Handle M-Pesa timeout
     */
    public function handleTimeout(Request $request)
    {
        Log::info('M-Pesa timeout received', $request->all());

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Timeout received successfully'
        ]);
    }

    /**
     * Test M-Pesa configuration
     */
    public function testConfiguration()
    {
        try {
            $errors = $this->mpesaService->validateConfiguration();
            
            if (empty($errors)) {
                // Try to generate access token
                $token = $this->mpesaService->generateAccessToken();
                
                if ($token) {
                    return response()->json([
                        'success' => true,
                        'message' => 'M-Pesa configuration is valid and working',
                        'environment' => config('mpesa.environment')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'M-Pesa credentials are invalid'
                    ], 400);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'M-Pesa configuration errors found',
                'errors' => $errors
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'M-Pesa service error: ' . $e->getMessage()
            ], 500);
        }
    }
}