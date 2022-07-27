<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialClosingTime extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class);
    }
}
