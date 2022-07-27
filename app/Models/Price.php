<?php

namespace App\Models;

use App\Luglocker\Builders\PriceQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $locker_id
 * @property float $range_start
 * @property float $range_end
 * @property string $range_type
 * @property float $price
 * @property string $verified
 * @property Locker $locker
 */

class Price extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locker_id',
        'range_start',
        'range_end',
        'price'
    ];

    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :PriceQueryBuilder
    {
        return new PriceQueryBuilder($query);
    }

    /** Locker
     * @return BelongsTo
     */

    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class);
    }
}
