<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('kes')) {
    /**
     * Format amount in Kenya Shillings
     *
     * @param float $amount
     * @return string
     */
    function kes($amount)
    {
        return CurrencyHelper::formatKESSymbol($amount);
    }
}

if (!function_exists('kes_short')) {
    /**
     * Format amount in Kenya Shillings (short format)
     *
     * @param float $amount
     * @return string
     */
    function kes_short($amount)
    {
        return CurrencyHelper::formatKESShort($amount);
    }
}

if (!function_exists('kes_mpesa')) {
    /**
     * Format amount for M-Pesa
     *
     * @param float $amount
     * @return string
     */
    function kes_mpesa($amount)
    {
        return CurrencyHelper::formatMpesa($amount);
    }
}
