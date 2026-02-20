<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'price',
        'cost_price',
        'stock',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(ProductTransaction::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)
                    ->withPivot('stock')
                    ->withTimestamps();
    }
}
