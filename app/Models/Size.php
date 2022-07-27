<?php

namespace App\Models;

use App\Luglocker\Builders\SizeQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $business_id
 * @property string $name
 * @property string $width
 * @property string $height
 * @property string $length
 * @property string $desc
 * @property Locker $lockers
 * @property Business $business
 */

class Size extends Model
{
    use HasFactory;


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'desc' => 'array',
        'name' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'name',
        'width',
        'height',
        'length',
        'desc',
        'verified',
    ];


    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :SizeQueryBuilder
    {
        return new SizeQueryBuilder($query);
    }

    /** Locker
     * @return HasMany
     */

    public function lockers(): HasMany
    {
        return $this->hasMany(Locker::class);
    }


    /** Business
     * @return BelongsTo
     */

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
