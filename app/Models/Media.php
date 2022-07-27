<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'related_id',
        'related_type',
        'url',
        'type',
        'main'
    ];

    /**
     * Get the parent related model
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
