<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            // For now, we'll log the notification
            // Later you can implement actual email sending
            Log::info('Order confirmation notification', [
                'order_code' => $order->order_code,
                'customer_email' => $order->user->email,
                'amount' => $order->totalPrice
            ]);

            // Simulate email sending
            $this->logNotification('email', $order->user->email, 'Order Confirmed', [
                'order_code' => $order->order_code,
                'amount' => $order->totalPrice
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendOrderStatusUpdate(Order $order, string $oldStatus): bool
    {
        try {
            $this->logNotification('email', $order->user->email, 'Order Status Updated', [
                'order_code' => $order->order_code,
                'old_status' => $oldStatus,
                'new_status' => $order->status_text
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order status update', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendLowStockAlert(array $products): bool
    {
        try {
            $admins = User::where('role', User::ROLE_ADMIN)
                ->orWhere('role', User::ROLE_SUPERADMIN)
                ->get();

            foreach ($admins as $admin) {
                $this->logNotification('email', $admin->email, 'Low Stock Alert', [
                    'products_count' => count($products),
                    'products' => $products
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send low stock alert', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendDailySalesReport(array $salesData): bool
    {
        try {
            $admins = User::where('role', User::ROLE_ADMIN)
                ->orWhere('role', User::ROLE_SUPERADMIN)
                ->get();

            foreach ($admins as $admin) {
                $this->logNotification('email', $admin->email, 'Daily Sales Report', $salesData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send daily sales report', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendWelcomeMessage(User $user): bool
    {
        try {
            $this->logNotification('email', $user->email, 'Welcome to Palsa POS', [
                'name' => $user->name,
                'role' => $user->role
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome message', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function logNotification(string $type, string $recipient, string $subject, array $data): void
    {
        Log::info("Notification sent", [
            'type' => $type,
            'recipient' => $recipient,
            'subject' => $subject,
            'data' => $data,
            'timestamp' => now()
        ]);
    }

    // Method to prepare for M-Pesa integration
    public function sendPaymentNotification(Order $order, array $paymentData): bool
    {
        try {
            $this->logNotification('sms', $order->user->phone ?? '', 'Payment Received', [
                'order_code' => $order->order_code,
                'amount' => $paymentData['amount'] ?? $order->totalPrice,
                'transaction_id' => $paymentData['transaction_id'] ?? 'N/A'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Placeholder for future SMS integration
    public function sendSMS(string $phoneNumber, string $message): bool
    {
        // This will be implemented when you integrate with SMS service
        Log::info('SMS notification', [
            'phone' => $phoneNumber,
            'message' => $message
        ]);

        return true;
    }
}