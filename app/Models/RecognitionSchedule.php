<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecognitionSchedule extends Model
{
    protected $fillable = [
        'type',
        'source_type',
        'source_id',
        'total_amount',
        'currency_id',
        'exchange_rate',
        'start_date',
        'end_date',
        'periods',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function lines()
    {
        return $this->hasMany(RecognitionLine::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}
