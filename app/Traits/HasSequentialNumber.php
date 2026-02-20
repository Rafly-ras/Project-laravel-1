<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasSequentialNumber
{
    public static function bootHasSequentialNumber()
    {
        static::creating(function ($model) {
            $field = $model->getSequentialField();
            $prefix = $model->getSequentialPrefix();
            
            $lastRecord = DB::table($model->getTable())
                ->orderBy('id', 'desc')
                ->first();
                
            $lastNumber = 0;
            if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->$field, $matches)) {
                $lastNumber = (int) $matches[1];
            }
            
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $model->$field = "{$prefix}-{$newNumber}";
        });
    }

    abstract public function getSequentialField(): string;
    abstract public function getSequentialPrefix(): string;
}
