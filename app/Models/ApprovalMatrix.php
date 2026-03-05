<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalMatrix extends Model
{
    protected $fillable = ['document_type', 'min_amount', 'role_id', 'sequence'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
