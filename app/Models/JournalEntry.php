<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use LogsActivity;

    protected $fillable = [
        'entry_date',
        'reference',
        'description',
        'currency_id',
        'exchange_rate',
        'accounting_period_id',
        'created_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'accounting_period_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if total debit equals total credit.
     */
    public function isBalanced(): bool
    {
        $debit = (float) $this->lines->sum('debit');
        $credit = (float) $this->lines->sum('credit');
        
        return round($debit, 2) === round($credit, 2);
    }
}
