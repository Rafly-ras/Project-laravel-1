<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSequentialNumber;
use App\Traits\LogsActivity;

class SalesOrder extends Model
{
    use HasSequentialNumber, LogsActivity;

    protected $fillable = [
        'sales_number',
        'request_order_id',
        'customer_name',
        'status',
        'total_amount',
        'gross_profit',
        'margin_percentage',
        'created_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
        ];
    }

    public function getSequentialField(): string { return 'sales_number'; }
    public function getSequentialPrefix(): string { return 'SO'; }

    public function requestOrder()
    {
        return $this->belongsTo(RequestOrder::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
