<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialNumber;
use App\Traits\LogsActivity;

class Payment extends Model
{
    use HasSequentialNumber, LogsActivity;

    protected $fillable = [
        'invoice_id',
        'payment_number',
        'amount',
        'payment_method',
        'reference_number',
        'paid_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function getSequentialField(): string { return 'payment_number'; }
    public function getSequentialPrefix(): string { return 'PAY'; }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
