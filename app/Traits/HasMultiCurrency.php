<?php

namespace App\Traits;

use App\Models\Currency;
use App\Services\CurrencyService;

trait HasMultiCurrency
{
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Boot the trait to automatically set default currency if not provided.
     */
    protected static function bootHasMultiCurrency()
    {
        static::creating(function ($model) {
            $currencyService = app(CurrencyService::class);
            
            if (!$model->currency_id) {
                $baseCurrency = $currencyService->getBaseCurrency();
                $model->currency_id = $baseCurrency->id;
            }

            if (!$model->exchange_rate || $model->exchange_rate == 1) {
                $model->exchange_rate = $currencyService->getRate($model->currency_id, $model->created_at);
            }

            // amount field is expected to exist in the model
            $amountField = $model->getAmountField();
            if ($model->{$amountField} && (!$model->base_amount || $model->base_amount == 0)) {
                $model->base_amount = $model->{$amountField} * $model->exchange_rate;
            }

            // Optional: Handle gross profit if exists
            if (isset($model->gross_profit) && property_exists($model, 'base_gross_profit')) {
                 $model->base_gross_profit = $model->gross_profit * $model->exchange_rate;
            }
        });
    }

    /**
     * Default amount field name. Can be overridden in models.
     */
    public function getAmountField()
    {
        return 'amount';
    }

    /**
     * Get formatted original amount.
     */
    public function getFormattedAmountAttribute()
    {
        $currencyService = app(CurrencyService::class);
        return $currencyService->format($this->{$this->getAmountField()}, $this->currency_id);
    }

    /**
     * Get formatted base amount.
     */
    public function getFormattedBaseAmountAttribute()
    {
        $currencyService = app(CurrencyService::class);
        $baseCurrency = $currencyService->getBaseCurrency();
        return $currencyService->format($this->base_amount, $baseCurrency->id);
    }
}
