<?php

namespace App\Services;

class TaxService
{
    private const DEFAULT_TAX_RATE = 0.16; // 16% VAT (Kenya standard rate)
    
    public function calculateTax(float $amount, float $taxRate = null): float
    {
        $rate = $taxRate ?? self::DEFAULT_TAX_RATE;
        return round($amount * $rate, 2);
    }

    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return round($subtotal, 2);
    }

    public function calculateOrderTotal(array $items, float $taxRate = null, float $discount = 0): array
    {
        $subtotal = $this->calculateSubtotal($items);
        $discountAmount = round($subtotal * ($discount / 100), 2);
        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $this->calculateTax($taxableAmount, $taxRate);
        $total = $taxableAmount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => round($total, 2)
        ];
    }

    public function getTaxBreakdown(float $total, float $taxRate = null): array
    {
        $rate = $taxRate ?? self::DEFAULT_TAX_RATE;
        $taxAmount = $total - ($total / (1 + $rate));
        $netAmount = $total - $taxAmount;

        return [
            'net_amount' => round($netAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'tax_rate' => $rate * 100,
            'total_amount' => round($total, 2)
        ];
    }

    public function getAvailableTaxRates(): array
    {
        return [
            0.00 => 'Zero Rated (0%) - Basic goods, exports',
            0.08 => 'Reduced Rate (8%) - Petroleum products',
            0.16 => 'Standard VAT (16%) - Most goods and services',
            0.25 => 'Excise Duty (25%) - Luxury items, alcohol, tobacco'
        ];
    }

    public function getKenyanTaxCategories(): array
    {
        return [
            'zero_rated' => [
                'rate' => 0.00,
                'description' => 'Zero-rated supplies (exports, basic food items)',
                'items' => ['Maize flour', 'Wheat flour', 'Rice', 'Sugar', 'Cooking oil', 'Milk', 'Bread']
            ],
            'exempt' => [
                'rate' => 0.00,
                'description' => 'Exempt supplies (financial services, education, medical)',
                'items' => ['Insurance', 'Banking services', 'Education fees', 'Medical services']
            ],
            'standard_vat' => [
                'rate' => 0.16,
                'description' => 'Standard VAT rate for most goods and services',
                'items' => ['Electronics', 'Clothing', 'Furniture', 'Restaurant services']
            ],
            'petroleum' => [
                'rate' => 0.08,
                'description' => 'Petroleum products',
                'items' => ['Petrol', 'Diesel', 'Kerosene', 'LPG']
            ],
            'excise_duty' => [
                'rate' => 0.25,
                'description' => 'Excise duty on specific goods',
                'items' => ['Alcohol', 'Tobacco', 'Motor vehicles', 'Mobile phones']
            ]
        ];
    }
}