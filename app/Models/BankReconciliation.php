<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankReconciliation extends Model
{
    protected $fillable = [
        'statement_line_id', 'journal_entry_id', 'reconciled_by',
        'match_type', 'amount_matched', 'difference',
        'adjustment_journal_id', 'notes',
    ];

    public function statementLine(): BelongsTo
    {
        return $this->belongsTo(BankStatementLine::class, 'statement_line_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function adjJournal(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'adjustment_journal_id');
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
