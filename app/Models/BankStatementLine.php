<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankStatementLine extends Model
{
    protected $fillable = [
        'bank_statement_id', 'transaction_date', 'description',
        'reference', 'debit', 'credit', 'running_balance', 'status',
    ];

    protected $casts = ['transaction_date' => 'date'];

    public function statement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class, 'bank_statement_id');
    }

    public function reconciliation(): HasOne
    {
        return $this->hasOne(BankReconciliation::class, 'statement_line_id');
    }

    public function getAmountAttribute(): float
    {
        return $this->credit - $this->debit;
    }
}
