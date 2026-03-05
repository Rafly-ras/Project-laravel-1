<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasMultiCurrency;

class Expense extends Model
{
    use HasMultiCurrency;

    protected $fillable = [
        'category_id',
        'amount',
        'description',
        'expense_date',
        'created_by',
        'currency_id',
        'exchange_rate',
        'base_amount',
        'department_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
