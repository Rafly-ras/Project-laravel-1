<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountSnapshot extends Model
{
    protected $fillable = [
        'account_id',
        'accounting_period_id',
        'ending_balance',
        'debit_turnover',
        'credit_turnover',
        'base_ending_balance',
        'base_debit_turnover',
        'base_credit_turnover',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'accounting_period_id');
    }
}
