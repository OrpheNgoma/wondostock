<?php

use App\Helpers\CurrencyHelper;

if (! function_exists('format_fcfa')) {
    /**
     * Format amount in FCFA currency
     */
    function format_fcfa(int $amount): string
    {
        return CurrencyHelper::formatFCFA($amount);
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format amount with current locale currency
     */
    function format_currency(int $amount): string
    {
        return CurrencyHelper::format($amount);
    }
}

if (! function_exists('parse_currency')) {
    /**
     * Parse currency formatted string to integer
     */
    function parse_currency(string $formattedAmount): int
    {
        return CurrencyHelper::parse($formattedAmount);
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get current currency symbol
     */
    function currency_symbol(): string
    {
        return CurrencyHelper::symbol();
    }
}
