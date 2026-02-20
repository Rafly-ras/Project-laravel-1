<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialNumber;
use App\Traits\LogsActivity;

class RequestOrder extends Model
{
    use HasSequentialNumber, LogsActivity;

    protected $fillable = [
        'request_number',
        'customer_name',
        'customer_email',
        'status',
        'total_amount',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function getSequentialField(): string { return 'request_number'; }
    public function getSequentialPrefix(): string { return 'RO'; }

    public function items()
    {
        return $this->hasMany(RequestOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class);
    }
}
