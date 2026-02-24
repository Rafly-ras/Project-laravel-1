<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class CurrencyService
{
    /**
     * Get the base currency.
     */
    public function getBaseCurrency()
    {
        return Currency::where('is_base', true)->first();
    }

    /**
     * Get exchange rate for a currency relative to the base currency.
     */
    public function getRate($currencyId, $date = null)
    {
        $date = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $baseCurrency = $this->getBaseCurrency();

        if ($currencyId == $baseCurrency->id) {
            return 1.0;
        }

        // Try to find rate for specific date
        $rate = ExchangeRate::where('from_currency_id', $currencyId)
            ->where('to_currency_id', $baseCurrency->id)
            ->where('rate_date', '<=', $date)
            ->orderBy('rate_date', 'desc')
            ->first();

        return $rate ? (float)$rate->rate : 1.0;
    }

    /**
     * Convert an amount to the base currency.
     */
    public function convertToBase($amount, $currencyId, $date = null)
    {
        $rate = $this->getRate($currencyId, $date);
        return $amount * $rate;
    }

    /**
     * Convert an amount from one currency to another.
     */
    public function convert($amount, $fromCurrencyId, $toCurrencyId, $date = null)
    {
        if ($fromCurrencyId == $toCurrencyId) {
            return $amount;
        }

        $baseAmount = $this->convertToBase($amount, $fromCurrencyId, $date);
        
        $toRate = $this->getRate($toCurrencyId, $date);
        
        return $toRate > 0 ? $baseAmount / $toRate : $baseAmount;
    }

    /**
     * Format amount with currency symbol.
     */
    public function format($amount, $currencyId = null)
    {
        $currency = $currencyId ? Currency::find($currencyId) : $this->getBaseCurrency();
        
        if (!$currency) {
            return number_format($amount, 2);
        }

        return $currency->symbol . ' ' . number_format($amount, 2);
    }
}
