<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'location', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(ProductTransaction::class);
    }
}
