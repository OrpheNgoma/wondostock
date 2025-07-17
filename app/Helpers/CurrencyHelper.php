<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount in FCFA currency
     */
    public static function formatFCFA(float|int $amount, int $decimals = 0): string
    {
        return number_format($amount, $decimals, ',', ' ') . ' FCFA';
    }

    /**
     * Format amount with currency symbol based on locale
     */
    public static function format(float|int $amount, int $decimals = 0): string
    {
        $currency = __('app.general.currency');
        return number_format($amount, $decimals, ',', ' ') . ' ' . $currency;
    }

    /**
     * Get currency symbol
     */
    public static function symbol(): string
    {
        return __('app.general.currency');
    }

    /**
     * Parse amount from string (removing currency and formatting)
     */
    public static function parse(string $formattedAmount): float
    {
        // Remove currency symbol and spaces
        $amount = str_replace([' FCFA', 'FCFA', ' '], '', $formattedAmount);
        // Replace comma with dot for decimal separator
        $amount = str_replace(',', '.', $amount);
        
        return (float) $amount;
    }
}