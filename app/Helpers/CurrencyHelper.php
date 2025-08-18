<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount in FCFA currency (no decimals since FCFA has no subunit)
     */
    public static function formatFCFA(int $amount): string
    {
        return number_format($amount, 0, ',', ' ').' FCFA';
    }

    /**
     * Format amount with currency symbol based on locale
     */
    public static function format(int $amount): string
    {
        $currency = __('app.general.currency') ?? 'FCFA';

        return number_format($amount, 0, ',', ' ').' '.$currency;
    }

    /**
     * Get currency symbol
     */
    public static function symbol(): string
    {
        return __('app.general.currency') ?? 'FCFA';
    }

    /**
     * Parse amount from string (removing currency and formatting)
     * Returns integer since FCFA has no subunit
     */
    public static function parse(string $formattedAmount): int
    {
        // Extract all digits from the string
        $digits = preg_replace('/[^\d]/', '', $formattedAmount);

        // Convert to integer safely
        return $digits === '' ? 0 : intval($digits);
    }

    /**
     * Convert decimal amount to integer (for migration purposes)
     */
    public static function decimalToInteger(float $decimalAmount): int
    {
        return (int) round($decimalAmount);
    }

    /**
     * Convert user input to integer amount
     */
    public static function inputToInteger(string|int|float $input): int
    {
        if (is_string($input)) {
            return self::parse($input);
        }

        return (int) $input;
    }
}
