<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecognitionLine extends Model
{
    protected $fillable = [
        'recognition_schedule_id',
        'scheduled_date',
        'amount',
        'base_amount',
        'journal_entry_id',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(RecognitionSchedule::class, 'recognition_schedule_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
