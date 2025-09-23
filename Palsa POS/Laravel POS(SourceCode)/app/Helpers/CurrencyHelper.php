<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount in Kenya Shillings
     */
    public static function formatKES(float $amount): string
    {
        return 'KES ' . number_format($amount, 2);
    }

    /**
     * Format amount with KES symbol
     */
    public static function formatKESSymbol(float $amount): string
    {
        return 'KSh ' . number_format($amount, 2);
    }

    /**
     * Format amount for display (short format)
     */
    public static function formatKESShort(float $amount): string
    {
        if ($amount >= 1000000) {
            return 'KSh ' . number_format($amount / 1000000, 1) . 'M';
        } elseif ($amount >= 1000) {
            return 'KSh ' . number_format($amount / 1000, 1) . 'K';
        }
        
        return 'KSh ' . number_format($amount, 2);
    }

    /**
     * Format amount for M-Pesa (no decimals for whole numbers)
     */
    public static function formatMpesa(float $amount): string
    {
        if ($amount == floor($amount)) {
            return 'KSh ' . number_format($amount, 0);
        }
        
        return 'KSh ' . number_format($amount, 2);
    }

    /**
     * Parse KES amount from string
     */
    public static function parseKES(string $amount): float
    {
        // Remove currency symbols and spaces
        $cleaned = preg_replace('/[KES|KSh|\s]/', '', $amount);
        $cleaned = str_replace(',', '', $cleaned);
        
        return (float) $cleaned;
    }

    /**
     * Get currency symbol
     */
    public static function getSymbol(): string
    {
        return 'KSh';
    }

    /**
     * Get currency code
     */
    public static function getCode(): string
    {
        return 'KES';
    }

    /**
     * Format for API responses
     */
    public static function formatForAPI(float $amount): array
    {
        return [
            'amount' => $amount,
            'formatted' => self::formatKESSymbol($amount),
            'currency' => 'KES',
            'symbol' => 'KSh'
        ];
    }

    /**
     * Convert to M-Pesa format (cents)
     */
    public static function toMpesaCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Convert from M-Pesa format (cents)
     */
    public static function fromMpesaCents(int $cents): float
    {
        return $cents / 100;
    }
}