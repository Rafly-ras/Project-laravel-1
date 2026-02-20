<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialNumber;
use App\Traits\LogsActivity;

class Invoice extends Model
{
    use HasSequentialNumber, LogsActivity;

    protected $fillable = [
        'invoice_number',
        'sales_order_id',
        'due_date',
        'total_amount',
        'status',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'issued_at' => 'datetime',
        ];
    }

    public function getSequentialField(): string { return 'invoice_number'; }
    public function getSequentialPrefix(): string { return 'INV'; }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }
}
