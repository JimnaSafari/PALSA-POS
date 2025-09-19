<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptService
{
    private TaxService $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    public function generateReceipt(string $orderCode): array
    {
        $orders = Order::with(['product', 'user'])
            ->where('order_code', $orderCode)
            ->get();

        if ($orders->isEmpty()) {
            throw new \Exception('Order not found');
        }

        $customer = $orders->first()->user;
        $items = $this->formatOrderItems($orders);
        $totals = $this->calculateTotals($orders);

        return [
            'order_code' => $orderCode,
            'date' => $orders->first()->created_at,
            'customer' => $customer,
            'items' => $items,
            'totals' => $totals,
            'receipt_number' => $this->generateReceiptNumber($orderCode)
        ];
    }

    public function generatePDF(string $orderCode): \Barryvdh\DomPDF\PDF
    {
        $receiptData = $this->generateReceipt($orderCode);
        
        $pdf = PDF::loadView('receipts.pdf', $receiptData);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }

    public function generateThermalReceipt(string $orderCode): string
    {
        $receiptData = $this->generateReceipt($orderCode);
        
        return View::make('receipts.thermal', $receiptData)->render();
    }

    private function formatOrderItems($orders): array
    {
        return $orders->map(function ($order) {
            return [
                'name' => $order->product->name,
                'quantity' => $order->count,
                'unit_price' => $order->product->price,
                'total_price' => $order->totalPrice,
                'sku' => $order->product->sku ?? 'N/A'
            ];
        })->toArray();
    }

    private function calculateTotals($orders): array
    {
        $subtotal = $orders->sum('totalPrice');
        $taxAmount = $orders->sum('tax_amount') ?: $this->taxService->calculateTax($subtotal);
        $discountAmount = $orders->sum('discount_amount') ?: 0;
        $total = $subtotal + $taxAmount - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'subtotal_formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($subtotal),
            'tax_amount' => $taxAmount,
            'tax_formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($taxAmount),
            'discount_amount' => $discountAmount,
            'discount_formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($discountAmount),
            'total' => $total,
            'total_formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($total),
            'items_count' => $orders->sum('count')
        ];
    }

    private function generateReceiptNumber(string $orderCode): string
    {
        return 'RCP-' . strtoupper($orderCode) . '-' . now()->format('Ymd');
    }

    public function emailReceipt(string $orderCode, string $email): bool
    {
        try {
            $receiptData = $this->generateReceipt($orderCode);
            $pdf = $this->generatePDF($orderCode);
            
            // Here you would implement email sending logic
            // For now, we'll just return true
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to email receipt: ' . $e->getMessage());
            return false;
        }
    }
}